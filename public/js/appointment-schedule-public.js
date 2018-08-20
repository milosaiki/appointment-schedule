(function( $ ) {
	'use strict';

	$(document).ready(function() {

		$('.appointment-schedule-time-step-time').on('click', function(e) {
			e.preventDefault();
			$('#timeStep').hide();
			$('#detailsStep').show();
			$('#detailsTracker').addClass('active');
			$('#nextBtn').show();
			$('#backBtn').show();
			$('#time').val($(this).find('span').text());
			$('#appointmentTime').val($(this).find('span').text());
			$('#date').val($('#scheduleDate').data('date'));
			$('#appointmentDate').val($('#scheduleDate').data('date'));
		});

		$(document).on('click', '#nextBtn', function (e) {
			e.preventDefault();
			//$('.spinner-holder').show();
			$('#doneTracker').addClass('active');
			$('#nextBtn').hide();

			var date = $('#date').val();
			var time = $('#time').val();
			var name = $('#name').val();
			var phone = $('#phone').val();
			var email = $('#email').val();
			var note = $('#note').val();
			var staffId = $('#staff').data('staff-id');
			var serviceId = $('#service').data('service-id');
			var duration = parseInt($('#appointmentTime').data('duration'));
			var data = {
				date: date.trim(),
				time: time.trim(),
				name: name.trim(),
				phone: phone.trim(),
				email: email.trim(),
				note: note.trim(),
				staffId: staffId,
				serviceId: serviceId,
				duration: duration,
				action: 'make_appointment'
			}

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				success: function(result) {
					console.log(result);
					$('.spinner-holder').hide();
					$('#detailsStep').hide();
					$('#doneStep').show();
				}
			});

		});

		$(document).on('click', '#backBtn', function(e) {
			var element = $('.appointment-schedule-progress.active').last();
			element.removeClass('active');
			$('#detailsStep').hide();
			$('#timeStep').show();
			$('#nextBtn').hide();
			$('#backBtn').hide();
		});

		$('.date-link').on('click', function (e) {
			e.preventDefault();
			$('.date').removeClass('active-date');
			$(this).parent().addClass('active-date');
		});

		$(document).on('click', '#changeMonth', function (e) {
			e.preventDefault();

			// $(this).data('status') == 0 => defaulth month  	$(this).data('status') == 1 => next month
			var status = $(this).data('status') == 0 ? 1 : 0;

			var data = {
				changed: status,
				action: 'change_month'
			};

			$.ajax({
				type: 'POST',
				url: appointment_schedule_ajax.ajax_url,
				data: data,
				dataType: 'json',
				success: function (result) {
					if (result.success == 1) {
						var respJson = result.calendar.split("\n");
						var calendar = jQuery.parseJSON(respJson.pop());
						
						$('.appointment-schedule-time-step-holder').empty();
						$('.appointment-schedule-time-step-holder').append(calendar);
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


	});

})( jQuery );
