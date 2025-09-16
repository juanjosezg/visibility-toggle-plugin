# Year Visibility Toggle (Plugin + Container)

This repository contains two PHP files designed to work together as a WordPress plugin to control the visibility of content based on the current and future years.

## 📂 Included Files

- **`container-year.php`**  
  A PHP file that works alongside the main plugin file. It lives inside the plugin folder and provides the container logic to apply year-based visibility to content.

- **`year-visibility-toggle.php`**  
  The main WordPress plugin file. It registers settings in the admin area, injects dynamic CSS on the front end, and manages showing or hiding sections by year.

## ✨ Key Features

- Visibility control of content for specific years (default: 2024–2027).
- URL override via query string `?aep=YYYY`.
- Elementor-friendly: in editor mode, year-specific content can be outlined (border) for easy identification.
- Simple configuration from the WordPress admin area.
- Automatic CSS injection based on configured years.

## ⚙️ Installation

1. Create a plugin folder and place both files inside it:
   ~~~text
   wp-content/plugins/year-visibility-toggle/
     ├── year-visibility-toggle.php
     └── container-year.php
   ~~~
2. Go to **WordPress Admin → Plugins** and activate **Year Visibility Toggle**.

> No manual `include` from the theme is required; `container-year.php` is loaded by the plugin.

## 📝 Basic Usage

- There is **no built-in shortcode** in these files.
- The plugin provides year-based visibility logic and injects CSS based on your admin settings.
- Use your theme/templates to mark or structure year-specific content as needed; the plugin’s rules will control visibility according to the saved configuration and the optional URL override.

### URL Override

Append `?aep=2025` to any site URL to force the plugin to treat the page as the year **2025**, regardless of the current year.

## ⚙️ Admin Configuration Panel

The plugin adds a **“Year Visibility Toggle”** settings page under **Settings → Year Visibility Toggle**.

### Options Available

- **Visibility per Year**  
  For each defined year (e.g., 2024, 2025, 2026, 2027), choose whether content for that year is **shown** or **hidden** by default.

- **Editor Border Style**  
  Configure border styles/colors for each year in Elementor editor mode to visually identify year-specific blocks while editing.

- **Save Settings**  
  Click **Save** to persist your configuration. The plugin stores options in the WordPress database and applies them automatically on the front end.

### How It Works

- Settings are saved into standard WordPress options (e.g., `yvt_visibility`, `yvt_style`).
- On the front end, the plugin injects CSS based on your selections.
- In Elementor edit mode, year-specific content can be force-visible and outlined, aiding authors while editing.

## 🗂 File Structure

~~~text
wp-content/
└── plugins/
    └── year-visibility-toggle/
        ├── container-year.php          # Container logic for year-based visibility
        └── year-visibility-toggle.php  # Main Year Visibility Toggle plugin file
~~~

## 💡 Development Notes

- Default years are defined in an array (e.g., `[2024, 2025, 2026, 2027]`) inside `year-visibility-toggle.php` (property `$years`). Adjust as needed.
- No shortcode is included by default. If you need one, you can add your own `add_shortcode()` wrapper within this plugin.
- CSS injection typically runs on `wp_head` (or `wp_print_footer_scripts` depending on version/needs).
- All settings from the control panel load automatically on each request.

## 🧪 Troubleshooting

- **Styles not visible in the editor**: Ensure the Editor Border Style settings are configured and that you are in Elementor’s editor mode.
- **Override not working**: Confirm you’re passing a 4-digit year in the `?aep=` query string (e.g., `?aep=2026`).
- **Cache**: If using caching, purge after changing visibility settings.

## 📄 License

This project is licensed under the GPL v2 or later.