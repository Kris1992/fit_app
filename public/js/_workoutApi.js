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
				WorkoutApi._selectors.deleteWorkoutButton,
				this.handleWorkoutDelete.bind(this)
				);
			this.$wrapper.on(
				'click',
				WorkoutApi._selectors.fastEditButton,
				this.handleEditWorkout.bind(this)
				);
			this.$wrapper.on(
				'click',
				WorkoutApi._selectors.cancelEditButton,
				this.handleEditWorkoutCancel.bind(this)
				);
			this.$wrapper.on(
				'click',
				WorkoutApi._selectors.submitEditButton,
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
                WorkoutApi._selectors.startButton,
                this.handleStartButtonClick.bind(this)
            );
            this.$nowWorkoutWrapper.on(
                'click',
                WorkoutApi._selectors.pauseButton,
                this.handlePauseButtonClick.bind(this)
            );
            this.$nowWorkoutWrapper.on(
                'click',
                WorkoutApi._selectors.continueButton,
                this.handleContinueButtonClick.bind(this)
            );
            this.$nowWorkoutWrapper.on(
                'click',
                WorkoutApi._selectors.stopButton,
                this.handleStopButtonClick.bind(this)
            );
            this.$nowWorkoutWrapper.on(
                'click',
                WorkoutApi._selectors.resetButton,
                this.handleResetButtonClick.bind(this)
            );
		}

		static get _selectors() {
            return {
                deleteWorkoutButton: '.js-delete-workout',
                fastEditButton: '.js-edit-workout',
                cancelEditButton: '.js-edit-workout-cancel',
                submitEditButton: '.js-submit-edit-workout',
                newWorkoutForm: '.js-new-workout-form',
                startButton: '.js-start-button',
                pauseButton: '.js-pause-button',
                continueButton: '.js-continue-button',
                stopButton: '.js-stop-button',
                resetButton: '.js-reset-button',
                totalWorkouts: '.js-total-workouts',
                workoutNowActivity: '#js-workout-now-activity',
            }
        }

        handleDocumentLoad() { 
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
            this.$wrapper.find(WorkoutApi._selectors.totalWorkouts).html(
            	CalculateHelperInstances.get(this).getTotalWorkouts()
            );
            CalculateHelperInstances.get(this).getTotalEnergy();
            CalculateHelperInstances.get(this).getTotalDistance();
            CalculateHelperInstances.get(this).getTotalDuration();
        }

        handleNewWorkoutSubmit(event) {
        	event.preventDefault();
            const $form = $(event.currentTarget);
            const url = $form.data('url');
        	const formData = this.getDataFromForm($form);
            
        	this._saveWorkout(formData, url).then((data) => {
                this._addRow(data);
                this._clearForm();
            }).catch((errorData) => {
                if (errorData.type === 'form_validation_error') {
                    this._mapErrorsToForm(errorData);
                } else {
                    this.showErrorMessage(errorData.title);
                }
            });
        }

        getDataFromForm($form) {
            if(typeof window.FormData !== 'undefined') {
                return new FormData($form[0]);
            } else {
                //TO DO upload image
                var formData = {};
                for(let fieldData of $form.serializeArray()) {
                    if(fieldData.name.includes('durationSecondsTotal', 0)) {
                        formData = this.getDurationSecondsTotalData(formData, fieldData);
                    } else if(fieldData.name.includes('movementSets', 0)) {
                        var index = this.getIndexFromFieldName(fieldData.name);
                        index = parseInt(index);
                        formData = this.getMovementSetsData(formData, fieldData, index);
                    } else {
                        formData[fieldData.name] = fieldData.value;
                    }
                }
                return formData;
            }
        }

        getIndexFromFieldName(fieldName){
            var clearFieldName = fieldName.replace('[', "-");
            clearFieldName = clearFieldName.replace(']', "-");

            var subString = clearFieldName.match(new RegExp("movementSets-(.*?)-")); 
            return subString[1];
        }

        getDurationSecondsTotalData(formData, fieldData) {//to helper
            if (!formData['durationSecondsTotal']) {
                formData['durationSecondsTotal'] = {};
            }
                
            if (fieldData.name.includes('hour', 0)) {
                formData['durationSecondsTotal']['hour'] = fieldData.value;
            } else if (fieldData.name.includes('minute', 0)) {
                formData['durationSecondsTotal']['minute'] = fieldData.value;
            } else if (fieldData.name.includes('second', 0)) {
                formData['durationSecondsTotal']['second'] = fieldData.value;
            }
            return formData;
        }

        getMovementSetsData(formData, fieldData, index) {
            if (!formData['movementSets']) {
                formData['movementSets'] = {};
            } 
            if (!formData['movementSets'][index]) {
                formData['movementSets'][index] = {};
                formData['movementSets'][index]['durationSeconds'] = {};
            }

            //Pass form data to array
            if (fieldData.name.includes('activity', 0)) {
                formData['movementSets'][index]['activity'] = fieldData.value;
            } else if (fieldData.name.includes('hour', 0)) {
                formData['movementSets'][index]['durationSeconds']['hour'] = fieldData.value;
            } else if (fieldData.name.includes('minute', 0)) {
                formData['movementSets'][index]['durationSeconds']['minute'] = fieldData.value;
            } else if (fieldData.name.includes('second', 0)) {
                formData['movementSets'][index]['durationSeconds']['second'] = fieldData.value;
            }
            return formData;
        }

        _saveWorkout(data, url) {
            if (data instanceof FormData) {
                var options = {
                    type: 'post',
                    contentType: false,
                    processData: false,
                    data: data
                };
            } else {
                var options = {
                    data: JSON.stringify(data)
                };
            }
            return new Promise(function(resolve, reject) { 
                $.ajax({
                    url,
                    method: 'POST',
                    ...options
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

        _addRow(workout) {
  	    	const tplText = $('#js-workout-row-template').html();
        	const tpl = _.template(tplText);
            const html = tpl(workout);
            this.$wrapper.find('tbody').append($.parseHTML(html));
            this.setTooltips();
            this.updateTotalWorkouts();
        }

		_clearForm() {
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
        	$row.find(WorkoutApi._selectors.fastEditButton)
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
        	const id = $row.find(WorkoutApi._selectors.cancelEditButton).data('id');

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
        	const url = $row.find(WorkoutApi._selectors.submitEditButton).data('url');

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
            inputsData['_token'] = $('#js-token').val();
        	
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
                        <a href="#" class="js-delete-workout delete-item" data-url="${data['_links']['delete']['href']}"
                        data-id="${data['id']}" data-toggle="tooltip" data-placement="left" title="Delete">
                    	   <span class="fa fa-trash-alt"></span>
                        </a>
                    </div>
                	<div class="link-wrapper">
                        <a href="#" class="js-edit-workout" data-url="${data['_links']['edit']['href']}"
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

            if($row === null && $nowWorkoutWrapper === null) {
            	$form = this.$wrapper.find(WorkoutApi._selectors.newWorkoutForm);
            } else if($row === null) {
                $form = $nowWorkoutWrapper;
            } else {
            	$form = $row;
            }
           
            this._removeFormErrors($form);
            
            for (let element of $form.find(':input')) {
                	let fieldName = $(element).attr('name');
                	const $fieldWrapper = $(element).closest('.form-group');

                    if(fieldName === 'durationSecondsTotal[hour]') {
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
            var $startButton = this.$nowWorkoutWrapper.find(WorkoutApi._selectors.startButton);
            var $selectInput = this.$nowWorkoutWrapper.find(WorkoutApi._selectors.workoutNowActivity);

            if ($selectInput.val()) {
                var hours = 0;
                var minutes = 0;
                var totalSeconds = 0;
                var seconds = 0;
                var durationNow;

                this.toggleSelect($selectInput);
                counter = setInterval(startCount, 1000);
                function startCount() {
                    if(!counterPaused) {
                        seconds++;
                        totalSeconds++;
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
                    }
                }

                this.changeStartButton($startButton);
                this._removeFormErrors(this.$nowWorkoutWrapper);
            } else {
                this._mapErrorsToForm({activity: 'Please choose activity'}, null , this.$nowWorkoutWrapper);
            }
        }

        toggleSelect($selectInput) {
            $('label[for="js-workout-now-activity"]').toggle();
            $selectInput.toggle();
        }

        changeStartButton($startButton) {
            var newButtons = 
            `
                <div class="form-group">
                    <label class="required" for="js-workout-now-duration">Duration: </label>
                    <input type="text" class="form-control" name="durationSecondsTotal" id="js-workout-now-duration" disabled="disabled">
                </div>
                <div class="form-group">
                    <label class="required" for="distance-total-now">Distance: </label>
                    <input type="number" class="form-control" name="distanceTotal" id="distance-total-now" aria-describedby="distanceTotalTip">
                    <small id="distanceTotalTip" class="form-text text-muted">
                        Complete it when you finish workout.
                    </small>
                </div>
                <button class="btn btn-lg btn-green btn-block text-uppercase js-pause-button" type="button">Pause</button>
                <button class="btn btn-lg btn-primary btn-block text-uppercase js-stop-button" type="button">Stop && Save</button>  
                <button class="btn btn-lg btn-danger btn-block text-uppercase js-reset-button" type="button">Reset</button>
            `
            ;

            $startButton.replaceWith(newButtons);
        }

        handlePauseButtonClick() {
            counterPaused = true;
            var $pauseButton = this.$nowWorkoutWrapper.find(WorkoutApi._selectors.pauseButton);
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
            var $continueButton = this.$nowWorkoutWrapper.find(WorkoutApi._selectors.continueButton);
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
            var $pauseButton = this.$nowWorkoutWrapper.find(WorkoutApi._selectors.pauseButton);
            this.changePauseButton($pauseButton);

            this._getServerDate().then((today) =>{
                var inputsData = this._getDataFromFields(today);
                this._saveWorkoutNow(inputsData).then((data) => {
                    this._addRow(data);
                    clearInterval(counter);
                    counterPaused = false;
                }).catch((errorData) => {
                    if (errorData.type === 'form_validation_error') {
                        this._mapErrorsToForm(errorData, null , this.$nowWorkoutWrapper);
                    } else {
                        this.showErrorMessage(errorData.title);
                    }
                });
            }).catch((errorData) => {
                this.showErrorMessage(errorData.title);  
            });
        }

        _getDataFromFields(today) {
            const inputsData = {};
            inputsData['durationSecondsTotal'] = {};

            var $selectInput = this.$nowWorkoutWrapper.find(WorkoutApi._selectors.workoutNowActivity);
            inputsData['activityName'] = $selectInput.val();
            inputsData['type'] = 'Movement';

            var durationString = this.$nowWorkoutWrapper.find('#js-workout-now-duration').val();
            var distanceTotal = this.$nowWorkoutWrapper.find('#distance-total-now').val();
            const durationArray = durationString.split(' ');

            inputsData['durationSecondsTotal']['hour'] = durationArray[0];
            inputsData['durationSecondsTotal']['minute'] = durationArray[2];
            inputsData['durationSecondsTotal']['second'] = durationArray[4];
            inputsData['distanceTotal'] = distanceTotal;
            inputsData['startAt'] = today;

            inputsData['_token'] = $('#js-token').val();

            return inputsData;
        }

        _getServerDate() {
            return new Promise(function(resolve, reject) {
                const url = '/api/server_date';
                $.ajax({
                    url,
                    method: 'GET'
                }).then(function(today) {
                    resolve(today);
                }).catch(function(jqXHR) {
                    let errorData = getStatusError(jqXHR);
                    if(errorData === null) {
                        errorData = JSON.parse(jqXHR.responseText);
                    }
                    reject(errorData);
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
                    let errorData = getStatusError(jqXHR);
                    if(errorData === null) {
                        errorData = JSON.parse(jqXHR.responseText);
                    }
                    reject(errorData);
                });
            });
        }

        handleResetButtonClick() {
            clearInterval(counter);
            counterPaused = false;
            var input = 
            `
                <button class="btn btn-lg btn-green btn-block text-uppercase js-start-button" type="button">Start</button>
            `
            ;
            var $selectInput = this.$nowWorkoutWrapper.find(WorkoutApi._selectors.workoutNowActivity);
            this.toggleSelect($selectInput);
            this.$nowWorkoutWrapper.find('#js-workout-now-wrapper').html(input);
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
            
            this.$wrapper.find('.js-total-distance').html(sum.toFixed(2));
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
