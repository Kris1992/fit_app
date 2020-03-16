'use strict';

var $collectionWrapper;
var $addNewSetButton = $(`<a href="#"" class="btn btn-info my-2">Add set</a>`);
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
        $workoutSetsForm.on(
                'click',
                '.remove-js',
                handleRemoveButtonClick.bind(this)
                );
    }
    
    
});

function initCollections() {
    $collectionWrapper = $('#workout_sets');
    $collectionWrapper.append($addNewSetButton);
    $collectionWrapper.data('counter', $collectionWrapper.find('.card-js').length)
    $collectionWrapper.find('.card-js').each(() => {   
        addRemoveButton($(this));
    });

    $addNewSetButton.click((event) => {
        event.preventDefault();
        addNewForm();
    });


}

//add new sets to movementSet
function addNewForm() {
    var newForm = $collectionWrapper.data('prototype');
    var counter = $collectionWrapper.data('counter');

    newForm = newForm.replace(/__name__/g, counter);

    var $card = $(`
        <div class="card my-2 card-js">
            <div class="card-header bg-info text-white">
                <strong>SET ${counter}</strong>
            </div>
        </div>`
    );
    var $cardBody = $(`<div class="card-body"></div>`).append(newForm);

    $card.append($cardBody);

    $collectionWrapper.data('counter', counter+1);

    addRemoveButton($card);

    $addNewSetButton.before($card);
}

//remove set from movementSet
function addRemoveButton($setWrapper) {
    var $removeButton = $(`<a href="#" class="btn btn-danger remove-js">Remove set</a>`);
    var $footerCard = $(`<div class="card-footer text-center"></div>`).append($removeButton);
    $setWrapper.append($footerCard);
}

function handleRemoveButtonClick(event)
{
    event.preventDefault();
    $(event.target).parents('.card-js').slideUp("normal").promise().done(function() {
        $(this).remove();
    });
}
