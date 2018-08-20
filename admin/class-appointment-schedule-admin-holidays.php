<?php

class Holidays 
{

  const HOLIDAYS_TABLE = 'as_holidays';

  public static function display()
  {
    self::getDates((new \DateTime())->format('Y'));
  }

  public static function getDates($year)
  {
    $dates = [];

    for ($i = 1; $i <= 366; $i++) {
      $month = date('m', mktime(0, 0, 0, 1, $i, $year));
      $wk = date('W', mktime(0, 0, 0, 1, $i, $year));
      $wkDay = date('D', mktime(0, 0, 0, 1, $i, $year));
      $day = date('d', mktime(0, 0, 0, 1, $i, $year));

      $dates[$month][$wk][$wkDay] = $day;
    }
    return self::makeCalendar($dates, $year);
  }

  private static function makeCalendar($dates, $year) 
  {
    $weekdays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'); ?>
 
    <?php foreach ($dates as $month => $weeks) { ?>
    <div class="month-holder">
      <p class="month-name">
        <?php
        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');
        echo $monthName;
        ?>
      </p>
      <table>
        <thead>
          <tr>
            <th><?php echo implode('</th><th>', $weekdays); ?></th>
          </tr> 
        </thead>
        <tbody>
        <?php foreach ($weeks as $week => $days) { ?>
          <tr>
            <?php foreach ($weekdays as $day) { 
              $tdClass = isset($days[$day]) ? 'date' : 'disabled';
            ?>
            <td class="<?php echo $tdClass; ?>" >
                <a href="#" id="<?php echo $year . '-' . $month . '-' . $days[$day]; ?>"><?php echo isset($days[$day]) ? $days[$day] : '&nbsp'; ?></a>
                <div class="popover popover-holiday holiday-holder-form" id="holiday-<?php echo $year . '-' . $month . '-' . $days[$day]; ?>">
									<form id ="holidayForm"> 
										<input type="hidden" id="holidayDate" value="<?php echo $year . '-' . $month . '-' . $days[$day]; ?>">
										<div class="checkbox appointment-schedule-margin-bottom-md">
											<label for="dayOff">
												<input type="checkbox" name="day_off" id="dayOff-<?php echo $year . '-' . $month . '-' . $days[$day]; ?>" data-date="<?php echo $year . '-' . $month . '-' . $days[$day]; ?>" data-type="day-off">
												<span class="appointment-schedule-toggle-label">We are not working on this day</span>
          						</label>
        						</div>
										<div class="checkbox appointment-schedule-margin-bottom-md">
                      <label for="everyYear">
												<input type="checkbox" name="every_year" id="everyYear-<?php echo $year . '-' . $month . '-' . $days[$day]; ?>" disabled data-type="repeat" data-date="<?php echo $year . '-' . $month . '-' . $days[$day]; ?>">
												<span class="appointment-schedule-toggle-label">Repeat every year</span>
          						</label>
        						</div>
										<hr>
										<div class="text-right">
											<a href="#" class="btn btn-danger " id="closeHolidayPopoverBtn" >Close</a>
										</div>
      						</form>
                </div>
              </td>               
            <?php } ?>
          </tr>
        <?php } ?>
        </tbody>
      </table>     
    </div>
    <?php } ?>
    
    <?php
  }

  public static function store($staffId, $date, $repeat, $store) 
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::HOLIDAYS_TABLE;
    error_log($repeat);
    error_log($store);
    try {
      if ($repeat == 0 && $store == 1) {
        $wpdb->insert($table_name, ['fk_staff_id' => $staffId, 'date' => $date, 'repeat' => $repeat, 'created' => current_time('mysql')]);
        $response['action'] = 'insert';
      } else {
        $wpdb->update($table_name, ['repeat' => $repeat], ['fk_staff_id' => $staffId, 'date' => $date]);
        $response['action'] = 'update';
      }
      $response['success'] = 1;
    } catch (\Exception $e) {
      $response['error'] = -1;
      $response['message'] = $e->getMessage();
    }

    return $response;
  }

  public static function unlink($staffId, $date)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::HOLIDAYS_TABLE;

    try {
      $response['success'] = 1;
      $response['action'] = 'delete';
      $wpdb->delete($table_name, [ 'fk_staff_id' => $staffId, 'date' => $date]);
    } catch(\Exception $e) {
      $response['error'] = -1;
      $response['message'] = $e->getMessage();
    }

    return $response;
  }

  public static function get($staffId)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::HOLIDAYS_TABLE;

    $sql = "SELECT `id`, `fk_staff_id`, `date`, `repeat` FROM $table_name WHERE `fk_staff_id` = $staffId";
    return $wpdb->get_results($sql);
  }

}