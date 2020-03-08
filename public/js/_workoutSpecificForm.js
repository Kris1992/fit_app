$(document).ready(function() {
    var $activityNameSelect = $('.js-activity-name-form');
    var $specificWorkoutForm = $('.js-additional-data-workout-specific-form');
    $activityNameSelect.on('change', (event) => {
        $.ajax({
            url: $activityNameSelect.data('specific-workout-url'),
            data: {
                activityName: $activityNameSelect.val()
            },
            success: function (html) {
                if (!html) {
                    $specificWorkoutForm.empty();
                    $specificWorkoutForm.addClass('d-none');
                    return;
                }

                // Replace the current field and show
                $specificWorkoutForm
                    .html(html)
                    .removeClass('d-none')
            }
        });
    });
});