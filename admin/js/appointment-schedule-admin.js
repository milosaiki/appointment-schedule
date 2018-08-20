(function( $ ) {
	'use strict';

	$(document).ready(function () {

		var addNewStaffBtn = $('#add-new-staff');
		addNewStaffBtn.on('click', function(e) {
			e.preventDefault();
			$('#staffPopover').slideToggle();
		});

		var closeNewStaffFormBtn = $('#closeNewStaffForm');
		closeNewStaffFormBtn.on('click', function(e) {
			e.preventDefault();
			$('#staffPopover').slideUp();
			$('#createNewStaffFrom')[0].reset();
		});

		$('.appointment-schedule-nav-item').on('click', function() {
			$('.appointment-schedule-nav-item').removeClass('active');
			$(this).addClass('appointment-schedule-nav-item active');
			var staffId = $(this).data('staff-id');
			if (staffId > 0) {
				var data = {
					staffId: staffId,
					action: 'select_staff'
				}
				$.ajax({
					type: 'POST',
					url: appointment_schedule_ajax.ajax_url,
					data: data,
					success: function(result) {
						console.log(result);
						if (result.success == 1) {
							$('#appointment-schedule-edit-staff').show();
							$('#fullname-pane').val(result.staff.fullname);
							$('#email-pane').val(result.staff.email);
							$('#phone-pane').val(result.staff.phone);
							$('#info-pane').val(result.staff.info);
							$('#staff-id-pane').val(result.staff.id);
							$('#fullname-title').text(result.staff.fullname);
							$('#staffId').val(staffId);
							if (result.staff_to_services.length > 0) {
								result.staff_to_services.forEach( service => {									
									$('#service-' + service.fk_service_id).prop('checked', true);
								} );
							}
							if (result.staff_schedule.length > 0) {
								for (var i = 0; i < 7; i++) {
									var dayIndex = result.staff_schedule[i].day_index
									$('#sheduleDay-' + dayIndex).val(result.staff_schedule[i].id);
									$('#startTime' + dayIndex).val(result.staff_schedule[i].start_time);
									$('#endTime' + dayIndex).val(result.staff_schedule[i].end_time);
								}
							}

							if (result.holidays.length > 0) {
								result.holidays.forEach( holiday => {
									$('#' + holiday.date).addClass('holiday');
									$('#dayOff-' + holiday.date).prop('checked', true);
									$('#everyYear-' + holiday.date).prop('disabled', false);
									if (holiday.repeat == 1) {
										$('#everyYear-' + holiday.date).prop('checked', true);
									}
								});
							}
						}
					}
				});
			}
		});

		$('#saveStaffDetailsBtn').on('click', function(e) {
			e.preventDefault();
			$('#error-msg').hide();
			$('#success-msg').hide();
			$('.spinner-holder ').show();
			var fullname = $('#fullname-pane').val();
			var email = $('#email-pane').val();
			var phone = $('#phone-pane').val();
			var info = $('#info-pane').val();
			var staffId = $('#staff-id-pane').val();
			
			var data = {
				staffId: staffId,
				fullname: fullname,
				phone: phone,
				email: email,
				info: info,
				action: 'save_staff_details'
			}

			$.ajax({
				type: 'post',
				url: appointment_schedule_ajax.ajax_url,
				data: data, 
				success: function(result) {
					$('.spinner-holder').hide();
					if (result.success == 1) {
						$('#success-msg').show();
						setTimeout(function() {
							$('#success-msg').hide();
						}, 3000);
					} else {
						$('#error-msg').show();
					}
				}
			});
		});



		$(document).on('click', '.tab-item', function (e) {
			e.preventDefault();
			$('.tab-item').removeClass('active-tab');
			$(this).addClass('active-tab');
			var elementId = $(this).attr('id');
			$('.tab-pane').addClass('none');
			$('#staff-' + elementId).removeClass('none');
		});

		$('#add-new-category').on('click', function (e) {
			e.preventDefault();
			$('#categoryPopover').slideToggle();
		});

		$('#closeNewCategoryForm').on('click', function (e) {
			e.preventDefault();
			$('#categoryPopover').slideUp();
			//$('#appointment-schedule-new-category-name').val();
			$('#createNewCategoryFrom')[0].reset();
		});

		$('.appointment-schedule-category-name').on('click', function (e) {
			e.preventDefault();
			$('#services-list').empty();
			var categoryId = $(this).data('category-id');

			var data = {
				categoryId: categoryId,
				action: 'get_category'
			};

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					if (result.success == 1) {
						$('#appointment-schedule-edit-category').show();
						if (result.services.length == 0) {
							$('.no-service-notification').show();
						} else {
							result.services.forEach(service => {
								var html = makeServiceItemHtml(service.id, service.title, service.duration, service.price);
								$('#services-list').append(html);
							});
						}
						$('#category-title').text(categoryId == 0 ? 'All Services' : result.category.name);
						
					}
				}
			});
		});

		$('#showVisibilityText').on('click', function () {
			$('#visibilityExtraData').slideToggle("slow");
		});

		$('#showPaddingText').on('click', function() {
			$('#paddingExtraData').slideToggle("slow");
		});

		$('#showLimitText').on('click', function () {
			$('#limitExtraData').slideToggle("slow");
		});

		$('#open-service-form').on('click', function (e) {
			e.preventDefault();
			$('.service-form-holder').toggle();
		});

		$('.close-form-btn').on('click', function () {
			$('.service-form-holder').hide();
		});

		$(document).keyup(function(e) {
        if (e.keyCode == 27) {
				$('.service-form-holder').hide();
			}
		});

		$('#saveServiceBtn').on('click', function (e) {
			e.preventDefault();
			var title = $('#serviceTitle').val();
			var visibility = $('#serviceVisibility').val();
			var price = $('#servicePrice').val();
			var duration = $('#serviceDuration').val();
			var paddingBefore = $('#servicePaddingBefore').val();
			var paddingAfter = $('#servicePaddingAfter').val();
			var categoryId = $('#category').val();
			var staffMemberId = $('#staffMember').val();
			var limit = $('#serviceLimit').val();
			var info = $('#serviceInfo').val();
			var serviceId = $('#serviceId').val();
			var action = serviceId > 0 ? 1 : 0;

			var data = {
				title: title,
				visibility: visibility,
				price: price,
				duration: duration,
				paddingBefore: paddingBefore,
				paddingAfter: paddingAfter,
				category: categoryId,
				staffMember: staffMemberId,
				limit: limit,
				info: info,
				serviceId: serviceId,
				action: 'save_service'
			}
			console.log(action);
			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					if (result.success == 1) {
						if (action == 0) {
							var html = makeServiceItemHtml(result.serviceId, title, duration, price);
							$('#services-list').append(html);
							$('#service-form')[0].reset();
						} else {
							$('#editService-' + serviceId).text(title);
							$('#price-' + serviceId).text('$' + price);
							$('#duration-' + serviceId).text(duration + ' min');
						}
						$('.service-form-holder').hide();
						$('#no-service-notification').hide();
					}					
				}
			});
		});

		$(document).on('click', '.edit-service-item', function (e) {
			e.preventDefault();
			$('.service-form-holder').show();
			var serviceId = $(this).data('service-id');
			console.log(serviceId);

			var data = {
				serviceId : serviceId,
				action: 'get_service'
			}

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					if (result.success == 1) {
						$('#serviceTitle').val(result.service.title);
						$('#serviceVisibility').val(result.service.visibility);
						$('#servicePrice').val(result.service.price);
						$('#serviceDuration').val(result.service.duration);
						$('#servicePaddingBefore').val(result.service.padding_left);
						$('#servicePaddingAfter').val(result.service.padding_right);
						$('#category').val(result.service.fk_category_id);
						$('#staffMember').val(result.staffId == null ? 0 : result.staffId.fk_staff_id);
						$('#serviceLimit').val(result.service.limit);
						$('#serviceInfo').val(result.service.info);
						$('#serviceId').val(result.service.id);
					}
				}
			});

		});

		function makeServiceItemHtml(serviceId, title, duration, price) {
			return '<div class="panel panel-default " id="service-item-holder-' + serviceId + '">' +
				'<div class= "panel-heading">' +
				'<div class="row appointment-schedule-service-list-item">' +
				'<div class="col-sm-8 col-xs-10">' +
				'<a href="#" id="editService-' + serviceId + '" class="edit-service-item" data-service-id="' + serviceId + '">' + title + '</a>' +
				'</div>' +
				'<div class="col-sm-4 col-xs-2">' +
				'<div class="col-md-4 appointment-schedule-service-list-item-duration">' +
				'<p id="duration-' + serviceId + '">' + displayTime(duration) + '</p>' +
				'</div>' +
				'<div class="col-md-4 appointment-schedule-service-list-item-price">' +
				'<p id="price-' + serviceId + '">$ ' + price + '</p>' +
				'</div>' +
				'<div class="col-md-4 appointment-schedule-service-list-item-delete">' +
				'<a href="#" class="delete-service" data-service-id="' + serviceId + '" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>' +
				'</div>' +
				'</div>' +
				'</div>' +
				'</div>' +
				'</div>';
		}

		$(document).on('click', '.delete-service', function(e) {
			e.preventDefault();
			var serviceId = $(this).data('service-id');

			var data = {
				serviceId: serviceId,
				action: 'delete_service'
			}

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					$('#service-item-holder-' + serviceId).remove();
				}
			});
		});

		$(document).on('click', '.edit-category', function(e) {
			e.preventDefault();
			var categoryId = $(this).data('category-id');

			var data = {
				categoryId: categoryId,
				action: 'get_category'
			}

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					console.log(result);
					if (result.success == 1) {
						$('#categoryPopover').slideToggle('slow');
						$('#appointment-schedule-new-category-name').val(result.category.name);
					}
				}

			});
		});

		$(document).on('click', '.delete-category', function(e) {
			e.preventDefault();
			var categoryId = $(this).data('category-id');

			var data = {
				categoryId: categoryId,
				action: 'delete_category'
			}

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					if (result.success == 1) {
						$('#category-nav-item-' + categoryId).remove();
						$('#appointment-schedule-edit-category').hide();
					}
				}
			});

		});

		function displayTime(seconds) {
			var minutes = seconds / 60;
			var mod = minutes % 60;
			var hoursString = Math.floor(minutes / 60) <= 1 ? ' hour ' : ' hours ';
			var time = '';
			if (mod == 0) {
				time = minutes / 60 + hoursString;
			} else {
				time = Math.floor(minutes / 60) != 0 ? Math.floor(minutes / 60) + hoursString + mod + ' minutes' : mod + ' minutes';
			}
			return time;
		}

		$('#saveStaffServicesBtn').on('click', function(e) {
			e.preventDefault();
			var servicesId = $('.appointment-schedule-category-checkbox:checkbox:checked').map(function () {
				return this.value;
			}).get();
			var staffId = $('#staffId').val();
			$('.spinner-holder ').show();

			var data = {
				servicesId: servicesId,
				staffId: staffId,
				action: 'staff_to_service'
			}

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					console.log(result);
					$('.spinner-holder ').hide();
					if (result.success == 1 || result.success == 2) {
						$('#success-msg').show();
						setTimeout(function () {
							$('#success-msg').hide();
						}, 3000);
					} else {
						$('#error-msg').show();
					}
				}
			});
			
		});

		$('.add-break-btn').on('click', function(e) {
			e.preventDefault();
			var dayIndex = $(this).data('day-index');
			$('#popover-' + dayIndex).toggle();
		});

		$('#saveScheduleBtn').on('click', function(e) {
			e.preventDefault();
			var schedule = [];
			var staffId = $('#staffId').val();
			for (var i = 0; i <= 7; i++) {
				schedule[i] = {
					start_time: $('#startTime-' + i).val(),
					end_time: $('#endTime-' + i).val()
				}
			}
			
			var data = {
				staffId: staffId,
				schedule: schedule,
				action: 'save_schedule'
			}

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					console.log(result);
				}
			});

		});

		$('.save-break').on('click', function(e) {
			e.preventDefault();
			var dayIndex = $(this).data('day-index');
			var sheduleId = $('#sheduleDay-' + dayIndex).val();
			var breakFrom = $('#breakFrom-' + dayIndex).val();
			var breakTo = $('#breakTo-' + dayIndex).val();
			
			var data = {
				sheduleId: sheduleId,
				breakFrom: breakFrom,
				breakTo: breakTo,
				action: 'save_break'
			}

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					console.log(result);
				}
			});
		});

		$('.date a').on('click', function() {
			var id = $(this).attr('id');
			$('.popover-holiday').hide();
			$('#holiday-' + id).toggle();
				
		});

		$(document).on('change', '.checkbox', function (e) {
			e.stopPropagation();
			var staffId = $('#staffId').val();
			var elementId = $(this).find('input').attr('id');
			var date = $('#' + elementId).data('date') != 'undefined' ? $('#' + elementId).data('date') : $('#' + elementId).data('date');
			var type = $('#' + elementId).data('type') != 'undefined' ? $('#' + elementId).data('type') : $('#' + elementId).data('type');
			var store = 1;
			var repeat = 0;
			var deleteAction = 0;

			if ( $('#dayOff-' + date).prop("checked") ) {				
				$('#everyYear-' + date).prop('disabled', false);
			} else {
				$('#everyYear-' + date).prop('disabled', true);
				store = 0;
				deleteAction = 1;
			}

			if (type == 'repeat') { 
				if ($('#everyYear-' + date).prop("checked")) {
					repeat = 1;
				}
				store = 0;
			}

			var data = {
				staffId: staffId,
				date: date,
				store: store,
				repeat: repeat,
				deleteAction: deleteAction,
				action: 'save_holidays'
			};
			
			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					if (result.success == 1) {
						if (result.action == 'delete') {
							$('#' + date).removeClass('holiday');
						}
					}
				}
			});

		});

		$(document).on('click', '#closeHolidayPopoverBtn',function (e) {
			e.stopPropagation();
			$('.popover-holiday').hide();
		});

		$('.year-changer').on('click', function (e) {
			e.preventDefault();
			var action = $(this).data('action');
			var currentYear = parseInt($('#calendarYear').text());

			// action: 1 => prev year, 2 => next year
			var changedYear = action == 1 ? currentYear - 1 : currentYear + 1;

			var data = {
				year: changedYear,
				action: 'chage_year_for_holidays'
			};

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					$('#calendarYear').text(changedYear);
					$('.holiday-holder').empty();
					$('.holiday-holder').append(result);
				}
			});
		});

		if ($('#appointmentIdSearch').length > 0) {
			$('#appointmentIdSearch').on('keyup', function (e) {
				e.preventDefault();
				var searchedId = $.trim($(this).val());
				if (searchedId != '') {
					$('.appointment-table-row').each((index, el) => {
						if ($(el).data('appointment-id') != searchedId) {
							$(el).hide();
						} else {
							$(el).show();
						}
					});
				}
			});
		}

		if ($('#staffSearch').length > 0) {
			$('#staffSearch').on('change', function() {
				var staffId = parseInt($(this).val());
				if (staffId >= 1) {
					$('.appointment-table-row').each((index, el) => {
						if ($(el).data('staff-id') != staffId) {
							$(el).hide();
						} else {
							$(el).show();
						}
					});
				}
			})
		}
		
		if ($('#customerSearch').length > 0) {
			$('#customerSearch').on('change', function() {
				var customerId = parseInt($(this).val());
				if (customerId >= 1) {
					$('.appointment-table-row').each((index, el) => {
						if ($(el).data('customer-id') != customerId) {
							$(el).hide();
						} else {
							$(el).show();
						}
					});
				}
			})
		}

		if ($('#serviceSearch').length > 0) {
			$('#serviceSearch').on('change', function () {
				var serviceId = parseInt($(this).val());
				if (serviceId >= 1) {
					$('.appointment-table-row').each((index, el) => {
						if ($(el).data('service-id') != serviceId) {
							$(el).hide();
						} else {
							$(el).show();
						}
					});
				}
			})
		}

		if ($('#statusSearch').length > 0) {
			$('#statusSearch').on('change', function () {
				var status = $(this).val();
				if (status != '') {
					$('.appointment-table-row').each((index, el) => {
						if ($(el).data('status') != status) {
							$(el).hide();
						} else {
							$(el).show();
						}
					});
				}
			})
		}

		if ($('#searchCustomer').length > 0) {
			$('#searchCustomer').on('keyup', function (e) {
				e.preventDefault();
				var searchedCustomer = $(this).val();
				if (searchedCustomer != '') {
					$('.customer-name').each((index, el) => {
						if ($(el).text().indexOf(searchedCustomer) != -1) {
							$(el).parent().show();
						} else {
							$(el).parent().hide();
						}
					} );
				} else {
					$('.customer-name').each((index, el) => {
						$(el).parent().show();
					});
				}
			});
		}

		if ($('#staffSearch').length > 0) {
			$('#staffSearch').select2({
				placeholder: 'Employee',
				allowClear: true
			});
		}
		
		if ($('#customerSearch').length > 0) {
			$('#customerSearch').select2({
				placeholder: 'Customer',
				allowClear: true
			});
		}
		if ($('#serviceSearch').length > 0) {
			$('#serviceSearch').select2({
				placeholder: 'Service',
				allowClear: true
			});
		}
		if ($('#statusSearch').length > 0) {
			$('#statusSearch').select2({
				placeholder: 'Status',
				allowClear: true
			});
		}

	});

})( jQuery );
