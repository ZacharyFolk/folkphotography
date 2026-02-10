# Security Fix - i-was-here Plugin

## 🔒 Critical Security Vulnerability Fixed

**Date:** February 2026  
**Plugin:** i-was-here  
**File:** `admin/meta-box-location.php`  
**Severity:** High (CSRF vulnerability)

---

## ⚠️ The Vulnerability

### What Was Wrong?

The attachment meta save function had **no security checks**:

```php
// BEFORE (VULNERABLE):
add_action('edit_attachment', function ($post_id) {
    if (isset($_POST['_iwh_lat'])) 
        update_post_meta($post_id, '_iwh_lat', sanitize_text_field($_POST['_iwh_lat']));
    if (isset($_POST['_iwh_lng'])) 
        update_post_meta($post_id, '_iwh_lng', sanitize_text_field($_POST['_iwh_lng']));
    if (isset($_POST['_iwh_place_name'])) 
        update_post_meta($post_id, '_iwh_place_name', sanitize_text_field($_POST['_iwh_place_name']));
});
```

### Security Issues:

1. **No Nonce Verification** - Vulnerable to CSRF (Cross-Site Request Forgery) attacks
2. **No Permission Check** - Anyone could update attachment metadata
3. **No Autosave Check** - Could save during autosave operations
4. **Weak Validation** - Only sanitized, didn't validate data types or ranges

### Attack Scenario:

An attacker could craft a malicious form that:
- Submits to your admin while you're logged in
- Updates GPS coordinates on your images
- Changes location data without your knowledge
- No permission check = any logged-in user could do it

---

## ✅ The Fix

### What Changed?

Added comprehensive security checks:

```php
// AFTER (SECURE):
add_action('edit_attachment', function ($post_id) {
    // 1. Verify nonce (CSRF protection)
    if (!isset($_POST['iwh_location_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['iwh_location_meta_box_nonce'], 'iwh_location_meta_box')) {
        return;
    }
    
    // 2. Check if autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 3. Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // 4. Validate and save with proper data validation
    if (isset($_POST['_iwh_lat'])) {
        $lat = sanitize_text_field($_POST['_iwh_lat']);
        // Validate latitude range (-90 to 90)
        if (is_numeric($lat) && $lat >= -90 && $lat <= 90) {
            update_post_meta($post_id, '_iwh_lat', $lat);
        } elseif (empty($lat)) {
            delete_post_meta($post_id, '_iwh_lat');
        }
    }
    
    // Similar validation for longitude and place name...
});
```

### Security Layers Added:

1. **Nonce Field** - Added to meta box HTML
   ```php
   wp_nonce_field('iwh_location_meta_box', 'iwh_location_meta_box_nonce');
   ```

2. **Nonce Verification** - Prevents CSRF attacks
   ```php
   wp_verify_nonce($_POST['iwh_location_meta_box_nonce'], 'iwh_location_meta_box')
   ```

3. **Autosave Check** - Prevents unwanted saves during autosave
   ```php
   if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
   ```

4. **Permission Check** - Only users who can edit the post can update metadata
   ```php
   current_user_can('edit_post', $post_id)
   ```

5. **Data Validation** - Validates latitude/longitude ranges
   ```php
   // Latitude: -90 to 90
   if (is_numeric($lat) && $lat >= -90 && $lat <= 90)
   
   // Longitude: -180 to 180
   if (is_numeric($lng) && $lng >= -180 && $lng <= 180)
   ```

6. **Empty Value Handling** - Properly deletes meta when empty
   ```php
   elseif (empty($lat)) {
       delete_post_meta($post_id, '_iwh_lat');
   }
   ```

---

## 🔍 How It Works

### 1. Meta Box Renders (Display Phase)

When WordPress shows the attachment edit screen:

```php
function iwh_location_meta_box_callback($post) {
    // Add nonce field (hidden input)
    wp_nonce_field('iwh_location_meta_box', 'iwh_location_meta_box_nonce');
    
    // Display form fields...
}
```

**Output HTML:**
```html
<input type="hidden" 
       name="iwh_location_meta_box_nonce" 
       value="abc123def456..." />
```

### 2. Form Submits (Save Phase)

When user clicks "Update" on attachment:

```php
add_action('edit_attachment', function ($post_id) {
    // Step 1: Check nonce exists
    if (!isset($_POST['iwh_location_meta_box_nonce'])) {
        return; // No nonce = not our form
    }
    
    // Step 2: Verify nonce is valid
    if (!wp_verify_nonce($_POST['iwh_location_meta_box_nonce'], 
                         'iwh_location_meta_box')) {
        return; // Invalid nonce = possible attack
    }
    
    // Step 3: Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return; // Don't save during autosave
    }
    
    // Step 4: Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return; // User can't edit = denied
    }
    
    // Step 5: Validate and save data
    // Only reached if all checks pass
});
```

---

## 📊 Security Comparison

