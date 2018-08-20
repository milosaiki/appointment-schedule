<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com/
 * @since      1.0.0
 *
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/admin
 * @author     Milos Zivic <milos.zivic@simplicity.rs>
 */
class Appointment_Schedule_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Appointment_Schedule_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Appointment_Schedule_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style('bootstrap_css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css');
		wp_enqueue_style('bootstrap_theme_css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap-theme.min.css');
		wp_enqueue_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/appointment-schedule-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Appointment_Schedule_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Appointment_Schedule_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/appointment-schedule-admin.js', array( 'jquery' ), $this->version, false );


		wp_localize_script(
			$this->plugin_name,
			'appointment_schedule_ajax',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'ajax_nonce' => wp_create_nonce('appointment_schedule_nonce'),
			)
		);

	}

	public function create_db_tables()	{
		Tables::create();
	}
	

	public function register_settings_page() {
		add_menu_page( 
			__('Appointment schedule', 'appointment-schedule'),
			__('Appointment schedule', 'appointment-schedule'),
			'manage_options',
			'appointment-schedule',
			[ $this, 'display_staff_submenu_page' ],
			'dashicons-calendar-alt'
		);
	}

	public function register_submenu_pages() {
		add_submenu_page(
			'appointment-schedule',
			__( 'Staff members', 'appointment-schedule'),
			__( 'Staff members', 'appointment-schedule'),
			'manage_options',
			'appointment-schedule',
			[ $this, 'display_staff_submenu_page' ]
		);

		add_submenu_page(
			'appointment-schedule',
			__('Appointments', 'appointment-schedule'),
			__('Appointments', 'appointment-schedule'),
			'manage_options',
			'appointment-schedule-appointments',
			[ $this, 'display_appointments_submenu_page' ]
		);

		add_submenu_page(
			'appointment-schedule',
			__('Calendar', 'appointment-schedule'),
			__('Calendar', 'appointment-schedule'),
			'manage_options',
			'appointment-schedule-calendar',
			[ $this, 'display_calendar_submenu_page' ]
		);

		add_submenu_page(
			'appointment-schedule',
			__('Services', 'appointment-schedule'),
			__('Services', 'appointment-schedule'),
			'manage_options',
			'appointment-schedule-services',
			[ $this, 'display_services_submenu_page' ]
		);

		add_submenu_page(
			'appointment-schedule',
			__('Customers', 'appointment-schedule'),
			__('Customers', 'appointment-schedule'),
			'manage_options',
			'appointment-schedule-customers',
			[$this, 'display_customers_submenu_page']
		);
	}

	public function display_staff_submenu_page() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_REQUEST['create_staff']) {
			$fullname = $_POST['fullname'];
			Staff_Member::createStaffMember($fullname);			
		}
		Staff_Member::display();
	}

	public function display_appointments_submenu_page() 
	{
		Appointment::display();
	}

	public function display_calendar_submenu_page() {
		?>
			<div class="wrap">
				<h1>Calendar</h1>
			</div>
			<?php
	}

	public function display_customers_submenu_page()
	{
		Customer::display();
	}

	public function display_services_submenu_page()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_REQUEST['create_category']) {
			global $wpdb;
			$categoryName = $_POST['category_name'];
			Category::create($categoryName);
		}
		Services::display();
	}

	public function save_staff_details() {

		$fullname = $_POST['fullname'];
		$email = $_POST['email'] != '' ? $_POST['email'] : NULL;
		$phone = $_POST['phone'] != '' ? $_POST['phone'] : NULL;
		$info = $_POST['info'] != '' ? $_POST['info'] : NULL;
		$staffId = $_POST['staffId'];

		$response = Staff_Member::update_staff_details($fullname, $email, $phone, $info, $staffId);

		return wp_send_json( $response );
	}

	public function select_staff() {
		$staffId = $_POST['staffId'];

		$staff = Staff_Member::get_staff($staffId);
		$services = Services::get_all();
		$staff_to_services['staff_to_services'] = Services::get_staff_to_services($staffId);
		$staff_schedule['staff_schedule'] = Staff_Member_Schedule::get($staffId);
		$holidays['holidays'] = Holidays::get($staffId);
		$response = array_merge($staff, $services, $staff_to_services, $staff_schedule, $holidays);
		return wp_send_json($response);
	}

	public function get_category() 
	{
		$categoryId = $_REQUEST['categoryId'];
		if ($categoryId > 0) {
			$category = Category::get($categoryId);
			$services = Services::get_services_by_cat_id($categoryId);
			$response = array_merge($category, $services);
		} else {
			$response = Services::get_all();
		}

		wp_send_json( $response );
	}

	public function save_service() 
	{
		$title = $_POST['title'];
		$visibility = $_POST['visibility'];
		$price = $_POST['price'];
		$duration = $_POST['duration'];
		$paddingBefore = $_POST['paddingBefore'];
		$paddingAfter = $_POST['paddingAfter'];
		$categoryId = $_POST['category'];
		$staffMemberId = (int)$_POST['staffMember'];
		$limit = $_POST['limit'];
		$info = $_POST['info'];
		$serviceId = $_POST['serviceId'];
		
		try {
			$serviceId = Services::store($title, $visibility, $price, $duration, $paddingBefore, $paddingAfter, $categoryId, $limit, $info, $serviceId);

			if ($staffMemberId > 0 && $serviceId > 0) {
				Services::service_to_staff($serviceId, $staffMemberId);
			}
			$response['success'] = 1;
			$response['serviceId'] = $serviceId;
		} catch (\Exception $e) {
			$response['error'] = -1;
			$response['message'] = $e->getMessage();
		}

		wp_send_json( $response );
	}

	public function get_service() 
	{
		$serviceId = (int)$_REQUEST['serviceId'];
		$response = Services::get($serviceId);

		wp_send_json( $response );
	}

	public function delete_service() 
	{
		$serviceId = $_POST['serviceId'];

		Services::delete($serviceId);

		wp_send_json($serviceId );
	}

	public function delete_category() 
	{
		$categoryId = $_REQUEST['categoryId'];
		$response = Category::delete($categoryId);

		wp_send_json( $response );
	}

	public function staff_to_service() 
	{
		$servicesId = $_POST['servicesId'];
		$staffId = $_POST['staffId'];

		Staff_Member::unlink_staff_service($staffId);
		if (empty($servicesId)) {
			$response['success'] = 2;
		} else {
			$response = Staff_Member::link_staff_service($staffId, $servicesId);
		}

		wp_send_json($response );
	}

	public static function save_schedule()
	{
		$schedule = $_POST['schedule'];
		$staffId = $_POST['staffId'];

		$response = Staff_Member_Schedule::save($staffId, $schedule);
		wp_send_json($response );
	}

	public function save_break()
	{
		$sheduleId = $_POST['sheduleId'];
		$breakFrom = $_POST['breakFrom'];
		$breakTo = $_POST['breakTo'];

		$response = Staff_Member_Schedule::save_break($sheduleId, $breakFrom, $breakTo);
		wp_send_json($response);
	}

	public function chage_year_for_holidays()
	{
		$year = $_POST['year'];

		wp_send_json(Holidays::getDates($year) );
	}

	public function save_holidays() 
	{
		$staffId = $_POST['staffId'];
		$date = $_POST['date'];
		$repeat = $_POST['repeat'];
		$store = $_POST['store'];
		$delete = $_POST['deleteAction'];

		$response = $delete != 1 ? Holidays::store($staffId, $date, $repeat, $store) : Holidays::unlink($staffId, $date);

		wp_send_json( $response );
	}

}
