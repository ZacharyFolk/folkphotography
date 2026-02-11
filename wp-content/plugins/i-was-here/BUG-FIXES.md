# i-was-here Plugin - Bug Fixes

## 🔧 Version History

### Version 0.4.0 - February 2026

**Bug Fixes:** wp_die() Usage & Leaflet Version Conflicts

#### ❌ Issue 1: Incorrect wp_die() Usage

**File:** `includes/debug-tools.php`

**Problem:**
```php
// BEFORE (INCORRECT):
if (! current_user_can('manage_options')) {
    wp_die('Forbidden', 403);
}
```

**Why It's Wrong:**
- `wp_die()`'s second parameter is the **page title**, not HTTP status code
- This sets the title to "403" but may return HTTP 200 response
- Incorrect HTTP semantics - search engines/browsers see 200 (success) instead of 403 (forbidden)
- Security tools can't detect unauthorized access attempts

**Solution:**
```php
// AFTER (CORRECT):
if (! current_user_can('manage_options')) {
    wp_die(
        __('You do not have sufficient permissions to access this page.', 'i-was-here'),
        __('Forbidden', 'i-was-here'),
        ['response' => 403]
    );
}
```

**What Changed:**
1. ✅ First parameter: User-friendly error message
2. ✅ Second parameter: Page title ("Forbidden")
3. ✅ Third parameter: Array with `['response' => 403]` for correct HTTP status
4. ✅ Internationalized with `__()`

**Impact:**
- ✅ Correct HTTP 403 response sent
- ✅ Better error message for users
- ✅ Security tools can detect unauthorized access
- ✅ Search engines handle correctly

---

#### ❌ Issue 2: Leaflet Version Conflicts

**Files:** `admin/meta-box-location.php`, `frontend/shortcode-world-map.php`

**Problem:**
```php
// BEFORE (PROBLEMATIC):
wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet/dist/leaflet.css');
wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet/dist/leaflet.js', [], null, true);
```

**Why It's Wrong:**
1. ❌ No pinned version - gets latest (could break with updates)
2. ❌ Different handle than theme (`leaflet-js` vs `leaflet`)
3. ❌ Can cause duplicate downloads if theme already loads Leaflet
4. ❌ Potential version conflicts (theme: 1.9.4, plugin: 1.9.5)
5. ❌ Increases page weight (Leaflet loaded twice)

**Real-World Impact:**
- Theme loads Leaflet 1.9.4 (handle: `leaflet`)
- Plugin loads Leaflet 1.9.x (handle: `leaflet-js`)
- Result: 2 copies of Leaflet.js (~150KB each) = 300KB wasted
- Potential: Version conflicts, map initialization issues

**Solution:**
```php
// AFTER (CORRECT):
// Use theme's Leaflet if available, otherwise enqueue our own
if (!wp_script_is('leaflet', 'registered')) {
    // Enqueue pinned version if theme doesn't provide Leaflet
    wp_enqueue_style(
        'leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
        [],
        '1.9.4'
    );
    wp_enqueue_script(
        'leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
        [],
        '1.9.4',
        true
    );
} else {
    // Theme provides Leaflet, just ensure it's enqueued
    wp_enqueue_style('leaflet');
    wp_enqueue_script('leaflet');
}

// Enqueue our map script (depends on leaflet)
wp_enqueue_script(
    'iwh-admin-map',
    plugin_dir_url(__FILE__) . 'js/admin-map.js',
    ['leaflet', 'jquery'], // Use 'leaflet' handle (same as theme)
    '0.1',
    true
);
```

**What Changed:**
1. ✅ Checks if Leaflet already registered (`wp_script_is('leaflet', 'registered')`)
2. ✅ Uses same handle as theme (`'leaflet'` instead of `'leaflet-js'`)
3. ✅ Pinned to version 1.9.4 (same as theme)
4. ✅ Reuses theme's Leaflet if available
5. ✅ Falls back to own Leaflet if theme doesn't provide it

**Impact:**
- ✅ No duplicate Leaflet downloads (saves ~150KB)
- ✅ No version conflicts
- ✅ Works with or without theme
- ✅ Predictable behavior (pinned version)
- ✅ Better performance

---

#### 📋 Files Modified

**Version 0.4.0:**
1. `includes/debug-tools.php` - Fixed wp_die() usage
2. `admin/meta-box-location.php` - Fixed Leaflet enqueue
3. `frontend/shortcode-world-map.php` - Fixed Leaflet enqueue
4. `i-was-here.php` - Updated version to 0.4.0
5. `BUG-FIXES.md` - This file

---

#### 🧪 Testing

**Test 1: wp_die() Returns Correct Status**

1. Log out of WordPress
2. Navigate to: `/wp-admin/admin-post.php?action=iwh_rescan_exif`
3. Check browser developer tools → Network tab
4. Should see: **403 Forbidden** (not 200 OK)
5. Page should show: "You do not have sufficient permissions"

**Test 2: No Duplicate Leaflet**

