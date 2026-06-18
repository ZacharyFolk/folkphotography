# I Was Here Plugin — Usage Guide

This guide explains what the plugin does, what data it stores, where that data surfaces in the FolkPhotography theme, and what fields are currently missing or not yet wired up.

---

## What the plugin does

When you upload an image to the WordPress Media Library, the plugin automatically reads the EXIF metadata embedded in the file and stores it as post meta on the attachment. You can also manually set or correct location data via the "I Was Here Location" panel on the attachment edit screen.

Everything is stored on the **attachment post** — not on the portfolio post that uses the image. The theme then reads from the attachment when rendering a portfolio item.

---

## Where to see the data

1. **Media Library → any image → Edit** (list view: click the image title)
2. Look for the **"I Was Here Location"** panel on the right side — it shows Camera Settings (read-only from EXIF) and editable Location fields (lat, lng, place name).

The panel is read-only for camera data. You cannot edit ISO, aperture, etc. from the admin — they come from the file. If they're wrong, the source EXIF needs to be fixed in your editing software before re-uploading (or use the rescan tool after fixing the file on disk).

---

## Complete list of stored meta keys

All keys are stored on the **attachment** (the image file), not the portfolio post.

| Meta key | What it holds | Example value | Source |
|---|---|---|---|
| `_iwh_has_exif` | Flag: EXIF was found on upload | `1` | Auto (upload) |
| `_iwh_camera_make` | Camera manufacturer | `NIKON CORPORATION` | Auto (EXIF IFD0 `Make`) |
| `_iwh_camera_model` | Camera body name | `NIKON D850` | Auto (EXIF IFD0 `Model`) |
| `_iwh_lens` | Lens model | `24-70mm f/2.8` | Auto (EXIF `LensModel`) |
| `_iwh_focal_length` | Focal length in mm, rounded | `24` | Auto (EXIF `FocalLength`) |
| `_iwh_aperture` | F-number, rounded to 1 decimal | `6.3` | Auto (EXIF `FNumber`) |
| `_iwh_shutter_speed` | Exposure time as raw fraction | `1/250` or `130/10` | Auto (EXIF `ExposureTime`) |
| `_iwh_iso` | ISO speed | `800` | Auto (EXIF `ISOSpeedRatings`) |
| `_iwh_date_taken` | Capture date/time | `2024:08:19 21:34:03` | Auto (EXIF `DateTimeOriginal`) |
| `_iwh_lat` | GPS latitude, decimal | `44.4280` | Auto (EXIF GPS) or manual |
| `_iwh_lng` | GPS longitude, decimal | `-110.5885` | Auto (EXIF GPS) or manual |
| `_iwh_place_name` | Human-readable location name | `Yellowstone` | Manual only |

**Note on shutter speed format:** The raw value stored is the fraction string from EXIF, e.g. `1/250` for fast exposures or `130/10` for long exposures (13 seconds). The single-portfolio template converts this to a readable format on display (e.g. `1/250s` or `13s`).

**Note on date format:** Stored as-is from EXIF: `YYYY:MM:DD HH:MM:SS`. The theme converts this to the site's configured date format for display.

---

## Where data appears in the theme

### Single portfolio item (`single-portfolio.php`)

The photo meta panel shows when the "Show photo description & camera data" toggle is ON in the editor. It reads from the **featured image** attachment.

Displayed fields: Camera (make + model combined), Lens, Focal Length, Aperture (shown as ƒ/x.x), Shutter, ISO, Date, Location (place name).

Also shows the **media library Description** field (`post_content` on the attachment) in the left column.

### Portfolio archive (`archive-portfolio.php`)

Shows Camera (make + model) in the overlay/caption area on the grid.

### Masonry gallery (`page-templates/masonry-gallery.php`)

Shows Camera, Lens, and exposure settings (aperture/shutter/ISO) in the GLightbox description panel.

### Camera Stats Widget

Aggregates across all attachments with EXIF data:
- Total photo count
- Most-used camera body
- Most-used lens
- Most common aperture and ISO
- Number of unique GPS locations

