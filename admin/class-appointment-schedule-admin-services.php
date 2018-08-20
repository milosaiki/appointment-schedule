<?php

class Services {

	const SERVICE_TABLE = 'as_service';
	const STAFF_TO_SERVICE = 'as_staff_to_service';
  
  public static function display() {
    ?>
		<div class="wrap" id="appointment-schedule">
      <h1 class="section-title"> Services </h1>
      <div class="messages-holder">
        <div class="alert alert-success text-center success-message" id="success-msg">Data saved</div>
        <div class="alert alert-success text-center error-message" id="error-msg">An error has occured while saving your data. Please try again</div>
      </div>
			<div class="row">
				<div class="col-sm-4">
					<ul class="appointment-schedule-nav" id="appointment-schedule-category-list">
						<li class="appointment-schedule-nav-item appointment-schedule-category-nav-item" >
							<div class="appointment-schedule-flexbox">
								<div class="appointment-schedule-flex-cell appointment-schedule-vertical-middle appointment-schedule-category-name" data-category-id="0">
                  All Services
								</div>
							</div>
            </li>            
            <?php 
              $categories = Category::get_all();
              foreach ($categories as $category) {
            ?>
            <li class="appointment-schedule-nav-item appointment-schedule-category-nav-item" id="category-nav-item-<?php echo $category->id; ?>" data-category-id="<?php echo $category->id; ?>">
							<div class="appointment-schedule-flexbox">
								<div class="row">
									<div class="col-md-9">
										<div class="appointment-schedule-flex-cell appointment-schedule-vertical-middle appointment-schedule-category-name" data-category-id="<?php echo $category->id; ?>">
											<?php echo $category->name; ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="col-md-6"><a href="#" class="edit-category" data-category-id="<?php echo $category->id; ?>"><i class="glyphicon glyphicon-edit"></i></a></div>
										<div class="col-md-6"><a href="#" class="delete-category" data-category-id="<?php echo $category->id; ?>"><i class="glyphicon glyphicon-trash"></i></a></div>
									</div>
								</div>
							</div>
            </li> 
            <?php } ?>
					</ul>					
					<div class="form-group appointment-schedule-submit-btn-holder">
						<a class="btn btn-xlg btn-block btn-success-outline" id="add-new-category"><i class="dashicons dashicons-plus-alt"></i> New Category</a>
						<div class="popover" id="categoryPopover">
							<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="createNewCategoryFrom">
								<div class="form-group">
									<label for="fullname" class="appointment-schedule-new-category-name">Category name</label>
									<input class="form-control" id="appointment-schedule-new-category-name" name="category_name" type="text" autofocus>
								</div>
								<hr>
								<div class="text-right">
									<input type="submit" class="btn btn-lg btn-success" id="createNewCategorySubmitBtn" name="create_category" value="Save">
									<a href="#" class="btn btn-lg btn-danger" id="closeNewCategoryForm">Close</a>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-sm-8" id="appointment-schedule-edit-category">
					<div class="panel panel-default appointment-schedule-main">
            <div class="panel-body">
								<div class="appointment-schedule-flexbox mb-12">										
									<div class="appointment-schedule-category-header-holder appointment-schedule-vertical-top">
										<h1 id="category-title" class="category-title"></h1>
										<a href="#" class="btn btn-success" id="open-service-form"><i class="dashicons dashicons-plus-alt"></i> Add Service</a>
									</div>
									<p class="no-service-notification">No services found. Please add services.</p>
								</div>
								<div id="services-list-holder">
									<div class="panel-group appointment-schedule-service-list" id="services-list">
										
									</div>
								</div>
								<div class="service-form-holder">
									<div class="spinner-holder">
										<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
									</div>
									<span class="close-form-btn"><i class="glyphicon glyphicon-remove"></i></span>
									<form id="service-form">
										<input type="hidden" name="category_id" id="categoryId" value="">
										<input type="hidden" name="service_id" id="serviceId" value="">
										<div class="form-group">
											<label for="serviceTitle">Title</label>
											<input type="text" class="form-control" id="serviceTitle" name="service_title">
										</div>
										<div class="row">
											<div class="col-lg-6">
												<div class="form-group" id="visibility-data-holder">
													<label for="serviceVisibility">Visibility </label> <i class="glyphicon glyphicon-question-sign" id="showVisibilityText"></i>
													<br>
													<small class="form-extra-data" id="visibilityExtraData">To make service invisible to your customers set the visibility to "Private".</small>
													<select name="service_visibility" id="serviceVisibility" class="form-control">
														<option value="0">Public</option>
														<option value="1">Private</option>
													</select>
												</div>
											</div>
											<div class="col-lg-6">
												<div class="form-group col-lg-6">
													<label for="servicePrice">Price</label>
													<input type="number" name="service_price" id="servicePrice" value="0.00" step=0.1 class="form-control">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-3">
												<div class="form-group">
													<label for="serviceDuration">Duration</label>
													<select class="form-control" name="service_duration" id="serviceDuration">
														<?php
															$i = 900;
															while ( $i <= 43200 ) {
																echo '<option value="' . $i . '">' . self::displayTime($i) . '</option>';															
																$i = $i + 900;
															}
															$j = 86400;
															while ( $j <= 604800) {
																echo '<option value="' . $j . '">' . self::displayDays($j) . '</option>';	
																$j = $j + 86400;
															}
														?>
													</select>
												</div>
											</div>
											<div class="col-lg-9">
												<div class="form-group">
													<label for="servicePaddingBefore">Padding time (before and after)</label> <i class="glyphicon glyphicon-question-sign" id="showPaddingText"></i>
													<br>
													<small class="form-extra-data" id="paddingExtraData">Set padding time before and/or after an appointment. For example, if you require 15 <br> minutes to prepare for the next appointment then you should set "padding before" to 15 <br> min. If there is an appointment from 8:00 to 9:00 then the next available time slot will be <br> 9:15 rather than 9:00.</small>
													<div class="col-lg-6">
														<select name="service_padding_before" id="servicePaddingBefore" class="form-control">
															<option value="1">OFF</option>
															<?php
																$i = 900;
																while ($i <= 86400) {
																	echo '<option value="' . $i . '">' . self::displayTime($i) . '</option>';
																	$i = $i + 900;
																}
															?>
														</select>
													</div>
													<div class="col-lg-6">
														<select name="service_padding_after" id="servicePaddingAfter" class="form-control">
															<option value="1">OFF</option>
															<?php
																$i = 900;
																while ($i <= 86400) {
																	echo '<option value="' . $i . '">' . self::displayTime($i) . '</option>';
																	$i = $i + 900;
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													<label for="category">Category</label>
													<select name="category" id="category" class="form-control">
														<option value="0">Uncategorized</option>
														<?php foreach($categories as $category) { ?>
																<option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-lg-6">
												<div class="form-group">
													<label for="staffMember">Provider</label>
													<select name="staff_member" id="staffMember" class="form-control">
														<option value="0">No staff selected</option>
														<?php 
															$staffMembers = Staff_Member::get_staff_members();
															foreach ($staffMembers as $staffMember) { ?>
																<option value="<?php echo $staffMember->id; ?>"><?php echo $staffMember->fullname; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<label for="serviceLimit">Limit appointments per customer</label> <i class="glyphicon glyphicon-question-sign" id="showLimitText"></i>
												<br>
												<small class="form-extra-data" id="limitExtraData">Allows you to limit the frequency of service bookings per customer.</small>
												<div class="col-lg-6">
													<input type="number" name="service_limit" id="serviceLimit" class="form-control">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<label for="serviceInfo">Info</label>
												<textarea name="service_info" id="serviceInfo" cols="30" rows="5" class="form-control"></textarea>
											</div>
										</div>
										<hr>
										<div class="text-right">
											<a href="#" class="btn btn-success" id="saveServiceBtn">Save</a>
											<a href="#" class="btn btn-default">Reset</a>
										</div>
									</form>
								</div>
              </div>
          </div>
        </div>
      </div>
    </div>
    <?php
	}
	
	public static function get_all() 
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::SERVICE_TABLE;
		try {
			$sql = "SELECT id, title, fk_category_id, duration, price, capacity_min, capacity_max, padding_left, padding_right, info, appointments_limit, visibility
							FROM $table_name";
			$response['services'] = $wpdb->get_results($sql);
			$response['success'] = 1;
		} catch (\Exception $e) {
			$response['error'] = -1;
			$response['message'] = $e->getMessage();
		}
		return $response;
	}

	public static function get_services_by_cat_id($catId) 
	{
		global $wpdb;
		$service_table = $wpdb->prefix . self::SERVICE_TABLE;
		$category_table = $wpdb->prefix . CATEGORY::CATEGORY_TABLE;
		try {
			$sql = "SELECT s.id, s.title, s.fk_category_id, s.duration, s.price, c.name
							FROM $service_table s
							INNER JOIN $category_table c
							ON c.id = s.fk_category_id
							WHERE s.fk_category_id = $catId";
			$response['services'] = $wpdb->get_results($sql);
			$response['success'] = 1;
		} catch (\Exception $e) {
			$response['error'] = -1;
			$response['message'] = $e->getMessage();
		}
		return $response;
	}

	public static function store($title, $visibility, $price, $duration, $paddingBefore, $paddingAfter, $categoryId, $limit, $info, $serviceId = 0)
	{
		global $wpdb;
		try {
			$table_name = $wpdb->prefix . self::SERVICE_TABLE;
			if ($serviceId > 0 ) {
					$wpdb->update($table_name, ['title' => $title, 'fk_category_id' => $categoryId > 0 ? $categoryId : null, 'duration' => $duration, 'price' => $price, 'info' => $info, 'appointments_limit' => $limit, 'visibility' => $visibility], ['ID' => $serviceId]);
			} else {
					$wpdb->insert($table_name, ['title' => $title, 'fk_category_id' => $categoryId > 0 ? $categoryId : null, 'duration' => $duration, 'price' => $price, 'info' => $info, 'appointments_limit' => $limit, 'visibility' => $visibility, 'created' => current_time('mysql')]);
					return $wpdb->insert_id;
			}
			return $serviceId;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public static function service_to_staff($serviceId, $staffId) 
	{
		global $wpdb;
		try {
			$table_name = $wpdb->prefix . SELF::STAFF_TO_SERVICE;
			$wpdb->insert($table_name, [ 'fk_staff_id' => $staffId, 'fk_service_id' => $serviceId, 'created' => current_time('mysql') ] );
		} catch (\Exception $e) {
			error_log($e->getMessage());
		}
	}

	public static function get($serviceId) 
	{
		global $wpdb;
		$service_table = $wpdb->prefix . self::SERVICE_TABLE;
		$staff_to_service = $wpdb->prefix . SELF::STAFF_TO_SERVICE;;
		try {
			$sql = "SELECT id, title, fk_category_id, duration, price, capacity_min, capacity_max, padding_left, padding_right, info, appointments_limit, visibility FROM $service_table WHERE id = $serviceId";
			$sql1 = "SELECT fk_staff_id FROM  $staff_to_service WHERE fk_service_id = $serviceId ";
			$response['success'] = 1;
			$response['service'] = $wpdb->get_row($sql);
			$response['staffId'] = $wpdb->get_row($sql1);
		} catch (\Exception $e) {
			$response['error'] = -1;
			$response['message'] = $e->getMessage();
		}
		return $response;
	}

	public static function delete($serviceId)
	{
		global $wpdb;
		$service_table = $wpdb->prefix . self::SERVICE_TABLE;
		$staff_to_service = $wpdb->prefix . self::STAFF_TO_SERVICE;
		
		try {
			$wpdb->delete( $service_table, ['ID' => $serviceId] );
			$wpdb->delete( $staff_to_service, ['fk_service_id' => $serviceId] );
			$response['success'] = 1;
		} catch (\Exception $e) {
			$response['message'] = $e->getMessage();
			$response['error'] = -1;
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

	private static function displayDays($seconds) {
		$dayString = $seconds / 86400 == 1 ? ' day' : ' days';
		return $seconds / 86400 < 7 ? $seconds / 86400 . $dayString : '1 week';
	}

	public static function get_staff_to_services($staffId)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . self::STAFF_TO_SERVICE;
		$sql = "SELECT fk_service_id FROM $table_name WHERE fk_staff_id = $staffId";
		return $wpdb->get_results($sql);
	}

}