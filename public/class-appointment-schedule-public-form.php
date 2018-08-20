<?php

class Appointment_Schedule_Form
{
  public static function display()
  {
    $staffId = 1;
    $serviceId = 1;
    $service = Services::get($serviceId)['service'];
    $staff = Staff_Member::get_staff($staffId)['staff'];
    ob_start();
    ?>
    <div class="appointment-schedule-form-holder">
      <h1>Schedule an Appointment</h1>
      <div class="spinner-holder">
          <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
        </div>
        <div class="appointment-schedule-progress-tracker appointment-schedule-table">
        <div class="appointment-schedule-progress appointment-schedule-progress-tracker-time active " id="timeTracker" data-panel="time">
          <p>1. Time</p>
          <div class="appointment-schedule-progress-tracker"></div>
        </div>
        <div class="appointment-schedule-progress appointment-schedule-progress-tracker-details" id="detailsTracker" data-panel="details">
          <p>2. Details</p>
          <div class="appointment-schedule-progress-tracker"></div>
        </div>
        <!-- <div class="appointment-schedule-progress appointment-schedule-progress-tracker-payment">
          <p>3. Payment</p>
          <div class="appointment-schedule-progress-tracker"></div>
        </div> -->
        <div class="appointment-schedule-progress appointment-schedule-progress-tracker-end" id="doneTracker" data-pane="done">
          <p>3. Done</p>
          <div class="appointment-schedule-progress-tracker"></div>
        </div>
      </div>
      <div class="appointment-schedule-step-time" id="timeStep">
        <div class="appointment-schedule-box">Below you can find a listof available time slots for <span class="service" id="service" data-service-id="<?php echo $service->id; ?>"><?php echo $service->title; ?></span> by <span class="service-staff" id="staff" data-staff-id="<?php echo $staff->id; ?>"><?php echo $staff->fullname; ?></span>. <br>	Click on a time slot to proceed with booking. 
        </div>      
        <div class="appointment-schedule-time-step-holder" >
        <?php
          self::dates($service->duration);
        ?>
        </div>
      </div>
      <div class="appointment-schedule-details-step" id="detailsStep">
        <div class="appointment-schedule-box">You selected a booking for <span class="service" id="service" data-service-id="<?php echo $service->id; ?>"><?php echo $service->title; ?></span> by <span class="service-staff" id="staff" data-staff-id="<?php echo $staff->id; ?>"><?php echo $staff->fullname; ?></span> at <span id="appointmentTime" data-time="" data-duration="<?php echo $service->duration; ?>">12:45 pm</span> on <span id="appointmentDate" data-date="">August 17, 2018</span>. The price for the service is <span id="servicePrice" data-price=""><?php echo $service->price; ?></span>. Please provide your details in the form below to proceed with booking. 
        </div>
      <?php
        self::details();
      ?>
      </div>
      <div class="appointment-schedule-done-step" id="doneStep">
        <p>Thank you! Your booking is complete. An email with details of your booking has been sent to you.</p>
      </div>
      <hr>
      <div class="appointment-schedule-btn-holder">
        <a href="#" class="buttons back-button" id="backBtn">Back</a>
        <a href="#" class="buttons next-button" id="nextBtn">Next</a>
      </div>
    </div>
    <?php
    //self::dates();
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }

  public static function dates($serviceDuration, $changed = 0, $fromAjax = 0)
  {
    if ($fromAjax == 1) {
      ob_start();
    }
    $dates = [];
    $year = (new \DateTime())->format('Y');
    $month = $changed != 0 ? (new \DateTime())->modify('+1 month')->format('m') : (new \DateTime())->format('m');
    $status = $changed != 0 ? 1 : 0;
    for ($i = 1; $i <= 31; $i++) {
      $wk = date('W', mktime(0, 0, 0, $month, $i, $year));
      $wkDay = date('D', mktime(0, 0, 0, $month, $i, $year));
      $day = date('d', mktime(0, 0, 0, $month, $i, $year));

      $dates[$month][$wk][$wkDay] = $day;
    }
    $weekdays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    foreach ($dates as $month => $weeks) { ?>
    <div class="appointment-schedule-month-holder">
      <div class="month-navigation mb-20">
      <?php
        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');
        $year = (new \DateTime())->format('Y');
        $today = (new \DateTime())->format('d');
        if ($status == 1) {
          echo '<span> <a href="#" id="changeMonth" data-status="' . $status . '"><i class="glyphicon glyphicon-chevron-left"></i></a></span>';
        }
        echo '<span class="appointment-schedule-month-name" id="month-' . $month . '" data-month-id="' . $month . '">' . $monthName . '</span> ';
        echo '<span class="appointment-schedule-year" id="year" data-year="' . $year . '">' . $year . '</span>';
        if ($status == 0) {
          echo '<span> <a href="#" id="changeMonth" data-status="' . $status  . '"><i class="glyphicon glyphicon-chevron-right"></i></a></span>';
        }
        ?>
      </div>
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
              $todayClass = (int)$days[$day] == (int)$today && $month == (new \DateTime())->format('m') ? 'today-date' : '';
              $activeDateClass = ((int)$days[$day] == (int)$today + 1) && $month == (new \DateTime())->format('m') ? 'active-date' : '';
              $tdClass = (isset($days[$day]) && (int)$days[$day] > (int)$today) || $month > (new \DateTime())->format('m')  ? 'date' : 'disabled';
              ?>
            <td class="<?php echo $tdClass . ' ' . $todayClass . ' ' . $activeDateClass; ?>" >
              <a href="#" class="date-link" id="<?php echo $month . '-' . $days[$day]; ?>"><?php echo isset($days[$day]) ? $days[$day] : '&nbsp'; ?></a>                
            </td>               
            <?php 
          } ?>
          </tr>
        <?php 
      } ?>
        </tbody>
      </table>     
    </div>
    <div class="appointment-schedule-time-step">      
      <div class="appointment-schedule-time-step-item appointment-schedule-time-step-date" id="scheduleDate" data-date="<?php echo (new \DateTime())->modify('+1 day')->format('d-m'); ?>"><?php echo (new \DateTime())->modify('+1 day')->format('D, M d'); ?></div>
      <?php echo self::scheduleListTime($serviceDuration); ?>
    </div>
    <?php 
    }
    if ($fromAjax == 1) {
      $response = ob_get_clean();
      ob_end_flush();
      return json_encode($response);
    }
  }

  public static function details() 
  {
    ?>
      <div class="appointment-schedule-box appointment-schedule-table">
        <div class="appointment-schedule-form-group">
          <input type="hidden" name="time" id="time" value="">
          <input type="hidden" name="date" id="date" value="">
            <label>Name</label>
            <div>
                <input class="appointment-schedule-full-name" id="name" type="text" value="" placeholder="Enter your name" required>
            </div>
            <div class="appointment-schedule-full-name-error appointment-schedule-label-error"></div>
        </div>
        <div class="appointment-schedule-form-group">
            <label>Phone</label>
            <div>
                <input class="appointment-schedule-phone" type="text" id="phone" value="" placeholder="Enter your phone num (eg. +xxxxxxxxx)" required>
            </div>
            <div class="appointment-schedule-phone-error appointment-schedule-label-error"></div>
        </div>
        <div class="appointment-schedule-form-group">
            <label>Email</label>
            <div>
                <input class="appointment-schedule-email" type="email" id="email" value="" placeholder="Enter your email address" required>
            </div>
            <div class="appointment-schedule-email-error appointment-schedule-label-error"></div>
        </div>
      </div>
      <div class="appointment-schedule-box">
          <div class="appointment-schedule-form-group">
              <label>Notes</label>
              <div>
                  <textarea id="note" class="appointment-schedule-user-notes" rows="3"></textarea>
              </div>
          </div>
      </div>
    <?php
  }

  private static function scheduleListTime($duration)
  {
    $startTime = '08:00';
    $endTime = '18:00';
    $html = '<div class="appointment-schedule-time-step-item appointment-schedule-time-step-time">
              <input type="radio" name="" id=""> <span>' . $startTime . '</span>
            </div>';
    $min = (int)explode(':', $startTime)[1];
    $hrs = (int)explode(':', $startTime)[0];

    $minutes = $duration / 60;
    $mod = $minutes % 60;
    $modifiedTime = $startTime;
    $hours = $hrs;
    $m = 0;
    if ($mod == 0) {
      $time = $minutes / 60;
      while ($hours < (int)explode(':', $endTime)[0]) {
        $hours += $time;
        if ($hours < (int)explode(':', $endTime)[0]){
          $html .= self::makeHtml($hours, $min);
        }
      }
    } else {
      $time = floor($minutes / 60) != 0 ? ((floor($minutes / 60) < 10) ? '0' . floor($minutes / 60) . ':' .  $mod : floor($minutes / 60) . ':' . $mod ) : '00:' .  $mod ;
      error_log(substr($time, 0, 2));
      while ($hours < (int)explode(':', $endTime)[0]) {
            $hours += (int)substr($time, 0, 2);
            error_log(__LINE__ . ':' . $hours);
        /* if (substr($time, 0, 2) == '00') { */
            if ($mod + $min == 60) {
              $hours += 1;
              $min = '00';         
            } else {
              if ($mod + $min > 60) {
                $min = $mod + $min - 60 ;
                $hours += 1;
              } else {
                $min += $mod;
              }
              error_log(__LINE__ . ':' . $hours);
              error_log(__LINE__ . ':' . $min);
            }
            if ($hours < (int)explode(':', $endTime)[0] && $min < 45) {
              $html .= self::makeHtml($hours, $min);
            }
          /* } else {
            error_log('prvi if');
            error_log(substr($time, 0, 2));
            error_log('');
          } */
        //die();
      } 
    }
    
    error_log('');
    return $html;

  }

  private static function makeHtml($hours, $min) 
  {
    $minutes = ($min === 0) ? $min . '0' : $min;
    $modifiedTime = ($hours < 10 ? '0' . $hours : $hours) . ':' . $minutes;
    return '<div class="appointment-schedule-time-step-item appointment-schedule-time-step-time">
              <input type="radio" name="" id=""> <span>' . $modifiedTime . '</span>
            </div>';
  }
}