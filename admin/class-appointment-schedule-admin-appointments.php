<?php

class Appointment 
{
  const APPOINTMENT_TABLE = 'as_appointments';
  const SERVICE_TABLE = 'as_service';
  const STAFF_TABLE = 'as_staff_member';
  const CUSTOMER_TABLE = 'as_customers';
  const CUSTOMER_APPOINTMENT_TABLE = 'as_customer_appointments';

  public static function display()
  {
    ?>
      <div class="wrap" id="appointment-schedule">
        <h1>Appointments</h1>
        <div class="panel panel-default appointment-schedule-main appointment-schedule-customer">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-4 col-lg-1">
                <div class="form-group">
                  <input type="number" name="appointmentIdSearch" id="appointmentIdSearch" placeholder="No.">
                </div>
              </div>
              <div class="col-md-4 col-lg-3">

              </div>
              <div class="col-md-4 col-lg-2">
                <div class="form-group">
                  <select name="staff_search" id="staffSearch" placeholder="Employee">
                    <option></option>
                    <?php
                      $staffs = Staff_Member::get_staff_members();
                      foreach ($staffs as $staff) {
                    ?>
                    <option value="<?php echo $staff->id; ?>"><?php echo $staff->fullname; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4 col-lg-2">
                <select name="customer_search" id="customerSearch" placeholder="Customer">
                  <option></option>
                <?php
                  $customers = Customer::all();
                  foreach ($customers as $customer) {
                ?>
                  <option value="<?php echo $customer->id; ?>"><?php echo $customer->full_name; ?></option>
                <?php } ?>
                </select>
              </div>
              <div class="col-md-4 col-lg-2">
                <select name="service_search" id="serviceSearch" placeholder="Service">
                  <option></option>
                <?php
                  $services = Services::get_all();
                  foreach($services['services'] as $service) {
                ?>
                <option value="<?php echo $service->id; ?>"><?php echo $service->title; ?></option>
                <?php } ?>
                </select>
              </div>
              <div class="col-md-4 col-lg-2">
                <select name="status_search" id="statusSearch" placeholder="Status">
                  <option></option>
                  <option value="approved">Approved</option>
                  <option value="cancelled">Cancelled</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
            </div>
          </div>

            <div class="row">
              <table class="customer-table table table-striped dataTable no-footer dtr-inline" id="customerTable">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Appointment Date</th>
                    <th>Employee</th>
                    <th>Customer Name</th>
                    <th>Customer Phone</th>
                    <th>Customer Email</th>
                    <th>Service</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Payments</th>
                    <th>Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $appointments = self::all();
                    foreach($appointments as $appointment) {
                  ?>
                    <tr class="appointment-table-row" 
                        data-appointment-id="<?php echo $appointment->id; ?>" 
                        data-staff-id="<?php echo $appointment->staff_id; ?>" 
                        data-customer-id="<?php echo $appointment->customer_id; ?>" 
                        data-status="<?php echo $appointment->status; ?>"
                        data-service-id="<?php echo $appointment->fk_service_id; ?>"
                    >
                      <td><?php echo $appointment->id; ?></td>
                      <td><?php echo $appointment->start_date; ?></td>
                      <td><?php echo $appointment->staff_name; ?></td>
                      <td><?php echo $appointment->full_name; ?></td>
                      <td><?php echo $appointment->phone; ?></td>
                      <td><?php echo $appointment->email; ?></td>
                      <td><?php echo $appointment->title; ?></td>
                      <td><?php echo self::displayTime($appointment->duration); ?></td>
                      <td><?php echo $appointment->status; ?></td>
                      <td>$ <?php echo $appointment->price; ?></td>
                      <td><?php echo $appointment->notes; ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
		<?php
  }

  public static function all()
  {
    global $wpdb;
    $appointment_table = $wpdb->prefix . self::APPOINTMENT_TABLE;
    $customer_table = $wpdb->prefix . self::CUSTOMER_TABLE;
    $customer_appointment_table = $wpdb->prefix . self::CUSTOMER_APPOINTMENT_TABLE;
    $service_table = $wpdb->prefix . self::SERVICE_TABLE;
    $staff_table = $wpdb->prefix . self::STAFF_TABLE;

    $sql = "SELECT a.id, a.fk_staff_id, a.fk_service_id, a.start_date, a.end_date, ca.id, ca.fk_customer_id, ca.fk_appointment_id, ca.status, ca.notes, s.title, s.fk_category_id, s.duration, s.price, c.id AS customer_id, c.full_name, c.phone, c.email, sm.fullname AS staff_name, sm.id AS staff_id
            FROM $appointment_table a
            INNER JOIN $customer_appointment_table ca 
            ON ca.id = a.id
            INNER JOIN $service_table s
            on s.id = a.fk_service_id
            INNER JOIN $customer_table c
            ON c.id = ca.fk_customer_id
            INNER JOIN $staff_table sm
            ON sm.id = a.fk_staff_id";
    return $wpdb->get_results($sql);
  }

  public static function store ($staffId, $serviceId, $startTime, $duration)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::APPOINTMENT_TABLE;

    try {
      $wpdb->insert($table_name, ['fk_staff_id' => $staffId, 'fk_service_id' => $serviceId, 'start_date' => current_time('mysql'), 'end_date' => current_time('mysql'), 'created' => current_time('mysql')]);
      $response = $wpdb->insert_id;
    } catch (\Exception $e) {
      $response = $e->getMessage();
    }   

    return $response;
  }

  private static function displayTime($seconds)
  {
    $minutes = $seconds / 60;
    $mod = $minutes % 60;
    $hoursString = floor($minutes / 60) <= 1 ? ' hour ' : ' hours ';
    if ($mod == 0) {
      $time = $minutes / 60 . $hoursString;
    } else {
      $time = floor($minutes / 60) != 0 ? floor($minutes / 60) . $hoursString . $mod . ' minutes' : $mod . ' minutes';
    }
    return $time;
  }
}