1. Enable theme (folkphotography)
2. Add Location Map widget to page
3. View page source (Ctrl+U)
4. Search for "leaflet" (Ctrl+F)
5. Should find:
   - ✅ One `leaflet.css` link
   - ✅ One `leaflet.js` script
   - ❌ NOT: `leaflet-css` or `leaflet-js` handles

**Test 3: Plugin Works Without Theme**

1. Switch to a different theme (Twenty Twenty-Four)
2. View page with Location Map widget
3. Map should still work
4. Source should show Leaflet 1.9.4 loaded by plugin

---

### Version 0.3.0 - February 2026

**Critical Bug Fix:** EXIF Rescan Tool Not Extracting Data

### Version 0.2.0 - February 2026

**Critical Security Fix:** CSRF Vulnerability in Attachment Metadata

---

## Version 0.3.0 - EXIF Rescan Fix

### ❌ Problem

**File:** `includes/debug-tools.php`

**Issue:** The rescan tool was not actually extracting EXIF data correctly. It was trying to access keys directly on the raw EXIF array, but EXIF data is structured with sections like `['EXIF' => ..., 'GPS' => ..., 'IFD0' => ...]`.

**Broken Code:**
```php
// BEFORE (BROKEN):
$exif = IWH_Exif_Reader::read($file);

if (is_array($exif)) {
    // These keys don't exist at top level! ❌
    if (isset($exif['iso'])) {
        update_post_meta($a->ID, '_iwh_iso', $exif['iso']);
    }
    
    if (isset($exif['lat'])) {
        update_post_meta($a->ID, '_iwh_lat', $exif['lat']);
    }
}
```

**Why It Failed:**
- `IWH_Exif_Reader::read()` returns raw EXIF array with sections
- Keys like `$exif['iso']` don't exist at top level
- Keys are nested like `$exif['EXIF']['ISOSpeedRatings']`
- GPS data is in `$exif['GPS']` section
- Camera data needs special extraction method

**Result:**
- ✅ Upload worked (attachment-hooks.php uses correct method)
- ❌ Rescan didn't extract camera data
- ❌ Rescan didn't extract GPS coordinates
- ❌ No error shown, but data not saved

---

### ✅ Solution

**Fixed Code:**
```php
// AFTER (FIXED):
$exif = IWH_Exif_Reader::read($file);

if (is_array($exif)) {
    update_post_meta($a->ID, '_iwh_has_exif', 1);
    
    // ✅ Use proper extraction method for camera data
    $camera_data = IWH_Exif_Reader::extract_camera_data($exif);
    if ($camera_data) {
        foreach ($camera_data as $key => $value) {
            update_post_meta($a->ID, '_iwh_' . $key, $value);
        }
    }
    
    // ✅ Use proper GPS extraction logic
    if (isset($exif['GPS'])) {
        $this->process_gps_data($a->ID, $exif['GPS']);
    }
}
```

**What Changed:**

1. **Camera Data Extraction** - Now uses `IWH_Exif_Reader::extract_camera_data()`
   - Correctly extracts ISO, aperture, shutter speed, focal length
   - Properly handles camera make/model
   - Extracts lens information
   - Handles date/time taken

2. **GPS Data Extraction** - Now uses same logic as attachment-hooks.php
   - Extracts GPS coordinates from `$exif['GPS']` section
   - Converts DMS (degrees, minutes, seconds) to decimal
   - Applies direction (N/S/E/W)
   - Saves to `_iwh_lat` and `_iwh_lng` meta keys

3. **Code Reuse** - Added helper methods to debug-tools.php
   - `process_gps_data()` - Same logic as attachment-hooks.php
   - `get_gps_coordinate()` - Extracts and converts GPS coordinates
   - `gps_to_num()` - Converts fractional GPS values

---

### 📊 Impact

**Before Fix:**
- ❌ Rescan tool: Does nothing (data not extracted)
- ❌ Camera stats widget: Empty (no data)
- ❌ Location map widget: No pins (no GPS)
- ✅ Upload: Works (uses correct method)

**After Fix:**
- ✅ Rescan tool: Extracts all EXIF data
- ✅ Camera stats widget: Shows data after rescan
- ✅ Location map widget: Shows pins after rescan
- ✅ Upload: Still works (no changes)

---

### 🧪 Testing

**How to Test:**

1. **Upload an image without rescan:**
   - Upload image with EXIF data
   - Check Media Library → Edit image
   - Should see camera data and GPS (if present)
   - ✅ This always worked

2. **Test rescan on old images:**
   - Go to **Tools → i-was-here**
   - Click "Rescan EXIF for existing attachments"
   - Check Media Library → Edit an old image
   - Should now see camera data and GPS
   - ✅ This is what's fixed

3. **Verify camera stats widget:**
   - Before rescan: Empty or incomplete
   - Run rescan
   - Refresh page with camera stats widget
   - Should show: Total photos, favorite camera, lens, settings
   - ✅ Now works

