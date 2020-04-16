'use strict';

$(document).ready(function() {
    var $typeSelect = $('.js-activity-form-type');
    var $specificActivityForm = $('.js-specific-activity-form');
    $typeSelect.on('change', (event) => {
        $.ajax({
            url: $typeSelect.data('specific-activity-url'),
            data: {
                type: $typeSelect.val()
            },
            success: function (html) {
                if (!html) {
                    $specificActivityForm.empty();
                    $specificActivityForm.addClass('d-none');
                    return;
                }

                // Replace the current field and show
                $specificActivityForm
                    .html(html)
                    .removeClass('d-none')
            }
        });
    });
});