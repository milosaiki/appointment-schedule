<?php

class Customer 
{
  const CUSTOMER_TABLE = 'as_customers';
  const CUSTOMER_APPOINTMENT_TABLE = 'as_customer_appointments';

  public static function display()
  {
    ?>
			<div class="wrap">
        <h1 class="mb-50">Custormers</h1>
        <div class="panel panel-default appointment-schedule-main appointment-schedule-customer">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-4 search-field-holder">
                <div class="form-group">
                  <input type="text" name="customer" id="searchCustomer" placeholder="Search customer" class="search-customer form-control">
                </div>
              </div>
            </div>

            <div class="row">
              <table class="customer-table table table-striped dataTable no-footer dtr-inline" id="customerTable">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Notes</th>
                    <th>Last Appointments</th>
                    <th>Total Appointments</th>
                    <th>Payments</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $customers = self::all();
                    foreach ($customers as $customer) {
                  ?>
                      <tr class="customer-table-row">
                        <td class="customer-name"><?php echo $customer->first_name ?></td>
                        <td><?php echo $customer->full_name ?></td>
                        <td><?php echo $customer->phone ?></td>
                        <td><?php echo $customer->email ?></td>
                        <td><?php echo $customer->notes ?></td>
                        <td>poslednji zakazani</td>
                        <td>1</td>
                        <td>$0.00</td>
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
    $table_name = $wpdb->prefix .  self::CUSTOMER_TABLE;

    $sql = "SELECT id, full_name, first_name, last_name, phone, email, notes FROM $table_name";
    return $wpdb->get_results($sql);
  }

  public static function store($name, $phone, $email, $note = '')
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::CUSTOMER_TABLE;

    $customer = self::get($email);
    if (!$customer) {
      $firstname = explode(' ', $name)[0];
      $lastname = isset(explode(' ', $name)[1]) ? explode(' ', $name)[1]: '';
      $note = $note == '' ? NULL : $note;
      try {
        $wpdb->insert($table_name, ['full_name' => $name, 'first_name' => $firstname, 'last_name' => $lastname, 'phone' => $phone, 'email' => $email, 'notes' => $note, 'created' => current_time('mysql')]);
      } catch (\Excetion $e) {
        return $e->getMessage();
      }
    }

    return !$customer ? $wpdb->insert_id : $customer->id;
  }

  public static function get($email) 
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::CUSTOMER_TABLE;

    $sql = "SELECT id FROM $table_name WHERE email = '$email'";
    return $wpdb->get_row($sql);
  }

  public static function link_appointment($customerId, $appointmentId)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::CUSTOMER_APPOINTMENT_TABLE;

    $wpdb->insert($table_name, ['fk_customer_id' => $customerId, 'fk_appointment_id' => $appointmentId, 'status' => 'aproved', 'created' => current_time('mysql')]);
  }
}