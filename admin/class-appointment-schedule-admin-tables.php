<?php

class Tables 
{

  const STAFF_TABLE = 'as_staff_member';
  const SERVICE_TABLE = 'as_service';
  const CATEGORY_TABLE = 'as_category';
  const STAFF_TO_SERVICE_TABLE = 'as_staff_to_service';
  const STAFF_SCHEDULE_TABLE = 'as_staff_schedule_items';
  const SCHEDULE_BREAKS_TABLE = 'as_schedule_item_breaks';
  const HOLIDAYS_TABLE = 'as_holidays';
  const CUSTOMERS_TABLE = 'as_customers';
  const CUSTOMER_APPOINTMENT_TABLE = 'as_customer_appointments';
  const APPOINTMENT_TABLE = 'as_appointments';

  public static function create() 
  {
    global $wpdb;
    self::staff_member_table($wpdb);
    self::service_table($wpdb);
    self::category_table($wpdb);
    self::staff_to_service_table($wpdb);
    self::staff_schedule_table($wpdb);
    self::schedule_breaks_table($wpdb);
    self::holidays_table($wpdb);
    self::customers_table($wpdb);
    self::appointment_table($wpdb);
    self::customer_appointment_table($wpdb);
  }

  private static function staff_member_table($wpdb) 
  {
    $table_name = $wpdb->prefix . self::STAFF_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			fullname varchar(55) NOT NULL,
			role varchar(55) NOT NULL,
			email varchar(55) NULL DEFAULT NULL,
			phone varchar(50) NULL DEFAULT NULL,
			info varchar(255) NULL DEFAULT NULL,
			fk_attachment_id int(10) NULL DEFAULT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		);";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function service_table($wpdb) 
  {
    $table_name = $wpdb->prefix . self::SERVICE_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			title varchar(55) NOT NULL,
      fk_category_id mediumint(9) NULL DEFAULT NULL,
      duration int(11) NOT NULL DEFAULT 900,
      price decimal(10,2) NOT NULL DEFAULT 0.00,
      capacity_min int(11) NOT NULL DEFAULT 1,
      capacity_max int(11) NOT NULL DEFAULT 1,
      padding_left int(11) NOT NULL DEFAULT 1,
      padding_right int(11) NOT NULL DEFAULT 1,
      info text NULL DEFAULT NULL,
      appointments_limit int(11) NULL DEFAULT NULL,
      visibility SMALLINT NOT NULL DEFAULT '0',
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		);";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function category_table($wpdb) 
  {
    $table_name = $wpdb->prefix . self::CATEGORY_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
      position int(11) NOT NULL DEFAULT 9999,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		);";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function staff_to_service_table($wpdb) 
  {
    $table_name = $wpdb->prefix . self::STAFF_TO_SERVICE_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      fk_staff_id int(11) NOT NULL,
      fk_service_id int(11) NOT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function staff_schedule_table($wpdb) 
  {
    $table_name = $wpdb->prefix . self::STAFF_SCHEDULE_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      fk_staff_id int(11) NOT NULL,
      day_index SMALLINT NOT NULL,
      start_time TIME NULL DEFAULT NULL,
      end_time TIME NULL DEFAULT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function schedule_breaks_table($wpdb)
  {
    $table_name = $wpdb->prefix . self::SCHEDULE_BREAKS_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      fk_schedule_id int(11) NOT NULL,
      start_time TIME NULL DEFAULT NULL,
      end_time TIME NULL DEFAULT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function holidays_table($wpdb)
  {
    $table_name = $wpdb->prefix . self::HOLIDAYS_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      fk_staff_id int(11) NOT NULL,
      `date` DATE NOT NULL,
      `repeat` tinyint(1) NULL DEFAULT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function customers_table($wpdb)
  {
    $table_name = $wpdb->prefix . self::CUSTOMERS_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      full_name VARCHAR(255) NOT NULL,
      first_name VARCHAR(255) NULL DEFAULT NULL,
      last_name VARCHAR(255) NULL DEFAULT NULL,
      phone VARCHAR(255) NULL DEFAULT NULL,
      email VARCHAR(255) NULL DEFAULT NULL,
      notes VARCHAR(255) NULL DEFAULT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function appointment_table($wpdb)
  {
    $table_name = $wpdb->prefix . self::APPOINTMENT_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      fk_staff_id int(11) NOT NULL,
      fk_service_id int(11) NOT NULL,
			start_date datetime NOT NULL,
			end_date datetime NOT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private static function customer_appointment_table($wpdb)
  {
    $table_name = $wpdb->prefix . self::CUSTOMER_APPOINTMENT_TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      fk_customer_id int(11) NOT NULL,
      fk_appointment_id int(11) NOT NULL,
      notes text NULL DEFAULT NULL,
      status varchar(50) NOT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

}