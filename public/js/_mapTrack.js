'use strict';

//config must be impelemented here
const platform = configPlatform();
const defaultLayers = configLayers();

//Marker with actual position of user
var actualPosMarker = null; 
var watchID;
//var markers = [];

var counter;
var counterPaused = false;
var track = false;
var durationSecondsTotal = 0;
var distanceTotal = 0;
var lastRoutesDistance = 0;
var activityData = null;

//All waypoints from all routes (used to recalculate routes and take routesData on the end)
var waypoints = [];
var waypointsIndex = 0;
var routePolylines = [];


// map instantiate must be global too
var map = new H.Map(
    document.getElementById('mapContainer'),
    defaultLayers.vector.normal.map,
    {
      zoom: 15,
    }
);

var ui = H.ui.UI.createDefault(map, defaultLayers);
var mapEvents = new H.mapevents.MapEvents(map);
var behavior = new H.mapevents.Behavior(mapEvents);
var icon  = setCustomMarker();

var routingService = platform.getRoutingService();

initMapView();

$(document).ready(function() {
    //event listeners
    $("#continue-js").on("click", sendActivity);
    $("#remove-message-js").on("click", removeMapResponse);
});

function initMapView() {
    if(navigator.geolocation) {
        watchActualPosition();
    }  else {
        alert('Geolocation is not supported.');
    }
}

/**
 * navigationError Show navigation error on panel
 * @param error Error from navigator 
 */
function navigationError(error) {
  //console.warn('ERROR(' + error.code + '): ' + error.message);
  showMapResponse('Gps connection lost...');
}

/**
 * calculateDistance Calculate route and get distance and shape of route
 */
function calculateDistance()
{
    if(waypoints[waypointsIndex].length > 1) {
        calculateRouteData(waypoints[waypointsIndex]).then((result) => {
            showDistance(result);
        }).catch((errorData) => {
            showMapResponse('Cannot calculate route.');
        });            
    }
}

/**
 * calculateRouteData Prepare and do call to api
 * @param  {Array} routeData Data of route to calculate
 * @param  {Boolean} needDraw Need to draw calculated route on the map? [optional]
 * @param  {Boolean} needElevation   Do you want to get elevation data (altitude)? [optional]
 */
function calculateRouteData(routeData, needDraw = true, needElevation = false)
{
    return new Promise( (resolve, reject) => {
        var routingParameters = {
            mode: 'shortest;pedestrian',
            routeattributes : 'summary,shape',
            representation: 'display',
            returnElevation: needElevation
        };

        //Add all waypoints to route
        var index = 0;
        routeData.forEach( (waypoint) => {
            routingParameters['waypoint' + index++] = 'geo!' + waypoint.coord.lat + ',' + waypoint.coord.lng;
        });

        var routingService = platform.getRoutingService();
        routingService.calculateRoute(routingParameters, result => {
            let response = result.response;
            if (response.route[0]) {
                var lineString = new H.geo.LineString();
                response.route[0].shape.forEach(routeCoord => {
                    var routeCoordArray = routeCoord.split(',');
                    lineString.pushLatLngAlt(routeCoordArray[0], routeCoordArray[1], 0);
                });

                if (needDraw) {
                    var polyline = new H.map.Polyline(
                        lineString, 
                        {
                            style: 
                                {
                                    strokeColor: 'rgb(0, 130, 130)',
                                    lineWidth: 2
                                }
                        }
                    );
                    routePolylines.push(polyline); 
                    map.addObject(polyline);
                }

                resolve(response.route[0]);
            }
        }, 
            error => { 
                reject(error);
            }
        );
    });
}

/**
 * calculateBurnoutEnergy Calculate burnout energy and show it on panel
 */
function calculateBurnoutEnergy()
{
    var averageSpeed = Math.floor(distanceTotal/(durationSecondsTotal/3600));
    var energyNow;
    activityData.forEach((activity) => {
        if (averageSpeed >= activity.speedAverageMin && averageSpeed <= activity.speedAverageMax) {
            energyNow = Math.floor(activity.energy * durationSecondsTotal/3600) +' kcal';
            document.getElementById('burnout-js').innerHTML = energyNow;
        }
    });    
}

/**
 * showDistance Show distance on panel 
 * @param routeData Data of route given by calculateRoute response
 */
function showDistance(routeData)
{   
    var routeDistanceTotal = routeData.summary.distance;
    distanceTotal = (routeDistanceTotal/1000) + lastRoutesDistance;

    if (distanceTotal < 1000) {
        document.getElementById('distance-js').innerHTML = (distanceTotal*1000)+" m";
    } else {
        document.getElementById('distance-js').innerHTML = distanceTotal+" km";
    }

    calculateBurnoutEnergy();
}

