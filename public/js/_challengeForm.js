import { getStatusError } from './_apiHelper.js';

'use strict';

$(document).ready(function() {
    var $typeSelect = $('.js-challenge-form-type');
    var $selectActivityForm = $('.js-select-activity-form');
    $typeSelect.on('change', (event) => {
        $.ajax({
            url: $typeSelect.data('activity-select-url'),
            data: {
                activityType: $typeSelect.val()
            },
            success: function (html) {
                if (!html) {
                    $selectActivityForm.empty();
                    $selectActivityForm.addClass('d-none');
                    return;
                }

                // Replace the current field and show
                $selectActivityForm
                    .html(html)
                    .removeClass('d-none')
            },
            error: function(error) {
                let errorData = getStatusError(error);
                if(errorData === null) {
                    errorData = JSON.parse(error.responseText);
                } 
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: `${errorData.title}`,
                }); 
            }
        });
    });
});