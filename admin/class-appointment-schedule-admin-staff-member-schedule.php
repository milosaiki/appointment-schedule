<?php

class Staff_Member_Schedule 
{

  const STAFF_SCHEDULE_TABLE = 'as_staff_schedule_items';
  const SCHEDULE_BREAK_TABLE = 'as_schedule_item_breaks';

  public static function display()
  {
    $days = [ 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday' ];

    foreach ($days as $key => $day) {
    ?>
    <div class="staff-schedule-item-row panel panel-default appointment-schedule-panel-unborder">
      <div class="panel-heading appointment-schedule-padding-vertical-md">
        <div class="row">
          <div class="col-sm-7 col-lg-5">
            <span class="panel-title"><?php echo $day; ?></span>
            <input type="hidden" name="schedule_staff" id="sheduleDay-<?php echo $key ?>" value="">
          </div>
          <div class="col-sm-5 col-lg-7 hidden-xs hidden-sm">
            <div class="appointment-schedule-font-smaller appointment-schedule-color-gray">Breaks</div>
          </div>
        </div>
      </div>
      <div class="panel-body padding-lr-none">
        <div class="row">
          <div class="col-sm-7 col-lg-5">
            <div class="appointment-schedule-flexbox">
              <div class="appointment-schedule-flex-cell" style="width: 50%">
                <select name="start_time[<?php echo $key; ?>]" data-default_value="08:00:00" class="working-schedule-start form-control" id="startTime-<?php echo $key; ?>">
                  <?php
                    echo self::get_time('from');
                  ?>
                </select>
              </div>
              <div class="appointment-schedule-flex-cell text-center" style="width: 1%">
                <div class="appointment-schedule-margin-horizontal-lg appointment-schedule-hide-on-off"> to  </div>
              </div>
              <div class="appointment-schedule-flex-cell" style="width: 50%">
                <select name="end_time[<?php echo $key; ?>]" data-default_value="18:00:00" class="working-schedule-end form-control appointment-schedule-hide-on-off" id="endTime-<?php echo $key; ?>">
                  <?php
                    echo self::get_time('to');
                  ?>
                </select>
              </div>
            </div>
          </div>
          <div class="col-sm-5 col-lg-7">
            <div class="appointment-schedule-intervals-wrapper appointment-schedule-hide-on-off">
              <a href="#" class="btn btn-link appointment-schedule-btn-unborder appointment-schedule-margin-vertical-screenxs-sm add-break-btn" data-day-index="<?php echo $key; ?>" > add break </a>
                <div class="popover popover-schedule" id="popover-<?php echo $key; ?>" style="top: 35px; left: -107.234px; "><div class="popover-arrow"></div><h3 class="popover-title" style="display: none;"></h3>
                  <div class="popover-content">
                    <div class="appointment-schedule-schedule-form">
                      <div class="appointment-schedule-flexbox" style="width: 260px">
                        <div class="appointment-schedule-flex-cell" style="width: 48%;">
                          <select class="break-start form-control" name="break_from" id="breakFrom-<?php echo $key; ?>">
                            <?php echo self::get_time('from'); ?>
                          </select>
                        </div>
                        <div class="appointment-schedule-flex-cell " style="width: 4%">
                          <div class="appointment-schedule-margin-horizontal-lg"> to  </div>
                        </div>
                        <div class="appointment-schedule-flex-cell" style="width: 48%;">
                          <select class="break-start form-control" name="break_to" id="breakTo-<?php echo $key; ?>">
                            <?php echo self::get_time('to'); ?>
                          </select>
                        </div>
                      </div>
                      <hr>
                      <div class="button-holder text-center">
                        <a href="#" class="btn btn-success save-break" id="saveBreakBtn-<?php echo $key; ?>" data-day-index="<?php echo $key; ?>">Save</a>
                        <a href="#" class="btn btn-danger close-break" id="closeBreakBtn-<?php echo $key; ?>" data-day-index="<?php echo $key; ?>">Close</a>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php } 
  }

  private static function get_time($type)
  {
    $start = "00:00:00"; //you can write here 00:00:00 but not need to it
    $end = "23:45:00";

    $tStart = strtotime($start);
    $tEnd = strtotime($end);
    $tNow = $tStart;
    $timeOptions = '';
    while ($tNow <= $tEnd) {
      $selected = '';
      $disabled = '';
      $time = date("H:i:s", $tNow);
      if ( $type == 'from' ) {
        $selected = $time == '08:00:00' ? 'selected' : '';
        $hidden = $time < '08:00:00' ? 'style="display: none;"' : '';
      } else {
        $selected = $time == '18:00:00' ? 'selected' : '';
      }
      $timeOptions .= '<option value="' . $time . '" ' . $selected . ' ' . $hidden . '>' . $time . '</option>';
      $tNow = strtotime('+15 minutes', $tNow);
    }
    return $timeOptions;
  }

  public static function save($staffId, $schedule)
  {
    global $wpdb;
    $table_name = $wpdb->prefix. self::STAFF_SCHEDULE_TABLE;
    try {
      self::remove_old($staffId, $wpdb, $table_name);
      foreach ($schedule as $key => $v) {
        $wpdb->insert($table_name, ['fk_staff_id' => $staffId, 'day_index' => $key, 'start_time' => $v['start_time'], 'end_time' => $v['end_time'], 'created' => current_time( 'mysql' )]);
      }
      $response['success'] = 1;
    } catch (\Exception $e) {
      $response['message'] = $e->getMessage();
      $response['error'] = -1;
    }

    return $response;
  }

  private static function remove_old($staffId, $wpdb, $table_name)
  {
    $wpdb->delete($table_name, [ 'fk_staff_id' => $staffId]);
  }

  public static function get($staffId) 
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::STAFF_SCHEDULE_TABLE;
    $sql = "SELECT id, fk_staff_id, day_index, start_time, end_time FROM $table_name WHERE fk_staff_id = $staffId";
    return $wpdb->get_results($sql);
  }

  public static function save_break($scheduleId, $breakFrom, $breakTo) 
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::SCHEDULE_BREAK_TABLE;
    try {
        $wpdb->insert($table_name, [ 'fk_schedule_id' => $scheduleId, 'start_time' => $breakFrom, 'end_time' => $breakTo, 'created' => current_time('mysql')]);
        $response['success'] = 1;
        $response['breakId'] = $wpdb->insert_id;    
    } catch (\Exception $e) {
      $response['error'] = -1;
      $response['message'] = $e->getMessage();
    }

    return $response;
  }
}