/**
 * showActualPosition Show current position of user by set marker on map
 * @param pos Calculated position by navigator
 */
function showActualPosition(pos) {
    var currentCoords = pos.coords;
    map.setCenter({lat: currentCoords.latitude, lng: currentCoords.longitude});

    if (actualPosMarker === null) {
        actualPosMarker = new H.map.Marker(
            { 
                lat: currentCoords.latitude,
                lng: currentCoords.longitude 
            },
            { 
                icon: icon 
            });

        map.addObject(actualPosMarker);
    } else {
        actualPosMarker.setGeometry({lat: currentCoords.latitude, lng: currentCoords.longitude});
    }

    //Push this position to route data
    if(track && !counterPaused) {
        addPointToRouteAndCalculate(currentCoords);
    }
}

/**
 * addPointToRouteAndCalculate Add actual postion point to route and calculate whole distance 
 * @param coords Actual position coords
 */
function addPointToRouteAndCalculate(coords)
{
    //Create array in waypoints
    if (!waypoints[waypointsIndex]) {
        waypoints[waypointsIndex] = [];
    }

    var lastWaypoint = waypoints[waypointsIndex].slice(-1)[0];

    //If position is the same like last one do nothing
    if (!lastWaypoint || (
        lastWaypoint.coord.lat !== coords.latitude || 
        lastWaypoint.coord.lng !== coords.longitude
        )) {

        waypoints[waypointsIndex].push({
            coord: {
                lat: coords.latitude,
                lng: coords.longitude
            }
        });
        calculateDistance();
    }
}

/**
 * sendActivity Send choosen activity name to server and get array of records
 * @param event  
 */
function sendActivity(event)
{
    disableElement($(event.target));
    event.preventDefault();
    removeFormErrors();
    
    var fieldNames = ['activityName', 'type'];
    var formData = getDataFromForm(fieldNames);
    if (!formData['activityName']) {
        var errorData = {};
        errorData['activityName'] = 'Please choose activity.';
        mapErrorsToForm(errorData);
    } else {
        var url = document.getElementById('continue-js').getAttribute('data-url');
        getActivityData(formData,url).then((result) => {
            activityData = result;
            var isDuplicate = addButton('button', 'success', 'Start tracking', 'trackme-js');
            /*
            If I add event listener by JQuery and use remove on element the 
            listener will be destroyed too
            */
            if (!isDuplicate) {
                $("#trackme-js").on("click", startTrackPosition);
            }
        }).catch((errorData) => {
            showMapResponse(errorData.title);                    
        });
    }
    
    enableElement($(event.target));
}

/**
 * getDataFromForm Get data from form 
 * @param fieldNames Array with names of fields to get[optional]
 */
function getDataFromForm(fieldNames = null)
{
    const $form = $('.js-new-workout-form');
    var formData = {};

    if (fieldNames === null) {
        //Group formData because I don't want allow_extra_fields in form
        formData['formData'] = {}
        for(let fieldData of $form.serializeArray()) { 
            formData['formData'][fieldData.name] = fieldData.value;
        }
        return formData;    
    } else {
        for(let fieldData of $form.serializeArray()) {
            if (fieldNames.includes(fieldData.name)) {
                formData[fieldData.name] = fieldData.value;
            }
        }
        return formData;    
    }
}

/**
 * saveWorkout Send data to server and save workout to database
 * @param data Workout data
 * @param url  Path to server method
 */
