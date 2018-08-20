<?php


/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com/
 * @since      1.0.0
 *
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/public
 * @author     Milos Zivic <milos.zivic@simplicity.rs>
 */
class Appointment_Schedule_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/appointment-schedule-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/appointment-schedule-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'appointment_schedule_ajax',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'ajax_nonce' => wp_create_nonce('appointment_schedule_nonce'),
			)
		);

	}

	public function display_form( $content ) 
	{
		error_log('display_form');
		$html = Appointment_Schedule_Form::display();
		return $content . '<hr>' . $html;
	}

	public function register_appointment_schedule_shortcode( $atts = [] )
	{
		error_log('register_appointment_schedule_shortcode');
	}

	 public function register_shortcodes()
	{
		add_shortcode( 'appointment-schedule-form', [$this, 'display_form'] );
	}

	public function change_month()
	{
		$response = [];
		if (isset($_POST['changed'])) {
			$changed = (int)$_POST['changed'];
			$fromAjax = 1;
			$response['calendar'] = Appointment_Schedule_Form::dates($changed, $fromAjax);
			$response['success'] = 1;
		} else {
			$response['error'] = -1;
		}
		wp_send_json( $response );
	}

	public function make_appointment()
	{
		$date = trim($_POST['date']);
		$time = trim($_POST['time']);
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$phone = trim($_POST['phone']);
		$note = trim($_POST['note']);
		$staffId = $_POST['staffId'];
		$serviceId = $_POST['serviceId'];
		$duration = $_POST['duration'];

		try {
			//register customer
			$customerId = Customer::store($name, $phone, $email, $note);

			//make new appointment
			$appointmentId = Appointment::store($staffId, $serviceId, $time, $duration);

			//link customer and appointment
			Customer::link_appointment($customerId, $appointmentId);
			$response['success'] = 1;
		} catch(\Exception $e) {
			$response['error'] = -1;
			$response['message'] = $e->getMessage();
		}

		wp_send_json($customerId);
	}

}
