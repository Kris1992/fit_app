import { getStatusError } from './_apiHelper.js';

'use strict';

$(document).ready(function()
{
    $('.js-friend').on('click', (event) => {
        event.preventDefault();
        var $link = $(event.currentTarget);

        console.log($link.attr('href'));
        //if($link.find('span').hasClass("text-green")) {
        //    showErrorMessage('You already invited this person.')
        //}

        sendInvitation($link.attr('href')).then((data) => {
            $link.find('span').addClass('text-green');
        }).catch((errorData) => {
            console.log(errorData);
            showErrorMessage(errorData.title);
        });
    });

});

function sendInvitation(url) {
    return new Promise(function(resolve, reject) { 
        $.ajax({
            url,
            method: 'GET',
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