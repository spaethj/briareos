<?php
/*
Plugin Name: Briareos
Plugin URI: https://github.com/spaethj/briareos.git
Description: A simple plugin to gather specific functions of this website.
Author: Jeremy Spaeth
Version: 1.0
Author URI: https://github.com/spaethj
*/
/**
 * @TODO ajouter fichier .gitignor dans /inc
 */
/**
 * Class Panel
 */
class Panel {

	/**
	 * Panel constructor.
	 * Instance 'eos_plugin_menu'
	 */
	function __construct()
	{
		add_action('admin_menu', array($this, 'eos_plugin_menu'));
		add_action('admin_init', array($this, 'register_settings'));
	}

	/**
	 * Add a new submenu under Settings.
	 */
	function eos_plugin_menu()
	{
		add_options_page( 'Briareos settings', 'Briareos', 'manage_options', 'briareos-settings', array ($this, 'eos_plugin_options'));
	}

	/**
	 * Get the list of all PHP files (only) in an array;
	 * @return array
	 */
	function files_list()
	{
		$files_list = glob(dirname(__FILE__) . '/inc/*.php');
		return $files_list;
	}

	/**
	 * Register the settings and their data.
	 */
	function register_settings()
	{
		$files_list = $this->files_list();
		foreach ($files_list as $file) {
			register_setting('eos_option_group', 'eos_' . pathinfo($file, PATHINFO_FILENAME));
		}
	}

	/**
	 * Generate checkbox with PHP files list.
	 */
	function eos_checkbox()
	{
		$buffer = '';
		$files_list = $this->files_list();

		foreach ($files_list as $file) {
			$buffer .= '<label for="eos_' . pathinfo($file, PATHINFO_FILENAME) . '">';
			$buffer .= '<input  id="eos_' . pathinfo($file, PATHINFO_FILENAME) . '" name="eos_' . pathinfo($file, PATHINFO_FILENAME) . '" type="checkbox" value="enable" ' . checked(get_option('eos_' . pathinfo($file, PATHINFO_FILENAME)), 'enable', false) . ' />' . pathinfo($file, PATHINFO_FILENAME);
			$buffer .= '</label><br />';
		}

		echo $buffer;
	}

	/**
	 * Displays the page content for the Briareos Settings submenu
	 */
	function eos_plugin_options()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		echo '<div class="wrap">';

		echo '<h1>' . get_admin_page_title() . '</h1>';

		echo '<form method="post" action="options.php">';
		settings_fields('eos_option_group');
		$this->eos_checkbox();
		submit_button();
		echo '</form>';

		echo '</div>';
	}
}
$panel = new Panel();

$files_list = $panel->files_list();
foreach ($files_list as $file) {
	if (get_option('eos_' . pathinfo($file, PATHINFO_FILENAME)) == 'enable') {
		include $file;
	}
}
