<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com/
 * @since      1.0.0
 *
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/includes
 * @author     Milos Zivic <milos.zivic@simplicity.rs>
 */
class Appointment_Schedule {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Appointment_Schedule_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'appointment-schedule';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Appointment_Schedule_Loader. Orchestrates the hooks of the plugin.
	 * - Appointment_Schedule_i18n. Defines internationalization functionality.
	 * - Appointment_Schedule_Admin. Defines all hooks for the admin area.
	 * - Appointment_Schedule_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-appointment-schedule-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-appointment-schedule-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-appointment-schedule-admin.php';


		require_once plugin_dir_path(dirname(__FILE__) ) . 'admin/class-appointment-schedule-admin-staff-member.php';
		require_once plugin_dir_path(dirname(__FILE__) ) . 'admin/class-appointment-schedule-admin-staff-member-schedule.php';
		require_once plugin_dir_path(dirname(__FILE__) ) . 'admin/class-appointment-schedule-admin-services.php';
		require_once plugin_dir_path(dirname(__FILE__) ) . 'admin/class-appointment-schedule-admin-tables.php';
		require_once plugin_dir_path(dirname(__FILE__) ) . 'admin/class-appointment-schedule-admin-category.php';
		require_once plugin_dir_path(dirname(__FILE__) ) . 'admin/class-appointment-schedule-admin-holidays.php';
		require_once plugin_dir_path(dirname(__FILE__) ) . 'admin/class-appointment-schedule-admin-customers.php';
		require_once plugin_dir_path(dirname(__FILE__) ) . 'admin/class-appointment-schedule-admin-appointments.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-appointment-schedule-public.php';

		require_once plugin_dir_path( dirname(__FILE__) ) . 'public/class-appointment-schedule-public-form.php';

		$this->loader = new Appointment_Schedule_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Appointment_Schedule_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Appointment_Schedule_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Appointment_Schedule_Admin( $this->get_plugin_name(), $this->get_version() );

		//created db table
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_db_tables');
		//register settings page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_settings_page');

		//register submenu pages
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_submenu_pages');

		//get staff details AJAX
		$this->loader->add_action('wp_ajax_select_staff', $plugin_admin, 'select_staff');
		$this->loader->add_action('wp_ajax_nopriv_select_staff', $plugin_admin, 'select_staff');

		//save staff details details AJAX
		$this->loader->add_action('wp_ajax_save_staff_details', $plugin_admin, 'save_staff_details');
		$this->loader->add_action('wp_ajax_nopriv_save_staff_details', $plugin_admin, 'save_staff_details');
	
		//get category AJAX
		$this->loader->add_action('wp_ajax_get_category', $plugin_admin, 'get_category');
		$this->loader->add_action('wp_ajax_nopriv_get_category', $plugin_admin, 'get_category');

		//get category AJAX
		$this->loader->add_action('wp_ajax_save_service', $plugin_admin, 'save_service');
		$this->loader->add_action('wp_ajax_nopriv_save_service', $plugin_admin, 'save_service');		
		
		//get service AJAX
		$this->loader->add_action('wp_ajax_get_service', $plugin_admin, 'get_service');
		$this->loader->add_action('wp_ajax_nopriv_get_service', $plugin_admin, 'get_service');		
		
		//delete service AJAX
		$this->loader->add_action('wp_ajax_delete_service', $plugin_admin, 'delete_service');
		$this->loader->add_action('wp_ajax_nopriv_delete_service', $plugin_admin, 'delete_service');		
		
		//delete category AJAX
		$this->loader->add_action('wp_ajax_delete_category', $plugin_admin, 'delete_category');
		$this->loader->add_action('wp_ajax_nopriv_delete_category', $plugin_admin, 'delete_category');		
		
		//save staff_to_service AJAX
		$this->loader->add_action('wp_ajax_staff_to_service', $plugin_admin, 'staff_to_service');
		$this->loader->add_action('wp_ajax_nopriv_staff_to_service', $plugin_admin, 'staff_to_service');		
		
		//save save_schedule AJAX
		$this->loader->add_action('wp_ajax_save_schedule', $plugin_admin, 'save_schedule');
		$this->loader->add_action('wp_ajax_nopriv_save_schedule', $plugin_admin, 'save_schedule');		
		
		//save save_break AJAX
		$this->loader->add_action('wp_ajax_save_break', $plugin_admin, 'save_break');
		$this->loader->add_action('wp_ajax_nopriv_save_break', $plugin_admin, 'save_break');		
		
		//save chage_year_for_holidays AJAX
		$this->loader->add_action('wp_ajax_chage_year_for_holidays', $plugin_admin, 'chage_year_for_holidays');
		$this->loader->add_action('wp_ajax_nopriv_chage_year_for_holidays', $plugin_admin, 'chage_year_for_holidays');		
		
		//save save_holidays AJAX
		$this->loader->add_action('wp_ajax_save_holidays', $plugin_admin, 'save_holidays');
		$this->loader->add_action('wp_ajax_nopriv_save_holidays', $plugin_admin, 'save_holidays');		

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Appointment_Schedule_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('the_content', $plugin_public, 'display_form');

		//save change_month AJAX
		$this->loader->add_action('wp_ajax_change_month', $plugin_public, 'change_month');
		$this->loader->add_action('wp_ajax_nopriv_change_month', $plugin_public, 'change_month');	
		
		//save make_appointment AJAX
		$this->loader->add_action('wp_ajax_make_appointment', $plugin_public, 'make_appointment');
		$this->loader->add_action('wp_ajax_nopriv_make_appointment', $plugin_public, 'make_appointment');	

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action('init', $plugin_public, 'register_shortcodes');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Appointment_Schedule_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
