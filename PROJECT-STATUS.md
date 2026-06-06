# Attribute Icon for WooCommerce — Project Status

## What This Plugin Does

Adds an icon upload field to each WooCommerce attribute (Products > Attributes).
The uploaded icon displays automatically before the attribute label on the single
product page via the `woocommerce_attribute_label` filter.

---

## Current State

**Status: Submitted to WordPress.org — awaiting review**

- Plugin is complete and passing Plugin Check (zero errors, zero warnings)
- Code is pushed to GitHub: https://github.com/mosharafmanu/attribute-icon-for-wooCommerce
- Zip submitted to wordpress.org/plugins/developers/add/
- Review takes 1–2 weeks

---

## What Was Built

| Feature | Status |
|---|---|
| Icon upload field on Add Attribute screen | Done |
| Icon upload field on Edit Attribute screen | Done |
| Icon column in the attribute list table | Done |
| Frontend display via `woocommerce_attribute_label` filter | Done |
| Template helpers for custom themes | Done |
| Uninstall cleanup | Done |
| `.pot` translation file | Done |
| WordPress.org `readme.txt` | Done |
| 3 admin screenshots | Done |
| Plugin Check — zero errors | Done |
| GitHub repository | Done |

---

## What To Do After WordPress.org Approves

### 1. Add the frontend screenshot (most important)
Take a screenshot of a product page showing the attribute icon next to the label
in the Additional Information tab. Save as `screenshot-4.png` in `assets/`.
Update `readme.txt`:
```
4. Attribute icon displayed alongside the label on the single product page.
```
Commit and push to GitHub, then also push to SVN assets (see step 3).

### 2. Update Plugin URI
Once the plugin is live on WP.org, update the Plugin URI in the plugin header
from GitHub URL back to the WP.org URL:
```php
* Plugin URI: https://wordpress.org/plugins/attribute-icon-for-woocommerce
```

### 3. Set up WordPress.org SVN
WP.org will email SVN credentials and repo URL after approval.

```bash
# Check out the SVN repo
svn co https://plugins.svn.wordpress.org/attribute-icon-for-woocommerce wporg-svn
cd wporg-svn

# Copy plugin files into trunk (exclude dev files)
cp -r /path/to/plugin/* trunk/
rm trunk/phpcs.xml trunk/README.md trunk/.distignore trunk/.gitignore

# Copy screenshots into assets (NOT inside trunk)
cp /path/to/plugin/assets/screenshot-*.png assets/

# Add and commit
svn add trunk/* assets/*
svn ci -m "Initial release 1.0.0"

# Tag the release
svn cp trunk tags/1.0.0
svn ci -m "Tag 1.0.0"
```

### 4. Request a banner and icon (optional but recommended)
WP.org listing looks better with:
- `assets/banner-1544x500.png` — header banner
- `assets/banner-772x250.png` — banner (retina)
- `assets/icon-256x256.png` — plugin icon
- `assets/icon-128x128.png` — plugin icon (small)

Upload these to the SVN `assets/` folder (not trunk).

---

## Key File Locations

| File | Path |
|---|---|
| Main plugin file | `attribute-icon-for-woocommerce.php` |
| Admin class | `src/class-attributeimagemanager.php` |
| Frontend class | `src/class-attributefrontend.php` |
| Admin CSS | `assets/css/admin.css` |
| Admin JS | `assets/js/admin.js` |
| WP.org readme | `readme.txt` |
| Translation template | `languages/attribute-icon-for-woocommerce.pot` |
| Submission zip | `/tmp/attribute-icon-for-woocommerce.zip` |

---

## Technical Notes

- **PHP namespace:** `AttrIconWoo` (kept from original name — internal only)
- **Text domain:** `attribute-icon-for-woocommerce`
- **Storage:** Icons stored as attachment IDs in `wp_options` under `attricfo_attribute_image_{id}`
- **WP.org slug:** `attribute-icon-for-woocommerce` (pending approval)
- **Version:** `1.0.0` — bump `PLUGIN_VERSION` constant and `readme.txt` Stable tag together on each release
- **Tested up to:** WordPress 7.0 — update this in `readme.txt` when new WP versions release

---

## If WordPress.org Rejects

Common rejection reasons and fixes:

| Reason | Fix |
|---|---|
| Trademark issue with "WooCommerce" | Unlikely — "for WooCommerce" suffix is accepted |
| Security issue | Check reviewer's specific note and fix the flagged line |
| Missing license | `License: GPL-2.0+` is already in the header |
| Generic function names | Namespace is already applied throughout |

Reviewer feedback comes via email. Reply to the thread — they're helpful.
