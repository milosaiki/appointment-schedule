<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com/
 * @since      1.0.0
 *
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/includes
 * @author     Milos Zivic <milos.zivic@simplicity.rs>
 */
class Appointment_Schedule_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'appointment-schedule',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
