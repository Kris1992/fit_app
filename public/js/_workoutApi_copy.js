'use strict';

(function(window, $, Swal)
{

	let CalculateHelperInstances = new WeakMap();
    var counter;
    var counterPaused = false;

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
			this.$wrapper.on(
				'click',
				'.js-edit-workout',
				this.handleEditWorkout.bind(this)
				);
			this.$wrapper.on(
				'click',
				'.js-edit-workout-cancel',
				this.handleEditWorkoutCancel.bind(this)
				);
			this.$wrapper.on(
				'click',
				'.js-submit-edit-workout',
				this.handleEditWorkoutSubmit.bind(this)
				);
			this.$wrapper.on(
                'submit',
                WorkoutApi._selectors.newWorkoutForm,
                this.handleNewWorkoutSubmit.bind(this)
        	);
		}

		static get _selectors() {
            return {
                newWorkoutForm: '.js-new-workout-form'
            }
        }
 		
 		handleRowClick() {

            console.log('row clicked!');

        }

        handleWorkoutDelete(e) {
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
                    return this._deleteWorkout($link);
                }
			})
        }

    	_deleteWorkout($link) {
        	$link.addClass('text-danger');
        	$link.find('.fa')
        		.removeClass('fa-trash-alt')
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
            CalculateHelperInstances.get(this).getTotalEnergy();
            CalculateHelperInstances.get(this).getTotalDuration();
        }

        handleNewWorkoutSubmit(e){
        	e.preventDefault();
        	const $form = $(e.currentTarget);
        	const formData = {};
            formData['durationSeconds'] = {};
        	formData['duration'] = {};// tymczasowo

        	for(let fieldData of $form.serializeArray()){
        		
        		if(fieldData.name == 'durationSeconds[hour]'){
        			formData['durationSeconds']['hour'] = fieldData.value;
        		} else if(fieldData.name == 'durationSeconds[minute]') {
        			formData['durationSeconds']['minute'] = fieldData.value;
        		} else if(fieldData.name == 'durationSeconds[second]') {
                    formData['durationSeconds']['second'] = fieldData.value;
                } 
                /// tymczasowo

                else if(fieldData.name == 'duration[hour]'){
                    formData['duration']['hour'] = fieldData.value;
                } else if(fieldData.name == 'duration[minute]') {
                    formData['duration']['minute'] = fieldData.value;
                }
///


                else {
        			formData[fieldData.name] = fieldData.value;
        		}
        	}

        	this._saveWorkout(formData, $form).then((data) => {
                this._addRow(data);
                this._clearForm();


                // te funkcje już wywołujewmy w addRow
                //CalculateHelperInstances.get(this).getTotalEnergy();
                //CalculateHelperInstances.get(this).getTotalDuration();
            }).catch((errorData) => {
                this._mapErrorsToForm(errorData);
            })

        }

        _saveWorkout(data, $form) {
        	
        	return new Promise(function(resolve, reject) {
                const url = $form.data('url');

                $.ajax({
                    url,
                    method: 'POST',
                    data: JSON.stringify(data)
                }).then(function(data, textStatus, jqXHR) {
                    $.ajax({
                        url: jqXHR.getResponseHeader('Location')
                    }).then(function(data) {
                        resolve(data);
                    });
                }).catch(function(jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    reject(errorData);
                });
            });
        }

        _addRow(workout) {
  	    	const tplText = $('#js-workout-row-template').html();
        	const tpl = _.template(tplText);
            const html = tpl(workout);
            this.$wrapper.find('tbody').append($.parseHTML(html));
            this.updateTotalWorkouts();
        }

		_clearForm() {
        	const $form = this.$wrapper.find(WorkoutApi._selectors.newWorkoutForm);
        	this._removeFormErrors($form);
			$form[0].reset();
        }

        handleEditWorkout(e) {
        	e.preventDefault();
        	const $link = $(e.currentTarget);
        	this._editWorkoutToForm($link);
        }

        _editWorkoutToForm($link) {
        	$link.find('.fa')
        		.removeClass('fa-pencil-alt')
        		.addClass('fa-save');
        	const $row = $link.closest('tr');
        	$row.find('.js-edit-workout')
        		.removeClass('js-edit-workout')
        		.addClass('js-submit-edit-workout');
        	const $tds = $row.find('td');
        	const data = {};
        	let activity_id = 0;
        	var i = 0;
 			for (var cell of $tds) {
 				data[i] = $(cell).html();
 				if($(cell).data('id')) {
 					activity_id = $(cell).data('id');
 				}
 				i++;
 			}

            data[4] = data[4].replace(" ", "T");

 			var $options = $("#activity > option").clone();
    		$options[0].text = data[1];
        	$options[0].value = activity_id;
        	
        	var newRow = 
        	`<tr>
				<td>${data[0]}</td>
				<td>
					<div class="form-group">
						<select name="activity" id="${data[0]}" required="required" class="form-control"></select>
					</div>
				</td>
				<td>
					<div class="form-group">
						<input type="text" name="durationSeconds" required="required" class="form-control form-control-sm" value="${data[2]}">
					</div>
				</td>
				<td>${data[3]}</td>
                <td><input type="datetime-local" name="startAt" required="required" class="form-control" value="${data[4]}"></td>
				<td>
					${data[5]}
					<a href="#" 
					class="js-edit-workout-cancel"
					data-id="${data[0]}"
                	>
                    	<span class="fa fa-times"></span>
                	</a>
				</td>
			</tr>`
        	;

			$row.replaceWith(newRow);
			$('#'+data[0]).append($options);
        }

        handleEditWorkoutCancel(e) {
        	e.preventDefault();
        	const $link = $(e.currentTarget);

        	this._getWorkout($link).then((data) => {
        		this._EditWorkoutToText($link, data);
        	})
        }

        _getWorkout($link) {
        	const $row = $link.closest('tr');
        	const id = $row.find('.js-edit-workout-cancel').data('id');

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

        handleEditWorkoutSubmit(e) {
        	e.preventDefault();
        	const $link = $(e.currentTarget);
        	const $row = $link.closest('tr');
        	const url = $row.find('.js-submit-edit-workout').data('url');

        	const duration = $row.find('[name=durationSeconds]').val();
        	const durationArray = duration.split(':');
        	const ex = new RegExp(/^0{1}.{1}/);
        	for (var i = 0; i < 3; i++) {
        		if(durationArray[i].match(ex)) {	
        			durationArray[i] = durationArray[i].substring(1);
        		}
        	}

        	const inputsData = {};
            inputsData['durationSeconds'] = {};
        	            //tymczasowo
                        inputsData['duration'] = {};
                        //


            inputsData['activity'] = $row.find('[name=activity]').children("option:selected").val();
        	inputsData['startAt'] = $row.find('[name=startAt]').val();
        	inputsData['durationSeconds']['hour'] = durationArray[0];
            inputsData['durationSeconds']['minute'] = durationArray[1];
        	inputsData['durationSeconds']['second'] = durationArray[2];

            //tymczasowo
            inputsData['duration']['hour'] = durationArray[0];
            inputsData['duration']['minute'] = durationArray[1];
            //
            inputsData['_token'] = $('#_token').val();
        	
            this.updateWorkout(inputsData, url).then((data) => {
                this._EditWorkoutToText($link, data);
                CalculateHelperInstances.get(this).getTotalEnergy();
                CalculateHelperInstances.get(this).getTotalDuration();
            }).catch((errorData) => {
                this._mapErrorsToForm(errorData, $row);
            })
        }

        updateWorkout(data, url) {
        	return new Promise(function(resolve, reject) {
                $.ajax({
                    url,
                    method: 'PUT',
                    data: JSON.stringify(data)
                }).then(function(data, textStatus, jqXHR) {
                    $.ajax({
                        url: jqXHR.getResponseHeader('Location')
                    }).then(function(data) {
                        resolve(data);
                    });
                }).catch(function(jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    reject(errorData);
                });
            });
        }

        _EditWorkoutToText($link, data) {
        	const $row = $link.closest('tr');
        	//console.log(data);
        	var newRow = 
        	`<tr>
				<td>${data['id']}</td>
				<td data-id="${data['activity']['id']}">${data['activity']['name']}</td>
				<td class="js-duration">${data['time']}</td>
				<td class="js-energy">${data['burnoutEnergy']}</td>
                <td>${data['startDate']}</td>
				<td>
					<a href="#" 
					class="js-delete-workout"
                	data-url="${data['links']['delete']}"
                	data-id="${data['id']}"
                	>
                    	<span class="fa fa-trash-alt"></span>
                	</a>
                	<a href="#"
                	class="js-edit-workout"
                	data-url="${data['links']['edit']}"
                	>
                    	<span class="fa fa-pencil-alt"></span>
                	</a>
				</td>
			</tr>`
        	;

			$row.replaceWith(newRow);
        }

        _mapErrorsToForm(errorData, $row = null) {
        	let $form;

            if($row == null) {
            	$form = this.$wrapper.find(WorkoutApi._selectors.newWorkoutForm);
            } else {
            	$form = $row;
            }
           
            this._removeFormErrors($form);
            
            for (let element of $form.find(':input')) {
                	let fieldName = $(element).attr('name');
                	const $wrapper = $(element).closest('.form-group');

                    //tymczasowo
                	if(fieldName == 'duration[hour]')
                	{
                		fieldName = 'duration';
                	}
                    //
                    if(fieldName == 'durationSeconds[hour]') {
                        fieldName = 'durationSeconds';
                    }

                	if (!errorData[fieldName]) {
                    	continue;
                	}
                	const $error = $('<span class="js-field-error help-block text-danger"></span>');
                	$error.html(errorData[fieldName]);
                	$wrapper.append($error);
                	$wrapper.addClass('has-error');
            }
        }

        _removeFormErrors($form) {
            $form.find('.js-field-error').remove();
            $form.find('.form-group').removeClass('has-error');
        }
	}

	class CalculateHelper
	{

		constructor($wrapper)
		{
			this.$wrapper = $wrapper;
			this.getTotalEnergy();
			this.getTotalDuration();
		}

		getTotalWorkouts() {
            let workouts = this.calculateTotalWorkouts();
            
            return workouts;
        }

        calculateTotalWorkouts() {
        	let workouts = this.$wrapper.find('tbody tr').length;

        	return workouts;
        }

        getTotalEnergy() {
        	let sum = 0;
        	for (let energy of this.$wrapper.find('.js-energy') )
        	{
        		sum = sum + parseInt($(energy).html());
        	}
        	
        	this.$wrapper.find('.js-total-energy').html(sum);
        }

        getTotalDuration() {
        	let sumHour = 0;
        	let sumMin = 0;
            let sumSec = 0;
			
			for (let duration of this.$wrapper.find('.js-duration')) {
        		let durationString = $(duration).html();

        		const durationArray = durationString.split(':');
        		const ex = new RegExp(/^0./);
        			for (var i = 0; i < 3; i++) {
        				if(durationArray[i].match(ex)) {
        					durationArray[i] = durationArray[i].substring(1);
        				}
        			}

        		sumHour = sumHour + parseInt(durationArray[0]);
        		sumMin = sumMin + parseInt(durationArray[1]);
                sumSec = sumSec + parseInt(durationArray[2]);
        	}
            sumMin = sumMin + parseInt(sumSec/60);
            sumSec = sumSec%60;
        	sumHour = sumHour + parseInt(sumMin/60);
        	sumMin = sumMin%60;
        	
        	this.$wrapper.find('.js-total-duration').html(sumHour+'h '+sumMin+'min '+sumSec+'sec');
        }
	}

    class WorkoutNowApi
    {
        // włączyć to do workoutApi bo część funkcji wtedy się skroci a po za tym calculatehelpera wykonujemy dwa razy 

        constructor($wrapper, $nowWrapper)
        {

            this.$wrapper = $wrapper;
            this.$nowWrapper = $nowWrapper;
            CalculateHelperInstances.set(this, new CalculateHelper(this.$wrapper));

            $(window).on(
                'load',
                this.handleDocumentLoad.bind(this)
            );
            this.$nowWrapper.on(
                'click',
                '.js-start-button',
                this.handleStartButtonClick.bind(this)
            );
            this.$nowWrapper.on(
                'click',
                '.js-pause-button',
                this.handlePauseButtonClick.bind(this)
            );
            this.$nowWrapper.on(
                'click',
                '.js-continue-button',
                this.handleContinueButtonClick.bind(this)
            );
            this.$nowWrapper.on(
                'click',
                '.js-stop-button',
                this.handleStopButtonClick.bind(this)
            );
            this.$nowWrapper.on(
                'click',
                '.js-reset-button',
                this.handleResetButtonClick.bind(this)
            );
        }

        handleDocumentLoad() {
            
            var $options = $("#activity > option").clone();
            var input = 
            `
                <h4 class="card-title text-center text-green text-uppercase">Start exercise now</h4>
                <form method="post" class="form-signin">
                <div class="form-group">
                    <label class="required" for="js-workout-now-activity">Activity: </label>
                    <select name="activity" id="js-workout-now-activity" required="required" class="form-control-lg form-control"></select>
                </div>

                <button class="btn btn-lg btn-green btn-block text-uppercase js-start-button" type="button">Start</button>
                            
                </form>
            `
            ;

            this.$nowWrapper.find('.js-workout-now-div').html(input);
            $('#js-workout-now-activity').append($options);
        }

        handleStartButtonClick() {
            
            var $startButton = this.$nowWrapper.find('.js-start-button');
            var $selectInput = this.$nowWrapper.find('#js-workout-now-activity');
            var value = $selectInput.val();

            this._getActivity(value).then((data) => {
    
                var hours = 0;
                var minutes = 0;
                var totalSeconds = 0;
                var seconds = 0;
                var durationNow;
                var energyNow = 0 +' kcal';

                this.disableSelect($selectInput);
                counter = setInterval(startCount, 1000);
                function startCount() {
                    if(!counterPaused) {
                        seconds++;
                        totalSeconds++;
                        energyNow = Math.floor((parseInt(data['energy']) * totalSeconds)/3600) +' kcal';
                        if(seconds/60 == 1 ) {
                            seconds = 0;
                            minutes++;
                        }

                        if(minutes/60 == 1) {
                            minutes = 0;
                            hours++;
                        }

                        durationNow = hours+' hours '+minutes+' minutes '+seconds+' seconds ';
                    
                        document.getElementById('js-workout-now-duration').value = durationNow;
                        document.getElementById('js-workout-now-energy').innerHTML = energyNow;
                    }
                }

            this.changeStartButton($startButton);
            this._removeFieldsError($this.nowWrapper);

            }).catch((errorData) => {
                this._mapErrorsToFields(errorData);
            })
        }

        disableSelect($selectInput) {
            $selectInput.attr('disabled', 'disabled');
        }

        _getActivity(value) {
            return new Promise(function(resolve, reject) {
                const url = '/api/activity_get/'+value;
                $.ajax({
                    url,
                    method: 'GET'
                }).then(function(data) {
                    resolve(data);
                }).catch(function(jqXHR) {
                    const errorData = {activity: 'Invalid activity'};//jqXHR.statusText;
                    reject(errorData);
                });;
            });
        }

        changeStartButton($startButton) {
            var newButtons = 
            `
                <div class="form-group">
                    <label class="required" for="js-workout-now-duration">Duration: </label>
                    <input type="text" class="form-control" name="durationSeconds" id="js-workout-now-duration" disabled="disabled">
                </div>
                <p><span>Burnt out energy: </span><span id="js-workout-now-energy"></span></p>
                <button class="btn btn-lg btn-green btn-block text-uppercase js-pause-button" type="button">Pause</button>
                <button class="btn btn-lg btn-primary btn-block text-uppercase js-stop-button" type="button">Stop/Save</button>  
                <button class="btn btn-lg btn-danger btn-block text-uppercase js-reset-button" type="button">Reset</button>

            `
            ;

            $startButton.replaceWith(newButtons);
        }

        handlePauseButtonClick() {
            counterPaused = true;
            var $pauseButton = this.$nowWrapper.find('.js-pause-button');
            this.changePauseButton($pauseButton);
        }

        changePauseButton($pauseButton) {
             var newButtons = 
            `
                <button class="btn btn-lg btn-green btn-block text-uppercase js-continue-button" type="button">Continue</button> 

            `
            ;

            $pauseButton.replaceWith(newButtons);
        }

        handleContinueButtonClick() {
            counterPaused = false;
            var $continueButton = this.$nowWrapper.find('.js-continue-button');
            this.changeContinueButton($continueButton);
        }

        changeContinueButton($continueButton) {
            var newButtons = 
            `
                <button class="btn btn-lg btn-green btn-block text-uppercase js-pause-button" type="button">Pause</button> 

            `
            ;

            $continueButton.replaceWith(newButtons);
        }

        handleStopButtonClick() {
            counterPaused = true;
            var $pauseButton = this.$nowWrapper.find('.js-pause-button');
            this.changePauseButton($pauseButton);

            this._getServerDate().then((today) =>{
                this._getDataFromFields(today);
            })
        }

        _getDataFromFields(today) {
            const inputsData = {};
            inputsData['durationSeconds'] = {};
            //tymczasowo
            inputsData['duration'] = {};

            var $selectInput = this.$nowWrapper.find('#js-workout-now-activity');
            inputsData['activity'] = $selectInput.val();

            var durationString = this.$nowWrapper.find('#js-workout-now-duration').val();
            const durationArray = durationString.split(' ');

            inputsData['durationSeconds']['hour'] = durationArray[0];
            inputsData['durationSeconds']['minute'] = durationArray[2];
            inputsData['durationSeconds']['second'] = durationArray[4];
            inputsData['startAt'] = today;

            inputsData['_token'] = $('#_token').val();

            //tymczasowo
            inputsData['duration']['hour'] = durationArray[0];
            inputsData['duration']['minute'] = durationArray[2];

            this._saveWorkoutNow(inputsData).then((data) => {
                this._addRowNow(data);
                clearInterval(counter);
                counterPaused = false;
            }).catch((errorData) => {
                console.log(errorData);
                this._mapErrorsToFields(errorData);
            })
        }

        _getServerDate() {
            return new Promise(function(resolve) {
                const url = '/api/server_date';
                $.ajax({
                    url,
                    method: 'GET'
                }).then(function(today)
                {
                    resolve(today);
                });
                });
        }

        _saveWorkoutNow(data) {
            return new Promise(function(resolve, reject) {
                const url = $('.js-workout-now').data('url');
                $.ajax({
                    url,
                    method: 'POST',
                    data: JSON.stringify(data)
                }).then(function(data, textStatus, jqXHR) {
                    $.ajax({
                        url: jqXHR.getResponseHeader('Location')
                    }).then(function(data) {
                        resolve(data);
                    });
                }).catch(function(jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    reject(errorData);
                });
            });
        }
/// to sie skroci
        _addRowNow(workout) {
            const tplText = $('#js-workout-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(workout);
            this.$wrapper.find('tbody').append($.parseHTML(html));
            this.updateTotalWorkoutsNow();
        }

        updateTotalWorkoutsNow() {
            this.$wrapper.find('.js-total-workouts').html(
                CalculateHelperInstances.get(this).getTotalWorkouts()
            );
            CalculateHelperInstances.get(this).getTotalEnergy();
            CalculateHelperInstances.get(this).getTotalDuration();
        }

        _mapErrorsToFields(errorData) {
            
            let $fields = this.$nowWrapper;
            this._removeFieldsErrors($fields);
            
            for (let element of $fields.find(':input') ) {
                    let fieldName = $(element).attr('name');
                    const $fieldWrapper = $(element).closest('.form-group');

                    if (!errorData[fieldName]) {
                        continue;
                    }

                    const $error = $('<span class="js-now-error help-block text-danger"></span>');
                    $error.html(errorData[fieldName]);
                    $fieldWrapper.append($error);
                    $fieldWrapper.addClass('has-error');
            }
        }

        _removeFieldsErrors($nowWrapper) {
            $nowWrapper.find('.js-now-error').remove();
            $nowWrapper.removeClass('has-error');
        }
        //

        handleResetButtonClick() {
            clearInterval(counter);
            counterPaused = false;
            var $options = $("#activity > option").clone();
            var input = 
            `
                <h4 class="card-title text-center text-green text-uppercase">Start exercise now</h4>
                <form method="post" class="form-signin">
                <div class="form-group">
                    <label class="required" for="js-workout-now-activity">Activity: </label>
                    <select name="activity" id="js-workout-now-activity" required="required" class="form-control-lg form-control"></select>
                </div>     

                <button class="btn btn-lg btn-green btn-block text-uppercase js-start-button" type="button">Start</button>
                            
                </form>
            `
            ;

            this.$nowWrapper.find('.js-workout-now-div').html(input);
            $('#js-workout-now-activity').append($options);
        }

    }

    window.WorkoutApi = WorkoutApi;
	window.WorkoutNowApi = WorkoutNowApi;

})(window, jQuery, Swal);
