import { getStatusError } from './_apiHelper.js';

'use strict';

$(document).ready(function() {
    $('.counter-js').counterUp({
        delay: 10,
        time: 2000
    });

    var loadingFlag = false;
    const section = document.querySelector('#loadingWrapper-js');
    
    const intersectionCallback = (entries, observer) => {
        if (entries[0].intersectionRatio <= 0) {
            return;
        }
        if(entries[0].intersectionRatio > 0.75 && loadingFlag === false) {
            getWorkouts().then((workouts) => {
                showLoading();
                addWorkoutCards(workouts);
                removeLoadingIcon();
            }).catch((errorData) => {
                removeLoadingIcon(errorData.title);
                observer.unobserve(section);
            });
        }
    };

    const intersectionOptions = {
        threshold: 1,
        rootMargin: '0px 0px 250px 0px'
    };

    const intersectionObserver = new IntersectionObserver(intersectionCallback, intersectionOptions);

    intersectionObserver.observe(section);

    function showLoading() {
        loadingFlag = true;
        var $loadingWrapper = $('#loadingWrapper-js');

        var $loadingIcon = `
        <div class="js-loading text-center">
            <span>Loading more workouts</span>
            <span class="fas fa-spinner fa-spin"></span>

        </div>
        `;
        $loadingWrapper.append($loadingIcon); 
    }

    function removeLoadingIcon(message = null) {
        if($('.js-loading').length) {
            $('.js-loading').remove();
        }

        if (message != null) {
            const $loadingWrapper = $('#loadingWrapper-js');
            $loadingWrapper.append(`<p class="text-center">${message}</p>`)
        }

        loadingFlag = false;
    }


});

function addWorkoutCards(workouts) {
    workouts.forEach((workout) => {
        addWorkoutCard(workout);
    });
}

function addWorkoutCard(workout) {
    console.log(workout);
    const $loadingWrapper = $('#loadingWrapper-js');
    const tplText = $('#js-workout-card-template').html();
    const tpl = _.template(tplText);
    const html = tpl(workout);
    $loadingWrapper.before($.parseHTML(html));
}

function getWorkouts() {   
    const url = $('#loadingWrapper-js').data('url');
    const lastDate = $('.workout-date-js').last().attr("data-date");

    return new Promise(function(resolve, reject) {
        $.ajax({
            url,
            method: 'POST',
            data: JSON.stringify(lastDate)
        }).then(function(data){
            resolve(data);
        }).catch(function(jqXHR){
            let statusError = [];
            statusError = getStatusError(jqXHR);
            if(statusError != null) {
                reject(statusError);
            } else {
                const errorData = JSON.parse(jqXHR.responseText);
                reject(errorData);
            }
        });
    });
}