### Location Map Widget / `[iwh_world_map]` shortcode

Plots markers on an interactive Leaflet map for every attachment that has `_iwh_lat` and `_iwh_lng`. Clicking a marker opens the image in GLightbox.

---

## Copyright — not currently supported

Your question about copyright: **copyright info is not extracted by this plugin**. EXIF files can carry a `Copyright` string in the `IFD0` section (same place as `Make` and `Model`), but `exif-reader.php` does not read it.

If you embed copyright strings in your files (e.g. via Lightroom's metadata presets: Metadata → Edit Metadata Presets → IPTC Copyright), they are ignored on upload and not stored anywhere.

To add copyright support, `exif-reader.php` would need one line in `extract_camera_data()`:

```php
// Copyright string (IFD0 section)
if (isset($exif['IFD0']['Copyright'])) {
    $data['copyright'] = trim($exif['IFD0']['Copyright']);
}
```

This would store it as `_iwh_copyright` on the attachment. The theme could then read and display it (e.g. in the photo meta panel footer or a caption line).

---

## Other fields the plugin doesn't currently extract

These are available in EXIF but not read by the plugin:

| EXIF field | What it is | Notes |
|---|---|---|
| `IFD0.Copyright` | Copyright string | See above |
| `IFD0.Artist` | Photographer name | Could be used for credit lines |
| `IFD0.Software` | Editing software | e.g. "Adobe Lightroom Classic 13.0" |
| `GPS.GPSAltitude` | Elevation in meters | Would need `_iwh_altitude` key |
| `GPS.GPSImgDirection` | Compass bearing the camera faced | Niche but useful for landscape |
| `EXIF.Flash` | Whether flash fired | 0/1 flag |
| `EXIF.WhiteBalance` | Auto/Manual white balance | |
| `EXIF.ExposureProgram` | Manual / Aperture Priority / etc. | |

The most useful additions for a photography portfolio would be **Copyright**, **Artist**, and **Altitude**.

---

## Using the Rescan Tool

Go to **Tools → i-was-here Scanner**. This re-reads EXIF from every image in your library and updates the meta. Use it when:

- You uploaded images before the plugin was installed
- You edited EXIF in Lightroom/Photoshop and re-exported (you'd need to delete and re-upload, or the file on disk won't have changed)
- You want to fill in missing data after a plugin update adds new extracted fields

The tool runs in batches of 20 images and shows a progress bar. If interrupted, a Resume button appears on next visit.

**Overwrite mode:** Off by default — only fills empty meta, does not overwrite existing values. Turn it on if you need to force a full re-read (e.g. after fixing EXIF in your source files).

---

## Manually correcting data

**Location (lat/lng/place name):** Fully editable in the "I Was Here Location" panel on any attachment edit screen. The map lets you click to set coordinates, or type them directly.

**Camera/EXIF data:** Not editable through the plugin UI. These come from the file. To correct wrong camera data:
1. Fix the EXIF in Lightroom (Metadata panel) or ExifTool
2. Re-upload the corrected file (WordPress will create a new attachment — update the Featured Image on the portfolio post)
3. Or edit the raw meta directly via a plugin like "Custom Field Suite" or WP-CLI: `wp post meta update <attachment_id> _iwh_camera_model "Nikon D850"`

---

## WordPress native fields on attachments

These are set in the Media Library and are separate from plugin data. The theme reads some of them:

| Field | Where to set it | Used by theme |
|---|---|---|
| **Title** | Media Library → Edit → Title | Not displayed on front end currently |
| **Caption** | Media Library → Edit → Caption | Shown in portfolio excerpt if post has no excerpt |
| **Description** | Media Library → Edit → Description | Shown in the photo meta panel (left column) |
| **Alt Text** | Media Library → Edit → Alternative Text | Used on `<img>` tags for accessibility |

The Description field is the main place to write a story or notes about the photograph. It supports full HTML and is displayed via `wpautop()` on single portfolio items.
