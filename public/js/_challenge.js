import { getStatusError } from './_apiHelper.js';

'use strict';

$(document).ready(function()
{
    $('#js-join').on('click', (event) => {
        event.preventDefault();
        var $link = $(event.currentTarget);

        sendParticipate($link.attr('href')).then((data) => {
            $link.replaceWith('<span>Joined</span>');
        }).catch((errorData) => {
            showErrorMessage(errorData.title);
        });
    });
});

function sendParticipate(url) {
    return new Promise(function(resolve, reject) { 
        $.ajax({
            url,
            method: 'POST'
        }).then((data) => {
            resolve(data);
        }).catch((jqXHR) => {
            let errorData = getStatusError(jqXHR);
            if(errorData === null) {
                errorData = JSON.parse(jqXHR.responseText);
            }
            reject(errorData);
        });
    });
}

function showErrorMessage(errorMessage) {
    Swal.fire({
        type: 'error',
        title: 'Oops...',
        text: `${errorMessage}`,
    });
}