import { getStatusError } from './_apiHelper.js';

'use strict';

(function(window, $, Swal)
{

	let CalculateHelperInstances = new WeakMap();
    var counter;
    var counterPaused = false;

	class WorkoutApi
	{
		constructor($wrapper, $nowWorkoutWrapper)
		{
			
			this.$wrapper = $wrapper;
            this.$nowWorkoutWrapper = $nowWorkoutWrapper;
			CalculateHelperInstances.set(this, new CalculateHelper(this.$wrapper));
            this.handleDocumentLoad();
           
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

            //  nowWorkoutWrapper
            this.$nowWorkoutWrapper.on(
                'click',
                '.js-start-button',
                this.handleStartButtonClick.bind(this)
            );
            this.$nowWorkoutWrapper.on(
                'click',
                '.js-pause-button',
                this.handlePauseButtonClick.bind(this)
            );
            this.$nowWorkoutWrapper.on(
                'click',
                '.js-continue-button',
                this.handleContinueButtonClick.bind(this)
            );
            this.$nowWorkoutWrapper.on(
                'click',
                '.js-stop-button',
                this.handleStopButtonClick.bind(this)
            );
            this.$nowWorkoutWrapper.on(
                'click',
                '.js-reset-button',
                this.handleResetButtonClick.bind(this)
            );
		}

		static get _selectors() {
            return {
                newWorkoutForm: '.js-new-workout-form'
            }
        }

        handleDocumentLoad() { 
        //TO DO  
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

            this.$nowWorkoutWrapper.find('.js-workout-now-div').html(input);
            $('#js-workout-now-activity').append($options);

            this.setTooltips();
        }

        handleWorkoutDelete(event) {
        	event.preventDefault();
            const $link = $(event.currentTarget);
            const id = $link.data('id');
            Swal.fire({
  				title: 'Delete workout??',
  				text: `Do you want delete this workout?`,
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
            CalculateHelperInstances.get(this).getTotalDistance();
            CalculateHelperInstances.get(this).getTotalDuration();
        }

        handleNewWorkoutSubmit(event) {
            //TO DO
        	event.preventDefault();
        	const $form = $(event.currentTarget);
        	const formData = {};
            formData['durationSecondsTotal'] = {};

        	for(let fieldData of $form.serializeArray()) {
        		if(fieldData.name == 'durationSecondsTotal[hour]') {
        			formData['durationSecondsTotal']['hour'] = fieldData.value;
        		} else if(fieldData.name == 'durationSecondsTotal[minute]') {
        			formData['durationSecondsTotal']['minute'] = fieldData.value;
        		} else if(fieldData.name == 'durationSecondsTotal[second]') {
                    formData['durationSecondsTotal']['second'] = fieldData.value;
                } else {
        			formData[fieldData.name] = fieldData.value;
        		}
        	}

        	this._saveWorkout(formData, $form).then((data) => {
                this._addRow(data);
                this._clearForm();
            }).catch((errorData) => {
                this._mapErrorsToForm(errorData);
            })

        }

        _saveWorkout(data, $form) {
            //TO DO
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
            //TO DO
  	    	const tplText = $('#js-workout-row-template').html();
        	const tpl = _.template(tplText);
            const html = tpl(workout);
            this.$wrapper.find('tbody').append($.parseHTML(html));
            this.updateTotalWorkouts();
        }

		_clearForm() {
            //TO DO
        	const $form = this.$wrapper.find(WorkoutApi._selectors.newWorkoutForm);
        	this._removeFormErrors($form);
			$form[0].reset();
        }

        handleEditWorkout(event) {
        	event.preventDefault();
        	const $link = $(event.currentTarget);
        	this._editWorkoutToForm($link);
        }

        _editWorkoutToForm($link) {
        	$link.find('.far')
        		.removeClass('far fa-edit')
        		.addClass('fas fa-save');
        	const $row = $link.closest('tr');
        	$row.find('.js-edit-workout')
        		.removeClass('js-edit-workout')
        		.addClass('js-submit-edit-workout');
        	const $tds = $row.find('td');
        	const data = {};
        	var i = 0;
 			for (var cell of $tds) {
 				data[i] = $(cell).html();
 				i++;
 			}

            data[8] = data[8].replace(" ", "T");
        	
        	var newRow = 
        	`<tr>
                <td class="d-none">${data[0]}</td>
                <td class="d-none js-activity-type">${data[1]}</td>
				<td class="align-middle js-activity-name">${data[2]}</td>
				<td class="align-middle"> 
                    <div class="form-group">
                        <input type="text" name="durationSecondsTotal" required="required" class="form-control form-control-sm" value="${data[3].trim()}">
                    </div>
                </td>
                <td class="align-middle">
                    ${data[4].trim() !== '---' ? 
                    `<div class="form-group">
                        <input type="number" name="distanceTotal" required="required" class="form-control form-control-sm" value="${data[4].trim()}">
                    </div>` : '---' }
                </td>
                <td class="align-middle">
                    ${data[5].trim() !== '---' ? 
                    `<div class="form-group">
                        <input type="number" name="repetitionsTotal" required="required" class="form-control form-control-sm" value="${data[5].trim()}">
                    </div>` : '---' }
                </td>
                <td class="align-middle">
                    ${data[6].trim() !== '---' ? 
                    `<div class="form-group">
                        <input type="number" name="dumbbellWeight" required="required" class="form-control form-control-sm" value="${data[6].trim()}">
                    </div>` : '---' }
                </td>
				<td class="align-middle">${data[7]}</td>
                <td class="align-middle">
                    <div class="form-group">
                        <input type="datetime-local" name="startAt" required="required" class="form-control" value="${data[8]}">
				    </div>
                </td>    
                <td class="links-table">
					${data[9]}
                    <div class="link-wrapper">
                        <a href="#" class="js-edit-workout-cancel" data-toggle="tooltip" data-placement="left" title="Cancel editing" data-id="${data[0]}">
                            <span class="fa fa-times"></span>
                	   </a>
                    </div>
				</td>
			</tr>`
        	;

			$row.replaceWith(newRow);
            this.setTooltips();
        }

        handleEditWorkoutCancel(event) {
        	event.preventDefault();
        	const $link = $(event.currentTarget);

        	this._getWorkout($link).then((data) => {
        		this._editWorkoutToText($link, data);
        	});
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

        handleEditWorkoutSubmit(event) {
        	event.preventDefault();
        	const $link = $(event.currentTarget);
        	const $row = $link.closest('tr');
        	const url = $row.find('.js-submit-edit-workout').data('url');

        	const duration = $row.find('[name=durationSecondsTotal]').val();
        	const durationArray = duration.split(':');
        	const ex = new RegExp(/^0{1}.{1}/);
        	for (var i = 0; i < 3; i++) {
        		if(durationArray[i].match(ex)) {	
        			durationArray[i] = durationArray[i].substring(1);
        		}
        	}

        	const inputsData = {};
            inputsData['durationSecondsTotal'] = {};
            
            inputsData['activityName'] = $row.find('.js-activity-name').text();
            inputsData['type'] = $row.find('.js-activity-type').text();
            inputsData['startAt'] = $row.find('[name=startAt]').val();
            if ($row.find('[name=distanceTotal]').val()) {
                inputsData['distanceTotal'] = $row.find('[name=distanceTotal]').val();
            }
            if ($row.find('[name=repetitionsTotal]').val()) {
                inputsData['repetitionsTotal'] = $row.find('[name=repetitionsTotal]').val();
            }
            if ($row.find('[name=dumbbellWeight]').val()) {
                inputsData['dumbbellWeight'] = $row.find('[name=dumbbellWeight]').val();
            }
        	inputsData['durationSecondsTotal']['hour'] = durationArray[0];
            inputsData['durationSecondsTotal']['minute'] = durationArray[1];
        	inputsData['durationSecondsTotal']['second'] = durationArray[2];
            //inputsData['_token'] = $('#_token').val();
        	
            this.updateWorkout(inputsData, url).then((data) => {
                this._editWorkoutToText($link, data);
                CalculateHelperInstances.get(this).getTotalEnergy();
                CalculateHelperInstances.get(this).getTotalDistance();
                CalculateHelperInstances.get(this).getTotalDuration();
            }).catch((errorData) => {
                if (errorData.type === 'form_validation_error') {
                    this._mapErrorsToForm(errorData, $row);
                } else {
                    this.showErrorMessage(errorData.title);
                }   
            });
        }

        showErrorMessage(errorMessage) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: `${errorMessage}`,
            });
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
                    let errorData = getStatusError(jqXHR);
                    if(errorData === null) {
                        errorData = JSON.parse(jqXHR.responseText);
                    }
                    reject(errorData);
                });
            });
        }

        _editWorkoutToText($link, data) {
        	const $row = $link.closest('tr');
        	var newRow = 
        	`<tr>
                <td class="d-none">${data['id']}</td>
                <td class="d-none js-activity-type">${data['activity']['type']}</td>
				<td class="align-middle js-activity-name" data-id="${data['activity']['id']}">${data['activity']['name']}</td>
				<td class="align-middle js-duration">${data['time']}</td>
                <td class="align-middle js-distance">
                    ${data['distanceTotal'] ? 
                        `${data['distanceTotal']}` 
                    : '---' }
                </td>
                <td class="align-middle">
                    ${data['repetitionsTotal'] ? 
                        `${data['repetitionsTotal']}` 
                    : '---' }
                </td>
                <td class="align-middle">
                    ${data['dumbbellWeight'] ? 
                        `${data['dumbbellWeight']}` 
                    : '---' }
                </td>
				<td class="align-middle js-energy">${data['burnoutEnergyTotal']}</td>
                <td class="align-middle">${data['startDate']}</td>
				<td class="links-table">
                    <div class="link-wrapper">
                        <a href="#" class="js-delete-workout delete-item" data-url="${data['links']['delete']}"
                        data-id="${data['id']}" data-toggle="tooltip" data-placement="left" title="Delete">
                    	   <span class="fa fa-trash-alt"></span>
                        </a>
                    </div>
                	<div class="link-wrapper">
                        <a href="#" class="js-edit-workout" data-url="${data['links']['edit']}"
                        data-toggle="tooltip" data-placement="left" title="Fast edit">
                    	   <span class="far fa-edit"></span>
                        </a>
                    </div>
                    <div class="link-wrapper">
                        <a href="#" data-toggle="tooltip" data-placement="left" title="Full edit">
                           <span class="fa fa-pencil-alt"></span>
                        </a>
                    </div>
				</td>
			</tr>`
        	;

			$row.replaceWith(newRow);
            this.setTooltips();
        }

        _mapErrorsToForm(errorData, $row = null, $nowWorkoutWrapper = null) {
        	let $form;

            if($row == null && $nowWorkoutWrapper == null) {
            	$form = this.$wrapper.find(WorkoutApi._selectors.newWorkoutForm);
            } else if($row == null) {
                $form = $nowWorkoutWrapper;
            } else {
            	$form = $row;
            }
           
            this._removeFormErrors($form);
            
            for (let element of $form.find(':input')) {
                	let fieldName = $(element).attr('name');
                	const $fieldWrapper = $(element).closest('.form-group');

                    if(fieldName == 'durationSecondsTotal[hour]') {
                        fieldName = 'durationSecondsTotal';
                    }

                	if (!errorData[fieldName]) {
                    	continue;
                	}

                	const $error = $('<span class="js-field-error help-block text-danger"></span>');
                	$error.html(errorData[fieldName]);
                	$fieldWrapper.append($error);
                	$fieldWrapper.addClass('has-error');
            }
        }

        _removeFormErrors($form) {
            $form.find('.js-field-error').remove();
            $form.find('.form-group').removeClass('has-error');
        }

            //  nowWorkoutWrapper
        handleStartButtonClick() {           
            var $startButton = this.$nowWorkoutWrapper.find('.js-start-button');
            var $selectInput = this.$nowWorkoutWrapper.find('#js-workout-now-activity');
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
                this._removeFormErrors(this.$nowWorkoutWrapper);

            }).catch((errorData) => {
                this._mapErrorsToForm(errorData, null , this.$nowWorkoutWrapper);
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
            var $pauseButton = this.$nowWorkoutWrapper.find('.js-pause-button');
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
            var $continueButton = this.$nowWorkoutWrapper.find('.js-continue-button');
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
            var $pauseButton = this.$nowWorkoutWrapper.find('.js-pause-button');
            this.changePauseButton($pauseButton);

            this._getServerDate().then((today) =>{
                this._getDataFromFields(today);
            })
        }

        _getDataFromFields(today) {
            const inputsData = {};
            inputsData['durationSecondsTotal'] = {};

            var $selectInput = this.$nowWorkoutWrapper.find('#js-workout-now-activity');
            inputsData['activity'] = $selectInput.val();

            var durationString = this.$nowWorkoutWrapper.find('#js-workout-now-duration').val();
            const durationArray = durationString.split(' ');

            inputsData['durationSecondsTotal']['hour'] = durationArray[0];
            inputsData['durationSecondsTotal']['minute'] = durationArray[2];
            inputsData['durationSecondsTotal']['second'] = durationArray[4];
            inputsData['startAt'] = today;

            inputsData['_token'] = $('#_token').val();

            this._saveWorkoutNow(inputsData).then((data) => {
                this._addRow(data);
                clearInterval(counter);
                counterPaused = false;
            }).catch((errorData) => {
                this._mapErrorsToForm(errorData, null , this.$nowWorkoutWrapper);
            })
        }

        _getServerDate() {
            return new Promise(function(resolve) {
                const url = '/api/server_date';
                $.ajax({
                    url,
                    method: 'GET'
                }).then(function(today) {
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

            this.$nowWorkoutWrapper.find('.js-workout-now-div').html(input);
            $('#js-workout-now-activity').append($options);
        }

        setTooltips() {
            $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
            $('[data-toggle="tooltip"]').on('click', function () {
                $(this).tooltip('hide')
            });
        }

	}

	class CalculateHelper
	{

		constructor($wrapper)
		{
			this.$wrapper = $wrapper;
			this.getTotalEnergy();
            this.getTotalDistance();
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
        	for (let energy of this.$wrapper.find('.js-energy')) {
        		sum = sum + parseInt($(energy).html());
        	}
        	
        	this.$wrapper.find('.js-total-energy').html(sum);
        }

        getTotalDistance() {
            let sum = 0;
            for (let distance of this.$wrapper.find('.js-distance')) {
                if (parseFloat($(distance).html())) {
                    sum = sum + parseFloat($(distance).html());
                }
            }
            
            this.$wrapper.find('.js-total-distance').html(sum);
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

    window.WorkoutApi = WorkoutApi;

})(window, jQuery, Swal);