function saveWorkout(data, url) {
    return new Promise( (resolve, reject) => {
        $.ajax({
            url,
            method: 'POST',
            data: JSON.stringify(data)
        }).then((result) => {
            resolve(result);
        }).catch((jqXHR) => {
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

/**
 * startTrackPosition Start tracking position
 * @param event
 */
function startTrackPosition(event) {
    removeElement($(event.target));
    removeElement($("#continue-js"));
    replaceSelect($("#activityName"));
    addButton('button', 'warning', 'Pause', 'pause-track-js');
    $('#pause-track-js').on("click", pauseTracking);
    addButton('submit', 'danger', 'Stop', 'stop-track-js');
    $('#stop-track-js').on("click", stopTracking);
    
    //Start tracking route
    getServerDate($('#startAt').data('url')).then((today) => {
        $('#startAt').val(today);
        trackRoute();
    }).catch((errorData) => {
        showMapResponse(errorData.title);
    });
}

/**
 * pauseTracking Pause tracking system
 * @param event 
 */
function pauseTracking(event)
{
    counterPaused = true;
    removeElement($(event.target));
    addButton('button', 'info', 'Restart', 'restart-track-js', true);
    $('#restart-track-js').on("click", restartTracking);

    //Reset current route (if user restart tracking it will be calculate new route)
    waypointsIndex++;
    lastRoutesDistance = distanceTotal;

}

/**
 * restartTracking Restart tracking system
 * @param event 
 */
function restartTracking(event)
{
    counterPaused = false;
    removeElement($(event.target));
    addButton('button', 'warning', 'Pause', 'pause-track-js', true);
    $('#pause-track-js').on("click", pauseTracking);
}

/**
 * stopTracking Stop tracking system and save data to db 
 * @param event
 */
function stopTracking(event)
{   
    event.preventDefault();
    counterPaused = true;
    if(distanceTotal > 0) {
        disableElement($(event.target));
        disableElement($('#pause-track-js'), false);
        disableElement($('#restart-track-js'), false);
        $('#durationSecondsTotal').val(durationSecondsTotal);

        calculateAllRoutesData();
    } else {
        showMapResponse('Your distance is equal 0.');
        if ($('#pause-track-js').length > 0) {
            removeElement($('#pause-track-js'));
            addButton('button', 'info', 'Restart', 'restart-track-js', true);
            $('#restart-track-js').on("click", restartTracking);
        }
    }
}

function calculateAllRoutesData()
{
    Promise.all(waypoints.map((waypoint) => {
            return calculateRouteData(waypoint, false, true);
    })).then((routes) => {
        var routesData = [];
        routes.map((route) => {
            routesData.push(...route.shape);
        });
        captureImageAndSaveWorkout(routesData);
    }).catch((errorData) => {
        showMapResponse('Cannot recalculate route. Try again.');
        enableElement($('#stop-track-js'));
    });
}

/**
 * captureImageAndSaveWorkout Get image of workout and send all data to database
 * @param  {Array} routeData Array with route data
 */
function captureImageAndSaveWorkout(routesData)
{
    map.capture((canvas) => {
        if (canvas) {
            //in future better solution
            var formData = getDataFromForm();
            var mapImage = canvas.toDataURL();
            formData['image'] = mapImage;
            formData['distanceTotal'] = distanceTotal;
            formData['routeData'] = routesData;

            saveWorkout(formData,$('#buttons-panel-js').data('url')).then((result) => {
                navigator.geolocation.clearWatch(watchID);
                window.location.href = result.url;
            }).catch((errorData) => {
                showMapResponse(errorData.title);
                enableElement($('#stop-track-js'));
            });
        } else {
            showMapResponse('Capturing is not supported.');
            enableElement($('#stop-track-js'));
        }
    }, []);
}

/**
 * replaceSelect Replace select input with text with value of it 
 * @param $select JQuery handler select input
 */
function replaceSelect($select)
{
    $select.hide();
    var selectValue = $select.val();    
    var span = `<span class="d-block text-center text-uppercase">${selectValue}</span>`;
    $select.closest("div").append(span);
}

/**
 * getServerDate Get actual date from server
 * @param url
 */
function getServerDate(url) {
    return new Promise(function(resolve) {
        $.ajax({
            url,
                method: 'GET'
            }).then(function(today) {
                resolve(today);
            }).catch((jqXHR) => {
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

/**
 * getActivityData Get array activities with the same name and type
 * @param data Array with type activity and name
 * @param url  
 */
function getActivityData(data, url) {
    return new Promise( (resolve, reject) => {
        $.ajax({
            url,
            method: 'POST',
            data: JSON.stringify(data)
        }).then((result) => {
            resolve(result);
        }).catch((jqXHR) => {
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

/**
 * getStatusError Get http error code and transform it to proper message
 * @param jqXHR 
 * @return errorMessage || null
 */
function getStatusError(jqXHR) {
    if (jqXHR.getResponseHeader('content-type') === 'application/problem+json') {
        return null;
    }
    if(jqXHR.status === 0) {
        return {
            "title":"Cannot connect. Verify Network."
        }
    } else if(jqXHR.status === 404) {
        return {
            "title":"Requested not found."
        }
    } else if(jqXHR.status === 500) {
        return {
            "title":"Internal Server Error."
        }
    } else if(jqXHR.status > 400) {
        return {
            "title":"Error. Contact with admin."
        }
    }
    return null;
}

/**
 * watchActualPosition Start watching position of user
 */
function watchActualPosition()
{
    var options = {
        timeout:60000,
        enableHighAccuracy: true
    };
    watchID = navigator.geolocation.watchPosition(showActualPosition, navigationError, options);
}

/**
 * trackRoute Start tracking route
 */
function trackRoute()
{
    var hours = 0;
    var minutes = 0;
    var seconds = 0;
    var durationNow;
    var energyNow = 0 +' kcal';
    track = true;

    counter = setInterval(startCount, 1000);
    function startCount() {
        if(!counterPaused) {
            seconds++;
            durationSecondsTotal++;

            if (!(seconds%10)) {
                updateSpeedAverage();
            }

            if(seconds/60 == 1 ) {
                seconds = 0;
                minutes++;
            }
            
            if(minutes/60 == 1) {
                minutes = 0;
                hours++;
            }

            durationNow = hours+' h '+minutes+' min '+seconds+' sec ';
            document.getElementById('duration-js').innerHTML = durationNow;
        }
    }
}

/**
 * updateSpeedAverage Calculate and show speed average
 */
function updateSpeedAverage()
{
    var averageSpeed = (distanceTotal/(durationSecondsTotal/3600)).toFixed(1)+' km/h';
    document.getElementById('speed-js').innerHTML = averageSpeed;
}

/**
 * addButton Add button with given type and text 
 * @param type Type of button e.g submit, button
 * @param bootstrapType Bootstrap type of button e.g success, danger
 * @param text Text inside button
 * @param id Id of button [optional]
 * @param firstChild It should be first button in panel? [optional]
 * @return {Boolean} Returns true if button was created before otherwise return false
 */
function addButton(type, bootstrapType, text, id = '', firstChild = false)
{
    if (!($('#'+id).length) || id == '') {
        var $button = 
            `
                <button type="${type}" class="btn btn-block btn-${bootstrapType} mt-2" id="${id}">
                    ${text}
                </button>
            `
        if (firstChild) {
            $('#buttons-panel-js').prepend($button);
        } else {
            $('#buttons-panel-js').append($button);
        }

        return false;
    } else {
        return true;
    }
}

/**
 * removeElement Remove button or other DOM element
 * @param  $element JQuery element handler
 */
function removeElement($element)
{
    $element.remove();
}

/**
 * disableElement Disable given button or other DOM element and if is needed add to it loading icon
 * @param  $element  JQuery DOM element handler
 * @param  loadIcon Add load icon or not?
 */
function disableElement($element, loadIcon = true)
{   
    $element.prop('disabled', true);
    if (loadIcon) {
        $element.append('<span class="fas fa-spinner fa-spin"></span>');
    }
}

/**
 * enableElement Enable DOM element
 * @param $element JQuery DOM element handler
 */
function enableElement($element)
{
    $element.prop('disabled', false);
    if ($element.children('.fa-spinner').length > 0) {
        $element.children('.fa-spinner').remove();
    }
}

/**
 * mapErrorsToForm Map validation errors to form fields
 * @param errorData Array with validation errors
 */
function mapErrorsToForm(errorData)
{
    var $form = $('.js-new-workout-form');

    for (let element of $form.find(':input')) {
        let fieldName = $(element).attr('name');
        const $fieldWrapper = $(element).closest('.form-group');

        if(fieldName == 'durationSecondsTotal[hour]') {
            fieldName = 'durationSecondsTotal';
        }

        if (!errorData[fieldName]) {
            continue;
        }

        const $error = $('<span class="js-field-error help-block text-danger"></span>');
        $error.html(errorData[fieldName]);
        $fieldWrapper.append($error);
        $fieldWrapper.addClass('has-error');
    }
}

/**
 * removeFormErrors Remove errors from form
 */
function removeFormErrors() {
    var $form = $('.js-new-workout-form');
    $form.find('.js-field-error').remove();
    $form.find('.form-group').removeClass('has-error');
}

/**
 * showMapResponse Show responses on the info panel
 * @param message String with message to display
 */
function showMapResponse(message)
{   
    $("#info-message-js").html(message);
    $('#info-panel').fadeIn('slow');    
}

/**
 * removeMapResponse Remove response from info panel 
 */
function removeMapResponse()
{
    var $infoPanel = $("#info-panel");
    var $infoMessage = $("#info-message-js");
    $infoPanel.fadeOut(500, () => {
        $infoMessage.html('');
    });
}

/*
 Config functions 
 */

function configPlatform() {
    return new H.service.Platform({
        'apikey': window.apikey,
    });
}

function configLayers() {
    return platform.createDefaultLayers();
}

/**
 * setCustomMarker Set custom marker icon
 * @return markerIcon 
 */
function setCustomMarker() {
    var svgMarkup = '<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg">' +
    '<circle fill="royalblue" cx="7" cy="7" r="7" /></svg>';

    return new H.map.Icon(svgMarkup);
}