4. **Verify location map widget:**
   - Before rescan: No pins or few pins
   - Run rescan
   - Refresh page with location map
   - Should show pins for all GPS photos
   - ✅ Now works

---

### 🔍 Technical Details

**EXIF Data Structure:**

```php
// What IWH_Exif_Reader::read() returns:
$exif = [
    'IFD0' => [
        'Make' => 'Canon',
        'Model' => 'Canon EOS R5',
    ],
    'EXIF' => [
        'ISOSpeedRatings' => 400,
        'FNumber' => '2.8',
        'ExposureTime' => '1/250',
        'FocalLength' => '70/1',
        'LensModel' => 'RF 24-70mm F2.8 L IS USM',
        'DateTimeOriginal' => '2024:01:15 14:30:22',
    ],
    'GPS' => [
        'GPSLatitude' => ['47/1', '36/1', '22/1'],
        'GPSLatitudeRef' => 'N',
        'GPSLongitude' => ['122/1', '19/1', '55/1'],
        'GPSLongitudeRef' => 'W',
    ],
];
```

**Correct Extraction:**

```php
// Camera data extraction:
$camera_data = IWH_Exif_Reader::extract_camera_data($exif);
// Returns:
[
    'iso' => 400,
    'aperture' => 2.8,
    'shutter_speed' => '1/250',
    'focal_length' => 70,
    'camera_make' => 'Canon',
    'camera_model' => 'Canon EOS R5',
    'lens' => 'RF 24-70mm F2.8 L IS USM',
    'date_taken' => '2024:01:15 14:30:22',
]

// GPS extraction:
process_gps_data($attachment_id, $exif['GPS']);
// Converts: 47°36'22"N, 122°19'55"W
// To: 47.6061, -122.3319 (decimal)
// Saves to: _iwh_lat, _iwh_lng
```

---

### 📋 Code Comparison

**Upload Hook (attachment-hooks.php) - Always Worked:**
```php
public function on_upload($attachment_id) {
    $exif = IWH_Exif_Reader::read($file);
    
    if ($exif) {
        // ✅ Correct: Uses extract_camera_data()
        $camera_data = IWH_Exif_Reader::extract_camera_data($exif);
        if ($camera_data) {
            foreach ($camera_data as $key => $value) {
                update_post_meta($attachment_id, '_iwh_' . $key, $value);
            }
        }
        
        // ✅ Correct: Processes GPS section
        if (isset($exif['GPS'])) {
            $this->process_gps_data($attachment_id, $exif['GPS']);
        }
    }
}
```

**Rescan Tool (debug-tools.php) - Fixed:**
```php
public function rescan() {
    foreach ($attachments as $a) {
        $exif = IWH_Exif_Reader::read($file);
        
        if (is_array($exif)) {
            // ✅ NOW FIXED: Uses extract_camera_data()
            $camera_data = IWH_Exif_Reader::extract_camera_data($exif);
            if ($camera_data) {
                foreach ($camera_data as $key => $value) {
                    update_post_meta($a->ID, '_iwh_' . $key, $value);
                }
            }
            
            // ✅ NOW FIXED: Processes GPS section
            if (isset($exif['GPS'])) {
                $this->process_gps_data($a->ID, $exif['GPS']);
            }
        }
    }
}
```

---

### 🎯 What Users Should Do

**If you uploaded images before this fix:**

1. **Run the rescan tool:**
   - Go to **Tools → i-was-here**
   - Click "Rescan EXIF for existing attachments"
   - Wait for completion (processes 50 images at a time)

2. **Check the results:**
   - Media Library → Edit any image
   - Should now see all camera data
   - Should see GPS coordinates (if photo has GPS)

3. **Verify widgets:**
   - Camera Stats widget should now show data
   - Location Map should now show all pins
   - All features should work

**For new uploads:**
- No action needed
- EXIF extraction works automatically on upload
- This fix only affects the rescan tool

---

## Version 0.2.0 - Security Fix

### 🔒 CSRF Vulnerability

**File:** `admin/meta-box-location.php`

**Issue:** Attachment metadata saved without nonce verification or permission checks.

**Solution:** Added comprehensive security:
- ✅ Nonce verification
- ✅ Permission checks  
- ✅ Autosave protection
- ✅ Data validation
- ✅ GPS coordinate range validation

**See:** SECURITY-FIX.md for complete details

---

## 📚 Summary

### Version History:

| Version | Date | Issue | Status |
|---------|------|-------|--------|
| 0.1.0 | Initial | N/A | Original release |
| 0.2.0 | Feb 2026 | CSRF vulnerability | ✅ Fixed |
| 0.3.0 | Feb 2026 | Rescan not extracting data | ✅ Fixed |
| 0.4.0 | Feb 2026 | wp_die() usage & Leaflet conflicts | ✅ Fixed |

### Current Status:

**Plugin Version:** 0.4.0  
**Theme Version:** 1.1.1 (folkphotography)  
**Issues:** 0  
**Production Ready:** ✅ Yes

---

**Last Updated:** February 2026  
**Status:** All issues resolved
