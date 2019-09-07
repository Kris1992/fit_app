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
                    return this._deleteWorkout($link);
                }

			})

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
            CalculateHelperInstances.get(this).getTotalEnergy();
            CalculateHelperInstances.get(this).getTotalDuration();
        }

        handleNewWorkoutSubmit(e){
        	e.preventDefault();
        	const $form = $(e.currentTarget);
        	const formData = {};
        	formData['duration'] = {};

        	for(let fieldData of $form.serializeArray())
        	{
        		

        		if(fieldData.name == 'duration[hour]')
        		{
        			formData['duration']['hour'] = fieldData.value;
        			
        		}
        		else if(fieldData.name == 'duration[minute]')
        		{
        			formData['duration']['minute'] = fieldData.value;
        		}
        		else
        		{
        			formData[fieldData.name] = fieldData.value;
        		}

        	}

        	//console.log(formData);
        	

        	this._saveWorkout(formData, $form).then((data) => {
        		//console.log(data);
        		this._addRow(data);
        		this._clearForm();
        		CalculateHelperInstances.get(this).getTotalEnergy();
        		CalculateHelperInstances.get(this).getTotalDuration();
        	}).catch((errorData) => {

        		//console.log(errorData);
                
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

        handleEditWorkout(e){
        	e.preventDefault();
        	const $link = $(e.currentTarget);
        	this._editWorkoutToForm($link);
        }
        _editWorkoutToForm($link){
        	$link.find('.fa')
        		.removeClass('fa-pencil')
        		.addClass('fa-save');
        	const $row = $link.closest('tr');
        	$row.find('.js-edit-workout')
        		.removeClass('js-edit-workout')
        		.addClass('js-submit-edit-workout');
        	const $tds = $row.find('td');
        	const data = {};
        	let activity_id = 0;
        	var i = 0;
 			for (var cell of $tds){
 				data[i] = $(cell).html();
 				if($(cell).data('id'))
 				{
 					activity_id = $(cell).data('id');
 				}
 				i++;
 			}


 			var $options = $("#activity > option").clone();
    		$options[0].text = data[1];
        	$options[0].value = activity_id;

 			//console.log(activity_id);
        	
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
						<input type="text" name="duration" class="form-control form-control-sm" value="${data[2]}">
					</div>
				</td>
				<td>${data[3]}</td>
				<td>
					${data[4]}
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

        handleEditWorkoutCancel(e)
        {
        	e.preventDefault();
        	const $link = $(e.currentTarget);

        	this._getWorkout($link).then((data) => {
        		this._EditWorkoutToText($link, data);
        	})
        }
        _getWorkout($link){
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

        handleEditWorkoutSubmit(e){
        	e.preventDefault();
        	const $link = $(e.currentTarget);
        	const $row = $link.closest('tr');
        	const url = $row.find('.js-submit-edit-workout').data('url');

        	const duration = $row.find('[name=duration]').val();
        	const durationArray = duration.split(':');
        	const ex = new RegExp(/^0{1}.{1}/);
        	for (var i = 0; i < 2; i++) {
        		if(durationArray[i].match(ex))
        		{	
        			console.log('catch');
        			durationArray[i] = durationArray[i].substring(1);
        		}
        	}

        	const inputsData = {};
        	inputsData['duration'] = {};

        	inputsData['activity'] = $row.find('[name=activity]').children("option:selected").val();
        	inputsData['duration']['hour'] = durationArray[0];
        	inputsData['duration']['minute'] = durationArray[1];


        	

        	this.updateWorkout(inputsData, url).then((data) => {
        		this._EditWorkoutToText($link, data);
        		CalculateHelperInstances.get(this).getTotalEnergy();
        		CalculateHelperInstances.get(this).getTotalDuration();
        	}).catch((errorData) => {
                this._mapErrorsToForm(errorData, $row);
            })

        	
        }
        updateWorkout(data, url){
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

        _EditWorkoutToText($link, data){
        	const $row = $link.closest('tr');
        	//console.log(data);
        	var newRow = 
        	`<tr>
				<td>${data['id']}</td>
				<td data-id="${data['activity']['id']}">${data['activity']['name']}</td>
				<td class="js-duration">${data['time']}</td>
				<td class="js-energy">${data['burnoutEnergy']}</td>
				<td>
					<a href="#" 
					class="js-delete-workout"
                	data-url="${data['links']['delete']}"
                	data-id="${data['id']}"
                	>
                    	<span class="fa fa-trash"></span>
                	</a>
                	<a href="#"
                	class="js-edit-workout"
                	data-url="${data['links']['edit']}"
                	>
                    	<span class="fa fa-pencil"></span>
                	</a>
				</td>
			</tr>`
        	;
			$row.replaceWith(newRow);

        }

        _mapErrorsToForm(errorData, $row = null){

        	let $form;

            if($row == null)
            {
            	$form = this.$wrapper.find(WorkoutApi._selectors.newWorkoutForm);
            }
            else
            {
            	$form = $row;
            }
           
            this._removeFormErrors($form);
            
            for (let element of $form.find('select') && $form.find(':input')) 
                {
                	let fieldName = $(element).attr('name');
                	const $wrapper = $(element).closest('.form-group');

                	if(fieldName == 'duration[hour]')
                	{
                		fieldName = 'duration';
                	}

                	if (!errorData[fieldName]) {
                    	continue;
                	}
                	const $error = $('<span class="js-field-error help-block text-danger"></span>');
                	$error.html(errorData[fieldName]);
                	$wrapper.append($error);
                	$wrapper.addClass('has-error');
            };

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

        calculateTotalWorkouts(){
        	let workouts = this.$wrapper.find('tbody tr').length;

        	return workouts;

        }

        getTotalEnergy()
        {
        	let sum = 0;
        	for (let energy of this.$wrapper.find('.js-energy') )
        	{
        		sum = sum + parseInt($(energy).html());
        	}
        	
        	this.$wrapper.find('.js-total-energy').html(sum);
        }

        getTotalDuration()
        {
        	let sumHour = 0;
        	let sumMin = 0;
			
			for (let duration of this.$wrapper.find('.js-duration') )
        	{
        		let durationString = $(duration).html();

        		const durationArray = durationString.split(':');
        		const ex = new RegExp(/^0./);
        			for (var i = 0; i < 2; i++) {
        				if(durationArray[i].match(ex))
        				{
        					durationArray[i] = durationArray[i].substring(1);
        				}
        			}

        		sumHour = sumHour + parseInt(durationArray[0]);
        		sumMin = sumMin + parseInt(durationArray[1]);
        	}
        	sumHour = sumHour + parseInt(sumMin/60);
        	sumMin = sumMin%60;
        	
        	this.$wrapper.find('.js-total-duration').html(sumHour+'h '+sumMin+'min');
        }

	}

	window.WorkoutApi = WorkoutApi;

})(window, jQuery, Swal);