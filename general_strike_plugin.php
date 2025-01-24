<?php
/*
Plugin Name: General Strike Overlay
Description: Displays a custom HTML overlay on all pages except the admin area. Supports two text fields and an enable/disable toggle.
Version: 1.01
Author: Freedom Supporter
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) exit;

// Add menu to the admin panel
add_action('admin_menu', function() {
    add_options_page(
        'Generalni štrajk',
        'Generalni štrajk',
        'manage_options',
        'general-strike-overlay',
        'general_strike_overlay_settings_page'
    );
});

// Register settings
add_action('admin_init', function() {
    register_setting('general_strike_overlay_options', 'gso_enabled');
    register_setting('general_strike_overlay_options', 'gso_heading');
    register_setting('general_strike_overlay_options', 'gso_description');
    register_setting('general_strike_overlay_options', 'gso_illustration');
});

// Render settings page
function general_strike_overlay_settings_page() {
    ?>
    <div class="wrap">
        <h1>Generalni štrajk</h1>
        <form method="post" action="options.php">
            <?php settings_fields('general_strike_overlay_options'); ?>
            <?php do_settings_sections('general_strike_overlay_options'); ?>
            <table class="form-table">
				<tr valign="top">
					<th scope="row">Omogući štrajk</th>
					<td><input type="checkbox" name="gso_enabled" value="1" <?php checked(1, get_option('gso_enabled', 0)); ?>></td>
				</tr>
                <tr valign="top">
                    <th scope="row">Naslov</th>
                    <td><input type="text" name="gso_heading" value="<?php echo esc_attr(get_option('gso_heading', 'Generalni štrajk')); ?>" style="width: 100%;"></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Dodatni tekst (opciono)</th>
                    <td><textarea name="gso_description" rows="5" style="width: 100%;"><?php echo esc_textarea(get_option('gso_description', '')); ?></textarea></td>
                </tr>
				
				<tr valign="top">
					<th scope="row">Omogući illustraciju</th>
					<td><input type="checkbox" name="gso_illustration" value="1" <?php checked(1, get_option('gso_illustration', 1)); ?>></td>
				</tr>
				
            </table>
            <?php submit_button('Sačuvaj izmene'); ?>
        </form>
    </div>
    <?php
}

// Output the overlay
add_action('template_redirect', function() {
    if (is_admin() || !get_option('gso_enabled', 0)) {
        return; // Don't interfere with the admin area or if the overlay is disabled
    }

    $heading = esc_html(get_option('gso_heading', 'Generalni štrajk'));
    $description = esc_html(get_option('gso_description', ''));
	$image_enabled = esc_url(get_option('gso_illustration', ''));

    $html = "<div style='position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; color: red; display: flex; flex-direction: column; align-items: center; font-family: sans-serif; z-index: 99999;'>";
	
	// Show image and limit it to 600px width
    if ($image_enabled) {
    	$html .= "<img src='" . plugin_dir_url(__FILE__) . 'strajk-illustracija.jpg' . "' style='max-width: 500px;'>";
    }

    $html .= "<h1 style='font-size: 3em; text-align: center; max-width: 80% font-weight: bold;'>$heading</h1>";
    if (!empty($description)) {
        $html .= "<p style='text-align: center; font-size: 1.5em; max-width: 80%'>$description</p>";
    }

	$html .= "<p style='text-align: center; font-size: 14px; margin-top: 50px;'><a style='color: #222222; text-decoration:none;' href='https://nuns.rs/download/strajk.html' target='_blank'>Preuzmi ovaj WordPress plugin i priključi svoj sajt štrajku! 📣</a></p>";

    $html .= "</div>";


    echo $html;
    exit; // Stop further output
});
