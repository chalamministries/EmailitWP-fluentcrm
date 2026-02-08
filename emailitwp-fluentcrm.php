<?php
/*
Plugin Name: EmailIt FluentCRM Integration
Plugin URI: https://github.com/chalamministries/EmailitWP-fluentcrm
Description: FluentCRM integration for EmailIt Mailer
Version: 2.3
Author: Steven Gauerke
Requires at least: 5.8
Requires PHP: 7.4
License: GPL2
*/

// Prevent direct access to this file
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('EmailIt_FluentCRM_Plugin_Updater')) {
	require_once plugin_dir_path(__FILE__) . 'class-emailit-fluentcrm-updater.php';
}

class EmailItFluentCRM {
	private static $instance = null;
	
	public static function get_instance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action('plugins_loaded', [$this, 'init']);
		add_action('emailit_register_tabs', [$this, 'register_fluentcrm_tab']);
		
		if (is_admin()) {
		   new EmailIt_FluentCRM_Plugin_Updater(__FILE__, 'chalamministries', 'EmailitWP-fluentcrm');
	   }
	}
	
	public function register_fluentcrm_tab() {
		if (class_exists('EmailItMailer')) {
			$emailit = EmailItMailer::get_instance();
			// Register before docs (100) but after settings (10)
			$emailit->register_tab('fluentcrm', 'FluentCRM', [$this, 'render_fluentcrm_tab'], 80);
		}
	}

	public function init() {
		// Check if required plugins are active
		if (!$this->check_dependencies()) {
			return;
		}

		// Register our hooks to intercept FluentCRM emails
		$this->register_email_hooks();
		
	}

	private function check_dependencies() {
		if (!function_exists('is_plugin_active')) {
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		$missing_plugins = [];

		if (!defined('FLUENTCRM')) {
			$missing_plugins[] = 'FluentCRM';
		}

		// Check for EmailIt with correct path
		if (!is_plugin_active('EmailitWP/emailit_mailer.php')) {
			$missing_plugins[] = 'EmailIt Mailer';
		}

		if (!empty($missing_plugins)) {
			add_action('admin_notices', function() use ($missing_plugins) {
				$message = 'EmailIt FluentCRM Integration requires the following plugins: ' . implode(', ', $missing_plugins);
				echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>';
			});
			return false;
		}

		return true;
	}
	
	public function render_fluentcrm_tab() {
		$is_enabled = get_option('emailit_fluentcrm_enabled', 'yes');
		
		if (isset($_POST['emailit_fluentcrm_save_settings'])) {
			check_admin_referer('emailit_fluentcrm_settings', 'emailit_fluentcrm_nonce');
			$is_enabled = isset($_POST['emailit_fluentcrm_enabled']) ? 'yes' : 'no';
			update_option('emailit_fluentcrm_enabled', $is_enabled);
			echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
		}
		
		?>
		<div class="card" style="max-width: 800px; margin-top: 20px;">
			<h2>FluentCRM Integration Settings</h2>
			<form method="post" action="">
				<?php wp_nonce_field('emailit_fluentcrm_settings', 'emailit_fluentcrm_nonce'); ?>
				<table class="form-table">
					<tr>
						<th scope="row">Enable Integration</th>
						<td>
							<label>
								<input type="checkbox" name="emailit_fluentcrm_enabled" value="yes" <?php checked($is_enabled, 'yes'); ?>>
								Use EmailIt for FluentCRM emails
							</label>
							<p class="description">When enabled, FluentCRM will use EmailIt to send all emails.</p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="emailit_fluentcrm_save_settings" class="button button-primary" value="Save Settings">
				</p>
			</form>
			
			<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
				<h3>How It Works</h3>
				<p>This integration allows FluentCRM to send emails through the EmailIt service.</p>
				<p>When enabled, FluentCRM will use your configured EmailIt API settings to send emails, 
				ensuring better deliverability and tracking features.</p>
				
				<h3>Recommended Settings</h3>
				<ul style="list-style-type: disc; margin-left: 20px;">
					<li>Ensure your EmailIt API key is properly configured in the main EmailIt settings page.</li>
					<li>Verify your sending domains in EmailIt to ensure proper email delivery.</li>
					<li>If you experience any issues, try disabling other email sending plugins that might conflict.</li>
				</ul>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Register hooks to intercept FluentCRM emails
	 */
	private function register_email_hooks() {
		$is_enabled = get_option('emailit_fluentcrm_enabled', 'yes');
		if($is_enabled) {
			add_filter('fluent_crm/email_headers', [$this, 'add_fluentcrm_identifier_header'], 10, 2);
			add_filter('fluent_crm/enable_mailer_to_name', function($status) {
			return false;
			});
		}
	}
	
	public function add_fluentcrm_identifier_header($headers, $data) {
		// Add a custom header to identify FluentCRM emails
		$headers['X-EmailIt-Source'] = 'FluentCRM';
		
		// You can also modify other headers or perform additional operations here
		
		return $headers;
	}
	
}

// Initialize the plugin
EmailItFluentCRM::get_instance();

add_action('plugins_loaded', function() {
	$assets_dir = plugin_dir_path(__FILE__) . 'assets';
	if (!file_exists($assets_dir)) {
		mkdir($assets_dir, 0755, true);
	}
	
	$logo_path = $assets_dir . '/emailit-logo.svg';
	if (!file_exists($logo_path)) {
		$logo_content = '<?xml version="1.0" encoding="UTF-8"?><svg id="Layer_2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 297.04 90.19"><g id="Layer_1-2"><path d="m70.06,45.47c0-11.31,7.91-19.53,19.15-19.54,11.84,0,19.23,10.25,17.5,23.14h-25.41c.76,4.46,4.6,6.49,8.22,6.49,3.32,0,6.18-1.81,7.69-4.6l9.13,4.97c-3.32,5.66-9.65,8.98-16.81,8.98-11.31,0-19.46-8.14-19.46-19.45Zm26.32-5.36c-.75-3.09-3.47-4.98-6.94-4.97-3.54,0-6.71,1.81-7.46,4.98h14.4Z" stroke-width="0"/><path d="m112.67,64.16V26.68s9.87,0,9.87,0l.08,2.04c1.96-1.36,4.6-2.79,8.22-2.79,4.37,0,8.14,1.66,10.86,4.52,2.64-2.56,6.03-4.53,10.63-4.53,9.43,0,15.84,7.16,15.84,17.49v20.74s-10.02,0-10.02,0v-20.13c0-5.28-2.42-8.45-6.72-8.44-2.26,0-4,1.13-4.98,3.09.15,1.81.23,3.7.23,5.58v19.91s-10.1,0-10.1,0v-20.13c0-5.28-2.42-8.45-6.65-8.44-4.37,0-7.16,3.4-7.16,8.07v20.51s-10.1,0-10.1,0Z" stroke-width="0"/><path d="m173.89,52.22c0-7.92,6.48-12.9,16.36-13.35,1.73-.08,3.54.07,5.13.38l-.08-.6c-.3-1.66-1.89-3.09-4.53-3.32-3.17-.3-7.54,1.06-12.14,3.7l-3.47-9.05c5.5-3.09,10.33-4.08,15.31-4.08,9.58,0,16.14,6.03,16.14,16.13v22.09s-9.87,0-9.87,0v-1.81c-2.26,1.43-5.05,2.57-8.52,2.57-8.29,0-14.33-4.97-14.33-12.66Zm22.17-1.67l.15-2.19c-1.66-.38-3.39-.53-5.35-.45-4.37.3-6.79,2.19-6.79,4.3,0,1.96,1.74,3.69,4.68,3.69,3.32-.08,6.71-2.11,7.31-5.36Z" stroke-width="0"/><path d="m211.58,17.14c0-3.24,2.41-5.81,5.73-5.81,3.24,0,5.81,2.56,5.81,5.8,0,3.32-2.56,5.81-5.8,5.81-3.32,0-5.73-2.49-5.73-5.8Zm.32,46.98V26.64s10.17,0,10.17,0v37.48s-10.17,0-10.17,0Z" stroke-width="0"/><path d="m249.45,62c-9.65,6.41-20.51,1.06-20.52-10.7l-.09-39.97h10.03s.09,39.21.09,39.21c0,4.37,2.87,5.13,6.79,2.94l3.7,8.52Z" stroke-width="0"/><path d="m253.58,17.13c0-3.24,2.41-5.81,5.73-5.81,3.24,0,5.81,2.56,5.81,5.8,0,3.32-2.56,5.81-5.8,5.81-3.32,0-5.73-2.49-5.73-5.8Zm.32,46.98V26.63s10.17,0,10.17,0v37.48s-10.17,0-10.17,0Z" stroke-width="0"/><path d="m294.16,60.85c-11.08,7.62-23.98,3.63-23.98-9.12l-.09-40.42h10.1s0,15.3,0,15.3h8.37s0,9.65,0,9.65h-8.37s.08,14.1.08,14.1c0,4.9,4.53,5.5,9.05,2.33l4.83,8.14Z" stroke-width="0"/><g id="mail-send-envelope--envelope-email-message-unopened-sealed-close"><g id="Subtract"><path d="m7.83,63.82c3.85.27,9.96.55,18.68.54,8.72,0,14.84-.29,18.68-.56,3.85-.27,6.9-3.18,7.25-7.06.29-3.34.58-8.38.57-15.36,0-.44,0-.88,0-1.3-3.14,1.45-6.31,2.83-9.51,4.13-2.95,1.19-6.15,2.39-9.11,3.29-2.91.89-5.74,1.55-7.88,1.55s-4.98-.66-7.88-1.55c-2.96-.9-6.16-2.1-9.11-3.29-3.2-1.29-6.38-2.67-9.51-4.12,0,.43,0,.86,0,1.31,0,6.98.29,12.02.58,15.36.34,3.89,3.4,6.79,7.25,7.06Z" fill="#15c182" stroke-width="0"/><path d="m.05,36.22c.1-4.37.31-7.74.52-10.19.34-3.89,3.4-6.79,7.25-7.06,3.85-.27,9.96-.55,18.68-.56,8.72,0,14.84.28,18.68.54,3.85.27,6.91,3.17,7.25,7.06.22,2.45.43,5.81.53,10.19-.04.02-.08.03-.12.05l-.05.02-.16.08c-.98.46-1.95.91-2.94,1.35-2.48,1.12-4.98,2.19-7.51,3.22-2.9,1.17-6,2.33-8.82,3.19-2.87.88-5.27,1.4-6.85,1.4s-3.98-.52-6.85-1.39c-2.82-.86-5.92-2.02-8.82-3.19-3.52-1.42-7.01-2.95-10.45-4.56l-.16-.08-.05-.02s-.08-.04-.12-.05h0Z" fill="#007b5e" stroke-width="0"/></g></g></g></svg>';
		file_put_contents($logo_path, $logo_content);
	}
});