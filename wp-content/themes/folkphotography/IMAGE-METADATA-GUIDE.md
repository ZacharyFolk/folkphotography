# Image Metadata Preservation Guide

## 🚨 The Problem

Your images are being stripped of EXIF data (camera info, GPS, settings) during export. This breaks:
- Location map widget (needs GPS)
- Camera stats widget (needs EXIF)
- Photo information display
- Automatic date/location organization

**This guide fixes that.**

---

## 🎯 Quick Fix Summary

### For DxO PhotoLab Users:
✅ Export → File Options → **Check "Keep EXIF data"**  
✅ Export → GPS → **Check "Preserve GPS data"**

### For Photoshop Users:
✅ Save for Web → Metadata → **Select "All"** or "Copyright and Contact Info"  
✅ Regular Save → File Info → **Verify data is present**

---

## 📸 Understanding EXIF Metadata

### What Gets Stripped?
When you process and export images, these can be lost:
- **Camera**: Make, model, serial number
- **Lens**: Lens model, focal length
- **Settings**: Aperture, shutter speed, ISO
- **GPS**: Latitude, longitude, altitude
- **Date/Time**: When photo was taken
- **Copyright**: Your name, copyright info

### Why Does This Happen?
- Export settings default to "strip metadata" for file size
- Privacy protection (removes GPS from web images)
- "Save for Web" feature removes everything by default
- File format conversions (TIFF → JPEG)

---

## 🛠️ Preserving EXIF in DxO PhotoLab

### Export Settings (CRITICAL)

1. **Open Export Dialog:**
   - File → Export to Disk
   - Or: Right-click image → Export

2. **File Options Tab:**
   ```
   ✅ Keep EXIF data
   ✅ Keep IPTC data
   ✅ Preserve GPS coordinates
   ✅ Embed color profile
   ```

3. **GPS Settings:**
   - Click "GPS" tab
   - ✅ Check "Preserve GPS data"
   - ✅ Check "Write GPS to exported files"

4. **Metadata Options:**
   - Click "Metadata" tab
   - Select "Keep all metadata"
   - DO NOT select "Strip all metadata"

### DxO PhotoLab Best Practices

**Create an Export Preset:**
1. Set all metadata options correctly (above)
2. Click "Save Preset"
3. Name it: "Web Export - Keep Metadata"
4. Use this preset for ALL exports

**Verify After Export:**
1. Right-click exported file
2. Properties → Details tab (Windows)
3. Check that Camera, GPS, etc. are present

### DxO Specific Issues

**Problem:** GPS removed even with settings checked  
**Solution:** 
- DxO PhotoLab → Preferences → Metadata
- Uncheck "Remove GPS data for privacy"

**Problem:** Some EXIF missing after export  
**Solution:**
- Use JPEG format (not TIFF then convert)
- Export quality: 95+ maintains metadata better
- Don't use "Save for Web" presets

---

## 🎨 Preserving EXIF in Adobe Photoshop

### Save vs. Save for Web

**For WordPress Uploads, Use "Save As" NOT "Save for Web":**

#### Save As (Preserves All Metadata):
1. File → Save As
2. Format: JPEG
3. ✅ Check "Embed Color Profile"
4. Click Save
5. Quality: 10-12 (High)
6. ✅ Baseline ("Standard")

#### Save for Web (Use Carefully):
1. File → Export → Save for Web (Legacy)
2. Bottom-right: **Metadata dropdown**
3. Select: **"All"** (not "None")
4. Alternative: Select "Copyright and Contact Info"

### Metadata Panel Check

**Before Saving ANY File:**
1. File → File Info (Alt+Ctrl+Shift+I / Alt+Cmd+Shift+I)
2. Verify these tabs have data:
   - **Basic**: Copyright, author
   - **Camera Data**: Camera, lens, settings (read-only)
   - **GPS**: Latitude, longitude
   - **IPTC**: Keywords, description

### Photoshop Best Practices

**Create an Action:**
```
1. Window → Actions
2. Create New Action: "Save for Web - Keep Metadata"
3. Record:
   - File → Export → Save for Web
   - Set Metadata to "All"
   - Set quality to 85
   - Stop recording
4. Use F-key for quick access
```

**Batch Processing:**
1. File → Automate → Batch
2. Select your action
3. ✅ Override "Save As" commands
4. Destination: Folder
5. Process entire folder

### Photoshop Specific Issues

**Problem:** GPS stripped even with "All" metadata  
**Solution:**
- Don't use "Save for Web" - use "Save As" instead
- If must use Save for Web: File → Scripts → Export Layers to Files

**Problem:** Camera info missing  
**Solution:**
- Original file must have EXIF
- Don't crop/resize in Camera Raw without clicking "Done" first
- Check File → File Info to verify before save

---

## 🔍 Verification Tools

### Windows Built-in:
1. Right-click image file
2. Properties → Details tab
3. Check "Camera" and "GPS" sections

### Mac Built-in:
1. Select image in Finder
2. Cmd+I (Get Info)
3. Click "More Info" section

### ExifTool (Advanced):
```bash
# Install ExifTool (free)
# Then run:
exiftool image.jpg

# Check specific fields:
exiftool -GPS* -Camera* image.jpg
```

