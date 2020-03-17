import { initCollections, addNewForm, addRemoveButton, handleRemoveButtonClick } from './_formHelper.js';

'use strict';

$(document).ready(function() {
    // Get additional fields by activity type
    var $activitySelect = $('.js-activity-form');
    var $workoutSetsForm = $('#workout-sets-wrapper-js');
    
    $activitySelect.on('change', (event) => {
        $.ajax({
            url: $activitySelect.data('workout-sets-url'),
            data: {
                id: $activitySelect.val()
            },
            success: function (html) {
                if (!html) {
                    $workoutSetsForm.empty();
                    $workoutSetsForm.addClass('d-none');
                    return;
                }

                // Replace the current field and show
                $workoutSetsForm
                    .html(html)
                    .removeClass('d-none');

                if($workoutSetsForm.find("#workout_sets").length > 0) {
                    initCollections();
                }
            }
        });
    });

    //If edit
    if($workoutSetsForm.find("#workout_sets").length > 0) {
        initCollections();
    }

    $workoutSetsForm.on(
        'click',
        '.remove-js',
        handleRemoveButtonClick.bind(this)
    );
 
});