| Aspect | Before (Vulnerable) | After (Secure) |
|--------|---------------------|----------------|
| CSRF Protection | ❌ None | ✅ Nonce verification |
| Permission Check | ❌ None | ✅ `current_user_can()` |
| Autosave Handling | ❌ None | ✅ Checked and blocked |
| Data Validation | ⚠️ Sanitize only | ✅ Validate + sanitize |
| Range Validation | ❌ None | ✅ GPS coordinate ranges |
| Empty Value Handling | ⚠️ Saves empty strings | ✅ Deletes meta properly |

---

## 🧪 Testing the Fix

### Manual Testing:

1. **Normal Edit (Should Work):**
   - Go to Media Library
   - Edit an image
   - Change GPS coordinates
   - Click Update
   - ✅ Coordinates should save

2. **Without Nonce (Should Fail):**
   - Use browser dev tools
   - Remove nonce field from HTML
   - Try to save
   - ✅ Should NOT save (security working)

3. **Wrong Permissions (Should Fail):**
   - Login as Subscriber role
   - Try to edit attachment
   - ✅ Should not be able to edit metadata

4. **Invalid Data (Should Reject):**
   - Enter latitude: 999 (invalid)
   - Click Update
   - ✅ Should NOT save (validation working)

### Automated Testing:

```php
// Test nonce verification
function test_iwh_nonce_security() {
    $post_id = 123; // Test attachment ID
    
    // Test 1: No nonce
    $_POST = ['_iwh_lat' => '47.6062'];
    do_action('edit_attachment', $post_id);
    assert(get_post_meta($post_id, '_iwh_lat', true) !== '47.6062');
    
    // Test 2: Invalid nonce
    $_POST['iwh_location_meta_box_nonce'] = 'fake_nonce';
    do_action('edit_attachment', $post_id);
    assert(get_post_meta($post_id, '_iwh_lat', true) !== '47.6062');
    
    // Test 3: Valid nonce
    $_POST['iwh_location_meta_box_nonce'] = wp_create_nonce('iwh_location_meta_box');
    do_action('edit_attachment', $post_id);
    assert(get_post_meta($post_id, '_iwh_lat', true) === '47.6062');
}
```

---

## 🛡️ Best Practices Followed

### WordPress Security Standards:

1. ✅ **Nonce Fields** - Every form has CSRF protection
2. ✅ **Capability Checks** - Permission verification
3. ✅ **Sanitization** - All input sanitized
4. ✅ **Validation** - Data type and range validation
5. ✅ **Escape Output** - All output escaped (already done)

### OWASP Top 10 Compliance:

1. ✅ **A01:2021 – Broken Access Control** - Permission checks added
2. ✅ **A03:2021 – Injection** - Sanitization and validation
3. ✅ **A07:2021 – Identification and Authentication Failures** - Nonce verification

---

## 📝 Code Review Checklist

For future meta box development:

- [ ] Add nonce field in meta box callback
- [ ] Verify nonce in save function
- [ ] Check for autosave
- [ ] Verify user capabilities
- [ ] Sanitize all input
- [ ] Validate data types and ranges
- [ ] Handle empty values properly
- [ ] Escape all output
- [ ] Test with different user roles
- [ ] Test CSRF scenarios

---

## 🔄 Migration Notes

### Existing Data:

- ✅ All existing metadata remains unchanged
- ✅ No database changes required
- ✅ Backward compatible

### User Impact:

- ✅ No visible changes for users
- ✅ Same functionality, more secure
- ✅ No retraining needed

### Plugin Updates:

If updating from previous version:
1. Deactivate plugin
2. Update files
3. Reactivate plugin
4. Test editing one attachment
5. Verify GPS data saves correctly

---

## 📚 References

### WordPress Documentation:

- [Nonces](https://developer.wordpress.org/apis/security/nonces/)
- [Data Validation](https://developer.wordpress.org/apis/security/data-validation/)
- [Sanitizing](https://developer.wordpress.org/apis/security/sanitizing/)
- [Escaping](https://developer.wordpress.org/apis/security/escaping/)

### Security Resources:

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WordPress Security Whitepaper](https://wordpress.org/about/security/)
- [Plugin Security Handbook](https://developer.wordpress.org/plugins/security/)

---

## ✅ Fix Verification

**Status:** ✅ **FIXED**

**Changed Files:**
- `wp-content/plugins/i-was-here/admin/meta-box-location.php`

**Security Improvements:**
1. ✅ CSRF protection implemented
2. ✅ Permission checks added
3. ✅ Autosave handling added
4. ✅ Data validation improved
5. ✅ Empty value handling fixed

**Testing Status:**
- ✅ Manual testing completed
- ✅ No functionality broken
- ✅ Security checks working
- ✅ Ready for production

---

**Last Updated:** February 2026  
**Fix Applied:** Version 0.2.0  
**Security Level:** ✅ Secure
