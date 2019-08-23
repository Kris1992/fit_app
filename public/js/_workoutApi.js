'use strict';

(function(window, $, Swal)
{

	let CalculateHelperInstances = new WeakMap();

	class WorkoutApi
	{
		constructor($wrapper)
		{
			this.$wrapper = $wrapper;
			CalculateHelperInstances.set(this, new CalculateHelper(this.$wrapper));

			this.$wrapper.on(
				'click',
				'tbody tr',
				this.handleRowClick.bind(this)
				);
			this.$wrapper.on(
				'click',
				'.js-delete-workout',
				this.handleWorkoutDelete.bind(this)
				);
		}


		handleRowClick() {
            console.log('Test - Row was clicked!');
        }

        handleWorkoutDelete(e)
        {
        	e.preventDefault();
            const $link = $(e.currentTarget);
            const id = $link.data('id');
            Swal.fire({
  				title: 'Delete workout??',
  				text: `Do you want delete this workout nr. ${id} ?`,
  				type: 'question',
  				showCancelButton: true,
                showLoaderOnConfirm: true,
                confirmButtonText: 'YES',

                preConfirm: () => {

                    //return new Promise(function(resolve, reject) {
                     //   setTimeout(function() {
                    //        resolve();
                    //    }, 1000);
                    //});
                    return this._deleteWorkout($link);
                }

			});

        }

    	_deleteWorkout($link){
        	$link.addClass('text-danger');
        	$link.find('.fa')
        		.removeClass('fa-trash')
        		.addClass('fa-spinner fa-spin');
        	const deleteUrl = $link.data('url');
        	const $row = $link.closest('tr');


        	return $.ajax({
        		url: deleteUrl,
        		method: 'DELETE'
        	}).then(() => {
        		$row.fadeOut('normal', () => {
        			$row.remove();
        			this.updateTotalWorkouts();
        		});
        	});

        }

    	updateTotalWorkouts() {
            this.$wrapper.find('.js-total-workouts').html(
            	CalculateHelperInstances.get(this).getTotalWorkouts()
            );
        }




	}

	class CalculateHelper
	{

		constructor($wrapper)
		{
			this.$wrapper = $wrapper;
		}

		getTotalWorkouts() {
            let workouts = this.calculateTotalWorkouts();
            
            return workouts;
        }
        calculateTotalWorkouts(){
        	let workouts = this.$wrapper.find('tbody tr').length;

        	return workouts;

        }


	}

	window.WorkoutApi = WorkoutApi;

})(window, jQuery, Swal);