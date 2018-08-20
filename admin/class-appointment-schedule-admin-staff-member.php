<?php

class Staff_Member {

	const STAFF_TABLE = 'as_staff_member';
	const STAFF_TO_SERVICE_TABLE = 'as_staff_to_service';

  public static function get_staff_members()
  {
    global $wpdb;

    $table_name = $wpdb->prefix . self::STAFF_TABLE;
    $prepare = "SELECT id, fullname, role FROM $table_name";
    return $wpdb->get_results($prepare);
  }

  public static function update_staff_details($fullname, $email, $phone, $info, $staffId)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::STAFF_TABLE;

    if ($fullname != '' || $email != '' || $phone != '' || $info != '') {
      $wpdb->update($table_name, [ 'fullname' => $fullname, 'email' => $email, 'phone' => $phone, 'info' => $info ], [ 'ID' => $staffId] );
      $response = ['success' => 1];
    } else {
      $response = ['error' => -1];
    }

    return $response;
  }

  public static function get_staff($staffId)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::STAFF_TABLE;

    if ($staffId > 0) {
      $response['success'] = 1;
      $prepare = "SELECT id, fullname, email, phone, info, role FROM $table_name WHERE id = $staffId";
      $response['staff'] = $wpdb->get_row($prepare);
    } else {
      $response = ['error' => -1];
    }
    return $response;
  }

  public static function display()
  {
    ?>
		<div class="wrap" id="appointment-schedule">
      <h1 class="section-title">Staff Members</h1>
      <div class="messages-holder">
        <div class="alert alert-success text-center success-message" id="success-msg">Data saved</div>
        <div class="alert alert-danger text-center error-message" id="error-msg">An error has occured while saving your data. Please try again</div>
      </div>
			<div class="row">
				<div class="col-sm-4">
					<?php 
    $staff_members = self::get_staff_members();
    if (!empty($staff_members)) {
      ?>
					<ul class="appointment-schedule-nav" id="appointment-schedule-staff-list">
						<?php foreach ($staff_members as $staff_member) { ?>
						<li class="appointment-schedule-nav-item" data-staff-id="<?php echo $staff_member->id; ?>">
							<div class="appointment-schedule-flexbox">
								<div class="appointment-schedule-flex-cell appointment-schedule-vertical-middle appointment-schedule-stuff-thumb">
									<div class="appointment-schedule-thumb"></div>
								</div>
								<div class="appointment-schedule-flex-cell appointment-schedule-vertical-middle">
									<?php echo $staff_member->fullname; ?>
								</div>
							</div>
						</li>
						<?php 
      } //end foreach ?>
					</ul>
					<?php 
    } //end if(!empty($staff_members)) ?>
					<div class="form-group appointment-schedule-submit-btn-holder">
						<a class="btn btn-xlg btn-block btn-success-outline" id="add-new-staff"><i class="dashicons dashicons-plus-alt"></i> New Staff Member</a>
						<div class="popover" id="staffPopover">
							<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="createNewStaffFrom">
								<div class="form-group">
									<label for="fullname" class="appointment-schedule-new-staff-fullname">Full name</label>
									<input class="form-control" id="appointment-schedule-new-staff-fullname" name="fullname" type="text" autofocus>
								</div>
								<hr>
								<div class="text-right">
									<input type="submit" class="btn btn-lg btn-success" id="createNewStaffSubmitBtn" name="create_staff" value="Save">
									<a href="#" class="btn btn-lg btn-danger" id="closeNewStaffForm">Close</a>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-sm-8" id="appointment-schedule-edit-staff">
          <div class="spinner-holder">
            <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
          </div>
					<div class="panel panel-default appointment-schedule-main">
						<div class="panel-body">
								<div class="appointment-schedule-flexbox mb-12">
									<div class="appointment-schedule-flex-cell">
										<div class="appointment-schedule-thumb appointment-schedule-thumb-lg mr-16"></div>
									</div>										
									<div class="appointment-schedule-flex-cell appointment-schedule-vertical-top">
										<h1 id="fullname-title"></h1>
										<input type="hidden" name="staff_id" id="staffId" value="">
									</div>
								</div>
								<ul class="nav nav-tabs nav-justified">
									<li><a href="#" id="details" class="tab-item active-tab"><i class="glyphicon glyphicon-info-sign"></i> Details</a></li>
									<li><a href="#" id="services" class="tab-item"><i class="glyphicon glyphicon-check"></i> Services</a></li>
									<li><a href="#" id="schedule" class="tab-item"><i class="glyphicon glyphicon-calendar"></i> Schedule</a></li>
									<li><a href="#" id="days-off" class="tab-item"><i class="glyphicon glyphicon-briefcase"></i> Days off</a></li>
								</ul>
								<!-- Details -->
								<div class="tab-pane" id="staff-details">
									<div class="appointment-schedule-details">
										<form action="#">
											<input type="hidden" name="staff_id" id="staff-id-pane" value="">
											<div class="form-group">
												<label for="fullname-pane">Full Name</label>
												<input type="text" name="staff_fullname" id="fullname-pane" class="form-control">
											</div>
											<div class="col-md-6 email-holder">
												<div class="form-group">
													<label for="email-pane">Email</label>
													<input type="text" name="staff_email" id="email-pane" class="form-control">
												</div>
											</div>
											<div class="col-md-6 phone-holder">
												<div class="form-group">
													<label for="phone-pane">Phone</label>
													<input type="text" name="staff_phone" id="phone-pane" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label for="info-pane">Info</label>
												<textarea name="staff_info" id="info-pane" cols="30" rows="5" class="form-control"></textarea>
											</div>
											<hr>
											<div class="form-group  btn-holder">
												<div class="text-left col-md-6">
													<a href="#" class="btn btn-danger" id="deleteStaff"><i class="glyphicon glyphicon-trash"></i> Delete</a>
												</div>
												<div class="text-right col-md-6">
													<a href="#" class="btn btn-success" id="saveStaffDetailsBtn">Save</a>
													<a href="#" class="btn btn-default" id="resetStaffDetailsBtn">Reset</a>
												</div>
											</div>
										</form>
									</div>
								</div>
								<!-- Services -->
								<div class="tab-pane none" id="staff-services">
									<div class="panel-heading appointment-schedule-services-category">
										<div class="row">
											<div class="col-lg-6">
												<label for="category">
													<input type="checkbox" name="category" id="category" data-category-id="1" class="appointment-schedule-category-checkbox"><b>All services</b>
												</label>
											</div>
											<div class="col-lg-6">
												<div class="appointment-schedule-flexbox">
													<div class="appointment-schedule-flex-row">
														<div class="appointment-schedule text-right">
															<div class="appointment-schedule-font-smaller appointment-schedule-color-gray">PRICE</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<ul class="appointment-schedule-category-services">
										<?php 
										$result = Services::get_all();
										foreach ($result['services'] as $service) {
										?>
										<li class="list-group-item">
											<div class="row">
												<div class="col-lg-6">
													<div class="checkbox">
														<label for="service-<?php echo $service->id; ?>">
															<input type="checkbox" name="service-<?php echo $service->id; ?>" id="service-<?php echo $service->id; ?>" class="appointment-schedule-category-checkbox" value="<?php echo $service->id; ?>" data-service-id="<?php echo $service->id; ?>"><span class="appointment-schedule-toggle-label" ><?php echo $service->title; ?></span>
														</label>
													</div>
												</div>
												<div class="col-lg-6">
													<div class="appointment-schedule-flexbox">
														<div class="appointment-schedule-flexbox-row">
															<input class="form-control text-right" type="text" disabled="disabled" name="price[1]" value="<?php echo $service->price; ?>">
														</div>
													</div>
												</div>
											</div>
										</li>
										<?php } ?>
                  </ul>
                  <hr>
                  <div class="text-right">
                    <a href="#" class="btn btn-success" id="saveStaffServicesBtn">Save</a>
                    <a href="#" class="btn btn-default">Reset</a>
                  </div>
								</div> <!-- end Services -->
								<!-- Schedule -->
								<div class="tab-pane none" id="staff-schedule">
									<div class="appointment-schedule-schedule-holder">
										<form action="">																										
											<?php Staff_Member_Schedule::display(); ?>
											<hr>
                      <div class="button-holder text-right">
                        <a href="#" class="btn btn-success" id="saveScheduleBtn" >Save</a>
                        <a href="#" class="btn btn-danger " id="closeScheduleBtn" >Reset</a>
                      </div>
										</form>
									</div>
								</div> <!-- end Schedule -->
								<!-- Days of -->
								<div class="tab-pane none" id="staff-days-off">
									<div class="appointment-schedule-schedule-holder">
										<div class="calendar-navigation-holder text-center mb-50">
											<div class="calendar-buttons">
												<a href="#" class="year-changer" id="prevYear" data-action="1">														
													<div class="prev-calendar-year"><i class="dashicons dashicons-arrow-left-alt2"></i></div>
												</a>												
												<div class="calendar-year" id="calendarYear">
													<?php echo (new \DateTime())->format('Y'); ?>
												</div>
												<a href="#" class="year-changer" id="nextYear" data-action="2">
													<div class="nex-calendar-year"><i class="dashicons dashicons-arrow-right-alt2"></i></div>												
												</a>
											</div>
										</div>	
										<div class="holiday-holder">																								
											<?php Holidays::display(); ?>
										</div>
											<hr>
									</div>
								</div> <!-- Days of -->
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
  }

  public static function createStaffMember($fullname) 
  {
    global $wpdb;
    if ($fullname != '') {
      $table_name = $wpdb->prefix . self::STAFF_TABLE;
      $wpdb->insert($table_name, ['fullname' => $fullname, 'role' => 'Staff member', 'created' => current_time('mysql')]);
    }
	}
	
	public static function unlink_staff_service($staffId) 
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::STAFF_TO_SERVICE_TABLE;
		try {
			$wpdb->delete($table_name, [ 'fk_staff_id' => $staffId]);
		} catch (\Exception $e) {
			error_log($e->getMessage());
		}
	}

	public static function link_staff_service($staffId, $servicesId) 
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::STAFF_TO_SERVICE_TABLE;
		try {
			foreach ($servicesId as $serviceId) {
				$wpdb->insert($table_name, [ 'fk_staff_id' => $staffId, 'fk_service_id' => $serviceId, 'created' => current_time('mysql')]);
			}
			$response['success'] = 1;
			
		} catch (\Exception $e) {
			$response['message'] = $e->getMessage();
			$response['error'] = -1;
		}
		
		return $response;
	}

}