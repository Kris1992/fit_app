'use strict';

(function(window, $, Swal)
{
	class CalendarApi
	{

		constructor($calendarWrapper)
		{
			this.$calendarWrapper = $calendarWrapper;
			this.handleDocumentLoadOrClick();
			
			/*$(window).on(
                'load',
                this.handleDocumentLoad.bind(this)
            );*/

			this.$calendarWrapper.on(
                'click',
                '.fc-center > :button',
                this.handleDocumentLoadOrClick.bind(this)
            );
            this.$calendarWrapper.on(
                'click',
                '.fc-right > .btn-group > :button',
                this.handleDocumentLoadOrClick.bind(this)
            );
            $(".fc-today-button").on(
                'click',
                this.handleDocumentLoadOrClick.bind(this)
            );
            this.$calendarWrapper.on(
                'click',
                '.js-cal-event',
                this.handleEventButtonClick.bind(this)
            );
		}

		handleDocumentLoadOrClick() {
			this.showLoadingIcon();
			var timelineBounds = this.getTimeline();
			this.getWorkouts(timelineBounds).then((data) => {
				this.mapWorkoutToCell(data);
				this.removeLoadingIcon('success');
        	}).catch((errorData) => {
        		this.removeLoadingIcon('error');
            });
		}

		handleEventButtonClick(e) {
			var eventElement = e.target.closest('button');
			var id = eventElement.dataset.id; 

			this._getWorkoutInfo(id).then((data) => {
				console.log(data);
        		Swal.fire({
  					title: `${data['activity']['name']}`,
  					html: `<strong>Duration: </strong> ${data['time']}
  							<br/><strong>Burnout Energy: </strong>${data['burnoutEnergy']} <strong>kcal</strong>
  							<br/><strong>Date: </strong> ${data['startDate']}`,
  					type: 'info',
 		 			confirmButtonText: '<i class="fa fa-thumbs-up"></i> OK!',
  					confirmButtonAriaLabel: 'OK!'
				});
        	})
			//console.log(eventElement.dataset.id);
		}

		_getWorkoutInfo(id) {
        	return new Promise(function(resolve) {
                const url = '/api/workout_get/'+id;
                $.ajax({
                    url,
                    method: 'GET'
                }).then(function(data) {
                    resolve(data);
                });
            });
        }

		getTimeline() {
			var $firstDataWrapper = this.$calendarWrapper.find('.fc-bg:first');
			var $lastDataWrapper = this.$calendarWrapper.find('.fc-bg:last');

			var timelineBounds = new Object();
			timelineBounds.startDate = $firstDataWrapper.find('td:first').data('date');
			timelineBounds.stopDate = $lastDataWrapper.find('td:last').data('date');
			
			return timelineBounds;
		}

		getWorkouts(timelineBounds) {	
			const url = $('#calendar').data('url');
			return new Promise(function(resolve, reject){
				$.ajax({
					url,
					method: 'POST',
					data: JSON.stringify(timelineBounds)
				}).then(function(data){
					resolve(data);
				}).catch(function(jqXHR){
					const errorData = JSON.parse(jqXHR.responseText);
                    reject(errorData);
				});
			});
		}

		mapWorkoutToCell(data) {	
			for (let date of data) {
				let $element = $('.fc-bg').find(`[data-date='${date['startDate']}']`);

				if(!$element.has('.cal-container').length) {
					$element.append('<div class="cal-container" role="group"></div>');
				}

				$element.find('.cal-container').append(
					`
					<button type="button" class="btn btn-info btn-sm rounded-circle js-cal-event" data-id="${date['id']}">
                		<span class="fas fa-running"></span>
              		</button>`
              		);
			}
		}

		showLoadingIcon() {
			var $loadingWrapper = this.$calendarWrapper.find('.fc-right');
			
			if($('.js-loading-cal').length) {
				$('.js-loading-cal').remove();
			}

			var $loadingIcon = `<span class="fas fa-spinner fa-spin js-loading-cal"></span>`;
			$loadingWrapper.append($loadingIcon);		
		}

		removeLoadingIcon(status) {
			var $loadingIcon = this.$calendarWrapper.find('.js-loading-cal');

			if(status == 'success') {
				$loadingIcon.removeClass('fas fa-spinner fa-spin');
				$loadingIcon.addClass('fa fa-check text-green');
			} else {
				$loadingIcon.removeClass('fas fa-spinner fa-spin');
				$loadingIcon.addClass('fas fa-exclamation text-danger');
			}
		}
	}

	window.CalendarApi = CalendarApi;

})(window, jQuery, Swal);