### Online Tools:
- https://exif.tools/ - Upload and view all EXIF
- https://jimpl.com/ - EXIF viewer
- https://www.get-metadata.com/ - Quick check

---

## 📋 Export Settings Checklist

### Before Every Export Session:

**DxO PhotoLab:**
- [ ] Export preset: "Keep Metadata" loaded
- [ ] GPS tab: "Preserve GPS" checked
- [ ] File options: "Keep EXIF" checked
- [ ] Metadata: "Keep all" selected

**Photoshop:**
- [ ] Using "Save As" (not Save for Web)
- [ ] File Info shows camera data
- [ ] GPS coordinates present
- [ ] Metadata dropdown: "All" selected

**After Export:**
- [ ] Test one file first
- [ ] Right-click → Properties → Check Details
- [ ] Upload test to WordPress
- [ ] Check Media Library shows camera info
- [ ] Verify map widget shows location

---

## 🐛 Troubleshooting Missing Metadata

### GPS Data Missing

**Check Original File:**
1. Does original have GPS? (Right-click → Properties)
2. If not, GPS can't be preserved

**DxO Specific:**
1. Edit → Preferences → Metadata
2. Uncheck any "Remove" or "Strip" options
3. Re-export

**Photoshop Specific:**
1. Camera Raw settings might strip GPS
2. Use File → Save As instead of Export

### Camera Info Stripped

**Check Original:**
1. Open original in ExifTool or Properties
2. Verify Camera Make/Model exists

**Common Causes:**
1. Screenshot instead of camera photo (no EXIF)
2. Downloaded from web (often stripped)
3. Previously edited and stripped

**Fix:**
1. Always work from camera original
2. Keep RAW files as backup
3. First export from RAW should preserve everything

### Partial EXIF Loss

**Some fields preserved, others missing:**

**Likely causes:**
- JPEG quality too low (use 90+)
- File format conversion
- Multiple edits/exports

**Solution:**
1. Export directly from RAW → JPEG (one step)
2. Don't re-save JPEG multiple times
3. Use highest quality settings

---

## 💾 Workflow for Scanned Images

### Film/Print Scans (No Original EXIF)

You can **add metadata manually**:

**In Photoshop:**
1. File → File Info
2. **Camera Data tab:**
   - Can't add camera/lens (read-only)
3. **GPS tab:**
   - Latitude: [your location]
   - Longitude: [your location]
4. **IPTC tab:**
   - Keywords: "film", "scanned", "holga", etc.
   - Date Created: When photo was taken
   - Location: City, state, country

**In WordPress Media Library:**
1. Upload image
2. Edit attachment
3. Add to "Location Data" meta box (i-was-here plugin)
4. Enter GPS coordinates manually

**Batch Add Metadata:**
1. ExifTool (command line):
   ```bash
   # Add GPS to all JPEGs in folder:
   exiftool -GPS* -GPSLatitude=47.6062 -GPSLongitude=-122.3321 *.jpg
   
   # Add keywords:
   exiftool -keywords="film,holga,scanned" *.jpg
   ```

2. Adobe Bridge:
   - Select multiple files
   - Edit → Find → Replace
   - Add metadata in bulk

---

## 🎯 Best Practices Summary

### The Golden Rules:

1. **Always export from RAW** (DNG, CR2, NEF, etc.)
2. **Use highest quality** (90+ for JPEG)
3. **Check metadata before upload** (every time)
4. **Use presets** (DxO) or Actions (Photoshop)
5. **Verify first export** before batch processing

### Quick Quality Check:
```
Good Export:
✅ File size: 2-8 MB (for web)
✅ Camera info visible in properties
✅ GPS coordinates present
✅ Date/time correct

Bad Export:
❌ File size: < 500 KB (too compressed)
❌ No camera info in properties
❌ No GPS data
❌ File created date instead of photo date
```

---

## 📚 Additional Resources

### Software-Specific Help:

**DxO PhotoLab:**
- Official Docs: https://support.dxo.com
- Metadata settings: Preferences → Metadata

**Adobe Photoshop:**
- File Info guide: Help → Photoshop Help → "metadata"
- Save for Web: Legacy feature, prefer "Export As"

### Metadata Standards:
- EXIF: Camera technical data
- IPTC: Description, keywords, copyright
- XMP: Adobe extensible metadata

---

## 🚀 Quick Start for Your Workflow

### Today:
1. Open DxO PhotoLab
2. Create export preset: "Web - Keep Metadata"
3. Test export one image
4. Verify metadata preserved
5. Use for all future exports

### This Week:
1. Export 10 test images
2. Upload to WordPress
3. Check camera stats widget shows data
4. Verify map widget shows locations
5. If working: batch process more

### Going Forward:
- Always use your metadata preset
- Check first image of every batch
- Keep RAW files as backup
- Document any issues

---

**Problem Solved! 🎉**

With these settings, your images will keep all their valuable metadata, making your theme's widgets work perfectly!

---

**Last Updated:** February 2026  
**Applies to:** DxO PhotoLab 7+, Adobe Photoshop CC 2020+
