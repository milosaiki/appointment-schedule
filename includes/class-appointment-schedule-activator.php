<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com/
 * @since      1.0.0
 *
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Appointment_Schedule
 * @subpackage Appointment_Schedule/includes
 * @author     Milos Zivic <milos.zivic@simplicity.rs>
 */
class Appointment_Schedule_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_role('staff_member', 'Staff member', array('read' => true, 'level_0' => true));
	}

}
