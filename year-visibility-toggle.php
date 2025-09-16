<?php
/**
 * Plugin Name: Year Visibility Toggle
 * Description: Show/hide elements by year (2024–2027). Supports override via ?aep=YYYY. Elementor editor shows all with colored border.
 * Version: 1.9.3
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

class Year_Visibility_Toggle {
    private $option_name  = 'yvt_visibility';
    private $option_style = 'yvt_style';
    private $years        = [2024, 2025, 2026, 2027];

    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_post_yvt_save', [$this, 'save_settings']);
        add_action('wp_head', [$this, 'inject_css'], 1);
        register_activation_hook(__FILE__, [$this, 'activate_defaults']);
    }

    public function activate_defaults() {
        $current_year = (int) current_time('Y');
        $defaults = [];
        foreach ($this->years as $y) {
            $defaults[$y] = ($y < $current_year) ? 'hide' : 'show';
        }
        if (!get_option($this->option_name)) {
            add_option($this->option_name, $defaults);
        }
        if (!get_option($this->option_style)) {
            $colors = [];
            foreach ($this->years as $y) {
                $colors[$y] = '#ff0000';
            }
            add_option($this->option_style, $colors);
        }
    }

    public function add_settings_page() {
        add_options_page('Year Visibility', 'Year Visibility', 'manage_options', 'yvt-settings', [$this, 'render_settings_page']);
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) return;

        $opts = get_option($this->option_name, []);
        $style_opts = get_option($this->option_style, []);
        $current_year = (int) current_time('Y');

        foreach ($this->years as $y) {
            if (!isset($opts[$y])) $opts[$y] = ($y < $current_year) ? 'hide' : 'show';
            if (!isset($style_opts[$y])) $style_opts[$y] = '#ff0000';
        }
        ?>
        <div class="wrap">
            <h1>Year Visibility (2024–2027)</h1>
            <p>Check the box to <strong>show</strong> that year. If unchecked, it will be <strong>hidden</strong> using <code>display: none !important;</code> on <code>.year-YYYY</code>.</p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('yvt_save_settings', 'yvt_nonce'); ?>
                <input type="hidden" name="action" value="yvt_save" />

                <h2>Year Settings</h2>
                <table class="widefat striped" style="max-width: 700px;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Visible</th>
                            <th>Editor Border Color</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->years as $y):
                            $checked = ($opts[$y] === 'show') ? 'checked' : '';
                            $border_color = $style_opts[$y] ?? '#ff0000';
                        ?>
                        <tr>
                            <td><code><?php echo esc_html($y); ?></code></td>
                            <td>
                                <input type="checkbox" name="yvt_visibility[<?php echo esc_attr($y); ?>]" value="show" <?php echo $checked; ?> />
                                Show
                            </td>
                            <td>
                                <input type="color" name="yvt_style[<?php echo esc_attr($y); ?>]" value="<?php echo esc_attr($border_color); ?>" />
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php submit_button('Save Changes'); ?>
            </form>

            <h2>Force visibility via URL</h2>
            <p>You can override the visibility setting for a specific page by adding <code>?aep=YYYY</code> to the URL.</p>
            <p><strong>Example:</strong>  
                <code>?aep=2026</code> will <strong>only show</strong> elements with <code>.year-2026</code> and <strong>hide all others</strong> including future years like 2027.
            </p>
            <p><strong>In Elementor editor mode</strong>, all years are always shown with a colored dashed border and hover year label.</p>
        </div>
        <?php
    }

    public function save_settings() {
        if (!current_user_can('manage_options') ||
            !isset($_POST['yvt_nonce']) ||
            !wp_verify_nonce($_POST['yvt_nonce'], 'yvt_save_settings')
        ) {
            wp_die('Permission denied.');
        }

        $incoming = $_POST['yvt_visibility'] ?? [];
        $styles_in = $_POST['yvt_style'] ?? [];

        $clean = [];
        $style_clean = [];

        foreach ($this->years as $y) {
            $clean[$y] = isset($incoming[$y]) ? 'show' : 'hide';
            $style_clean[$y] = sanitize_hex_color($styles_in[$y] ?? '') ?: '#ff0000';
        }

        update_option($this->option_name, $clean);
        update_option($this->option_style, $style_clean);

        wp_safe_redirect(admin_url('options-general.php?page=yvt-settings&updated=true'));
        exit;
    }

    private function sanitize_year($val) {
        $val = is_array($val) ? reset($val) : $val;
        $val = preg_replace('/\D/', '', $val);
        return (int) $val;
    }

    private function get_override_year() {
        if (isset($_GET['aep'])) {
            $y = $this->sanitize_year($_GET['aep']);
            return in_array($y, $this->years) ? $y : null;
        }
        return null;
    }

    private function is_elementor_editor() {
        if (isset($_GET['elementor-preview'])) return true;
        if (class_exists('\Elementor\Plugin')) {
            $plugin = \Elementor\Plugin::$instance;
            return $plugin && $plugin->editor && $plugin->editor->is_edit_mode();
        }
        return false;
    }

    public function inject_css() {
        $opts = get_option($this->option_name, []);
        $styles = get_option($this->option_style, []);
        $override = $this->get_override_year();
        $is_editor = $this->is_elementor_editor();

        echo "<!-- YVT active -->\n";

        if ($is_editor) {
            echo "<style id='yvt-style-editor'>\n";
            foreach ($this->years as $y) {
                $color = esc_html($styles[$y] ?? '#ff0000');
                echo ".year-{$y} { display: initial !important; border: 1px dashed {$color} !important; padding: 0; position: relative; }\n";
                echo ".year-{$y}:hover::after { content: '{$y}'; position: absolute; top: -25px; right: 0; background: {$color}; color: white; font-size: 10px; padding: 4px; }\n";
            }
            echo "</style>\n";
            return;
        }

        $to_hide = [];

        foreach ($this->years as $y) {
            if ($override !== null) {
                if ((int) $y !== (int) $override) {
                    $to_hide[] = ".year-{$y}";
                }
            } else {
                if (($opts[$y] ?? 'show') === 'hide') {
                    $to_hide[] = ".year-{$y}";
                }
            }
        }

        if (!empty($to_hide)) {
            echo "<style id='yvt-style'>\n";
            foreach ($to_hide as $selector) {
                echo esc_html($selector) . " { display: none !important; }\n";
            }
            echo "</style>\n";
        }
    }
}

new Year_Visibility_Toggle();