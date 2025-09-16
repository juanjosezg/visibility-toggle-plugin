# Year Visibility Toggle (Plugin + Container)

This repository contains two PHP files designed to work together to control the visibility of WordPress content based on the current and future years.  

## 📂 Included Files

- **`container-year.php`**  
  A PHP container file that wraps HTML content in shortcodes or templates to show/hide blocks depending on the year. Ideal for integrating dynamic content in Elementor or other builders without modifying the main theme.  

- **`year-visibility-toggle.php`**  
  A WordPress plugin that manages the core logic: registers settings in the admin area, injects dynamic CSS on the front end, and allows showing or hiding sections by year.  

## ✨ Key Features

- Visibility control of content for specific years (default: 2024–2027).  
- Override via URL query string `?aep=YYYY`.  
- Integration with Elementor (in editor mode all blocks display with colored borders for easy identification).  
- Simple configuration from the WordPress admin area.  
- Automatic CSS injection based on configured years.  

## ⚙️ Installation

1. Upload the **`year-visibility-toggle.php`** file to the `wp-content/plugins/` folder.  
2. Activate the plugin from the WordPress Admin Panel → Plugins.  
3. Upload or include **`container-year.php`** in your child theme or in a custom functional plugin.  

## 📝 Basic Usage

### Shortcode / Container  
Wrap your content with the year container. For example:

```php
[year_container year="2026"]
  <p>This content will only be visible in 2026.</p>
[/year_container]