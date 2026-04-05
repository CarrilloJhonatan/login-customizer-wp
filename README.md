# Login Customizer WP

Plugin to customize the WordPress login page (logo, colors, navigation links, input text, status messages/notices) with a modern admin panel, shortcodes, a REST API, and a Gutenberg block.

## Features

- Settings panel using the WordPress Color Picker (predefined palettes + custom editing) with real-time preview.
- Native image selector (Media Library) with upload/select and optional cropping.
- Login logo uses the original image (full) when selected from the Media Library.
- Configurable colors:
  - Background (`background_color`)
  - Form background (`form_background_color`)
  - Primary button (`primary_button_color`)
  - Text/labels (`label_color`)
  - Navigation links (`nav_link_color`)
  - Input text (`input_text_color`)
- Status messages (`#login_error`, `.message`, `.success`, `.notice`) use the Text/labels color.
- CSS variables (`--lcw-*`) for easier future changes and theme compatibility (light/dark).
- Data validation/sanitization using helpers and WordPress native functions.
- Smart caching for the login CSS with automatic invalidation.
- Theme-overridable template system.
- Dynamic shortcodes.
- REST API to read/update settings.
- Gutenberg compatibility via `register_block_type()` (dynamic block).
- Internationalization (textdomain + `.pot`).
- Optional structured (JSON) logger.
- Unit tests (PHPUnit + Brain Monkey scaffolding).

## Requirements

- WordPress 6.x recommended (must include Media Library and Color Picker).
- PHP >= 7.4.

## Installation

1. Download the plugin and upload it as a ZIP from `Plugins > Add New > Upload Plugin`.
2. Activate it.
3. In the admin menu open `Login Customizer` to configure.

## Configuration (Admin)

Menu: `Login Customizer`

- Logo:
  - Select an image from the Media Library (optionally crop it).
  - You can also paste an image URL (it is normalized and sanitized).
- Palettes:
  - Choose a predefined palette or use “Custom” to edit colors manually.
- Preview:
  - Reflects color and image changes live.

## Stored options (wp_options)

All settings are stored in a single option:

- `lcw_settings` (array)

Main keys:

- `logo_attachment_id` (int)
- `logo_url` (string)
- `background_color` (hex)
- `form_background_color` (hex)
- `primary_button_color` (hex)
- `label_color` (hex)
- `nav_link_color` (hex)
- `input_text_color` (hex)
- `palette` (string)
- `template` (string)
- `cache_ttl` (int, minimum 60)
- `enable_logging` (bool)
- `login_header_url` (url)
- `login_header_text` (string)

Compatibility: if legacy options exist (`custom_login_*`), the plugin reads them as a fallback.

## Shortcodes

- `[lcw_login_box]` Inserts a login form.
  - Attributes: `redirect`, `remember`, `template`, `cache`.
- `[lcw_login_logo]` Inserts the configured logo.
  - Attributes: `size`, `template`, `cache`.

## Templates

Templates included in the plugin `templates/` directory:

- `login-box.php`
- `logo.php`

Theme override:

- Copy templates to:
  - `wp-content/themes/YOUR-THEME/login-customizer-wp/templates/`
  - or `wp-content/themes/YOUR-CHILD/login-customizer-wp/templates/`

## Gutenberg

Dynamic block:

- `Login Box (LCW)` (Widgets category)
- Renders on the frontend using the shortcodes.

Block files:

- `blocks/login-customizer/block.json`
- `blocks/login-customizer/editor.js`
- `blocks/login-customizer/render.php`

## REST API

Namespace: `lcw/v1`

- `GET /wp-json/lcw/v1/settings`
- `POST /wp-json/lcw/v1/settings`

Permissions: requires `manage_options`.

## Cache

- The login CSS is cached using transients.
- Automatic invalidation happens when:
  - Settings are saved (`lcw_cache_version` changes).
  - The plugin version changes.
  - The style generator file changes (mtime).

If you need to force-refresh, open settings and click “Save changes”.

## Hooks and filters

Main filters:

- `lcw_settings_defaults`
- `lcw_sanitize_settings`
- `lcw_allowed_templates`
- `lcw_color_palettes`
- `lcw_template_paths`
- `lcw_cache_ttl`
- `lcw_min_contrast_ratio`
- `lcw_login_css`

Action:

- `lcw_settings_updated`

## i18n

- Text Domain: `login-customizer-wp`
- `.pot` file: `languages/login-customizer-wp.pot`

## Logs

If you enable “Structured logs”, events will be sent to `error_log` in JSON format (useful for debugging).

## Development and tests

Development dependencies via Composer:

1. `composer install`
2. Run PHPUnit tests (depending on your environment):
   - `vendor/bin/phpunit`

Files:

- `phpunit.xml.dist`
- `tests/`

## Project structure

- `custom.php`: plugin bootstrap.
- `includes/`: plugin code (modules, settings, utils, template loader).
- `assets/admin/`: admin panel JS/CSS.
- `templates/`: base templates.
- `blocks/`: Gutenberg block.
- `languages/`: i18n.
- `tests/`: unit tests.

## Troubleshooting

- If you see old styles on the login page:
  - Save settings once.
  - Clear browser cache and any caching/minification plugin cache.
- If the logo does not update in the live preview:
  - Ensure the URL is valid (no backticks, no pasted `url(...)`, no trailing `)`).
