<?php
/**
 * Plugin Name: Navilytics
 * Plugin URI: https://www.navilytics.com
 * Description: Adds the Navilytics script to each page of your site enabling user recordings, mouse movement and click heatmaps, and much more!
 * Version: 1.0
 * Author: Conner Hewitt
 * License: GPL3
 */

if(!class_exists('WP_Navilytics_Plugin')) {
	/**
	 * Navilytics class declaration
	 */
	class WP_Navilytics_Plugin {
		/**
		 * Construct
		 */
		public function __construct() {
			//Add the Navilytics code to the head of the page
			add_action('wp_head', array(&$this, 'add_code'));
			
			//Return here if user is not admin
			if(!is_admin()) return;
			
			//Register the settings page
			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_menu'));
		}
		
		/**
		 * Register and add settings
		 */
		public function admin_init() {
			register_setting( 'wp_navilytics_plugin-maingroup', 'navilytics_mid', array( $this, 'navilytics_settings_validate' ) );
			register_setting( 'wp_navilytics_plugin-maingroup', 'navilytics_pid', array( $this, 'navilytics_settings_validate' ) );
		}

		/**
		 * Add our options to the settings menu
		 */
		public function add_menu() {
			add_options_page('Navilytics Integration Settings', 'Navilytics', 'manage_options', 'wp_navilytics_plugin', array(&$this, 'add_settings_page'));
		}

		/*
		 * No validation, just remove leading and trailing spaces
		 */
		public function navilytics_settings_validate($input) {
			$cleaned = trim($input);
			return $cleaned;
		}

		/**
		 * Callback for adding settings page
		 */
		public function add_settings_page() {
?>
<div class="wrap">
  <h2>Navilytics Integration Settings</h2>
  <form method="post" action="options.php">
    <?php @settings_fields('wp_navilytics_plugin-maingroup'); ?>
    <?php @do_settings_fields('wp_navilytics_plugin-maingroup'); ?>
	
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><label for="navilytics_mid">Member ID</label></th>
          <td>
            <input id="navilytics_mid" name="navilytics_mid" value="<?php echo get_option('navilytics_mid'); ?>" class="regular-text" />
            <span class="description"> (ex. 1234)</span>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="navilytics_pid">Project ID</label></th>
          <td>
            <input id="navilytics_pid" name="navilytics_pid" value="<?php echo get_option('navilytics_pid'); ?>" class="regular-text" />
            <span class="description"> (ex. 1234)</span>
          </td>
        </tr>
      </tbody>
    </table>
    <p style="width: 80%;">In order for Navilytics to function, you must enter your Member ID and Project ID above. Both can be found on the <a href="https://www.navilytics.com/member/code_settings" target="_blank">Code & Settings</a> page within your member panel.</p>
    <p><a href="https://www.navilytics.com" target="_blank">https://www.navilytics.com</a></p>
    <p class="submit">
      <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Settings'); ?>" />
    </p>
  </form>
</div>
<?php
		}
		
		/**
		 * Add the Navilytics code to the page
		 */
		public static function add_code() {
			if(is_admin()) return;
			
			$mid = get_option( 'navilytics_mid' );
			$pid = get_option( 'navilytics_pid' );
			
			$script = "<!-- Navilytics -->
<script type=\"text/javascript\" id=\"__nls_script\">
    window.__nls = window.__nls || [];
    (function() {
        var nls = document.createElement('script'); nls.type = 'text/javascript'; nls.async = true; nls.id = '__nls_script_async';
        nls.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://www.navilytics.com/nls.js?mid={$mid}&pid={$pid}';
        var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(nls, x);
    })();
</script>
<!-- End Navilytics -->";

			_e($script);
		}
		
		/**
		 * Activate Navilytics
		 */
		public static function activate() {
		}
		
		/**
		 * Deactivate Navilytics
		 */
		public static function deactivate() {
		}
	}
}

if(class_exists('WP_Navilytics_Plugin')) {
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WP_Navilytics_Plugin', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_Navilytics_Plugin', 'deactivate'));
	
	//Instantiate the Navilytics class
	$wp_navilytics_plugin = new WP_Navilytics_Plugin();
} else {
	$message = "<h2 style='color:red'>Error in plugin</h2>
	<p>Sorry about that! Plugin <span style='color:blue;font-family:monospace'>Navilytics</span> reports that it was unable to start.</p>
	<p><a href='mailto:support@navilytics.com?subject=WP Plugin error%20error&body=What version of WordPress are you running? Please paste a list of your current active plugins here:'>Please report this error</a>.
	Meanwhile, here are some things you can try:</p>
	<ul><li>Make sure you are running the latest version of the plugin; update the plugin if not.</li>
	<li>There might be a conflict with other plugins. You can try disabling every other plugin; if the problem goes away, there is a conflict.</li>
	<li>Try a different theme to see if there's a conflict between the theme and the plugin.</li>
	</ul>";
	wp_die( $message );
}