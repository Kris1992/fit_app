import { getStatusError } from './_apiHelper.js';

'use strict';

$(document).ready(function()
{
    $('#js-workout-cards-wrapper').on('click', '.js-like', (event) => {
        event.preventDefault();
        var $link = $(event.currentTarget);
        
        var reaction = {
            type: 1,
        };

        sendReaction(reaction, $link.attr('href')).then((data) => {
            $link.find('span').toggleClass('far').toggleClass('text-primary fas');
            $link.closest('.js-reactions').find('.js-like-count').html(data.count);
        }).catch((errorData) => {
            showErrorMessage(errorData.title);
        });
    });

    $('#js-workout-cards-wrapper').on('click', '.js-love', (event) => {
        event.preventDefault();
        var $link = $(event.currentTarget);
        
        var reaction = {
            type: 2,
        };

        sendReaction(reaction, $link.attr('href')).then((data) => {
            $link.find('span').toggleClass('far').toggleClass('text-danger fas');
            $link.closest('.js-reactions').find('.js-love-count').html(data.count);
        }).catch((errorData) => {
            showErrorMessage(errorData.title);
        });
    });
});

function sendReaction(reaction, url) {
    return new Promise(function(resolve, reject) { 
        $.ajax({
            url,
            method: 'POST',
            data: JSON.stringify(reaction)
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