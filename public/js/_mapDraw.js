'use strict';

//config must be impelemented here
const platform = configPlatform();
const defaultLayers = configLayers();
var searchMarker;
var markers = [];
var waypoints = [];
var routePolylines = [];
var distanceTotal = null;

// map instantiate must be global too
var map = new H.Map(
    document.getElementById('mapContainer'),
    defaultLayers.vector.normal.map,
    {
      zoom: 10,
    }
);

var ui = H.ui.UI.createDefault(map, defaultLayers);
var mapEvents = new H.mapevents.MapEvents(map);
var behavior = new H.mapevents.Behavior(mapEvents);
var icon  = setCustomMarker();

//const lineString = new H.geo.LineString();
var routingService = platform.getRoutingService();
var searchService = platform.getSearchService();

initMapView();

$(document).ready(function() {
    //event listeners
    addClickEventListener();
    document.getElementById("search-button-js").addEventListener("click", getLocationFromSearch);
    document.getElementById("position-button-js").addEventListener("click", showCurrentLocation);
    document.getElementById("toogle-marker-js").addEventListener("click", toggleSearchMarker);
    document.getElementById("remove-last-js").addEventListener("click", removeLastWaypointMarker);
    document.getElementById("remove-all-js").addEventListener("click", removeAllWaypointsMarkers);
    document.getElementById("continue-js").addEventListener("click", sendData);
    document.getElementById("remove-message-js").addEventListener("click", removeMapResponse);
});

/**
 * showCurrentLocation Shows curent location by marker on the map
 */
function showCurrentLocation()
{
    getActualPosition().then((currentPos) => {
        var currentCoords = {
            lat: currentPos.coords.latitude,
            lng: currentPos.coords.longitude
        };
        showSearchMarker(currentCoords);
    });
}

/**
 * getActualPosition Get actual position of user
 */
function getActualPosition()
{
    return new Promise((res, rej) => {
        navigator.geolocation.getCurrentPosition(res, rej);
    });
}

/**
 * initMapView Set map view to user current position
 */
function initMapView() {
    if(navigator.geolocation) {
        getActualPosition().then((currentPos) => {
            var currentCoords = currentPos.coords;
            setMapView(currentCoords);
        });
    }  else {
        alert('Geolocation is not supported');
    }
}

/**
 * addClickEventListener Add click event listener to map
 */
function addClickEventListener() {
    map.addEventListener('tap', (evt) => {
        const marker_mode = document.getElementById('marker_mode').checked;
        if (marker_mode) {
            var coord = map.screenToGeo(
                evt.currentPointer.viewportX,
                evt.currentPointer.viewportY
            );

            const marker = new H.map.Marker({ lat: coord.lat, lng: coord.lng },{ icon: icon });
            markers.push(marker);
            waypoints.push({
                id: marker.getId(),
                coord: {
                    lat: coord.lat,
                    lng: coord.lng
                }
            });

            map.addObject(marker);
            calculateDistance();
        }
    });
}

/**
 * calculateDistance Calculate distance of drawed route
 * @param  {Boolean} isRemovedMarker Do you want recalculate distance after remove marker? [optional]
 */
function calculateDistance(isRemovedMarker = false)
{
    if (isRemovedMarker && waypoints.length == 1) {
        resetDistance();
    }

    if(waypoints.length > 1) {
        var routingParameters = {
            'mode': 'shortest;pedestrian',
            routeattributes : 'summary,shape',
            'representation': 'display'
        };

        //Add all waypoints to route
        var index = 0;
        waypoints.forEach( (waypoint) => {
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

                if (!isRemovedMarker) {
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
                showDistance(response.route[0]);
            }
        }, 
            error => { 
                showMapResponse('Cannot calculate route.');
        });
    }
}

/**
 * showDistance Show total distance on map panel
 * @param  {Array} routeData Array with route data 
 */
function showDistance(routeData)
{
    var routeDistanceTotal = routeData.summary.distance;
    distanceTotal = (routeDistanceTotal/1000);
    console.log('route data:');
    console.log(routeData);
    if (routeDistanceTotal < 1000) {
        document.getElementById('distance-js').innerHTML = routeDistanceTotal+" m";
    } else {
        document.getElementById('distance-js').innerHTML = distanceTotal+" km";
    }
}

/**
 * resetDistance Reset distance on the map panel
 */
function resetDistance()
{
    document.getElementById('distance-js').innerHTML = "0 m";
}

/**
 * showSearchMarker Show marker of search position on the map
 * @param  {Array} coords
 */
function showSearchMarker(coords)
{
    if (searchMarker) {
        map.removeObject(searchMarker);
    }
    setMapView(coords);
    searchMarker = new H.map.Marker(coords);
    map.addObject(searchMarker);
}

/**
 * getLocationFromSearch Get location from search input and search position of it
 */
function getLocationFromSearch()
{
    var $searchButton = $("#search-button-js");
    disableButton($searchButton);

    var locationSearch = document.getElementById("geolocation-js").value;
    if (locationSearch) {
        searchService.geocode({
            q: locationSearch
        }, (result) => {
            result.items.forEach((item) => {
                showSearchMarker(item.position);
                enableButton($searchButton);
            });
        }, (error) => {
            showMapResponse('Cannot find this place.');
            enableButton($searchButton);
        });
    } else {
        showMapResponse('You cannot search place without name.');
        enableButton($searchButton);
    }   
}

/**
 * disableButton Disable button
 * @param $button JQuery handler to the button
 */
function disableButton($button)
{   
    $button.prop('disabled', true);
    $button.append('<span class="fas fa-spinner fa-spin"></span>');
}

/**
 * enableButton Enable button
 * @param $button JQuery handler to the button
 */
function enableButton($button)
{
    $button.prop('disabled', false);
    $button.children('.fa-spinner').remove();
}

/**
 * showMapResponse Shows any informations to information panel
 * @param  {string} message String with message to show 
 */
function showMapResponse(message)
{   
    $("#info-message-js").html(message);
    $('#info-panel').fadeIn('slow');    
}

/**
 * removeMapResponse Remove info panel message
 */
function removeMapResponse()
{
    var $infoPanel = $("#info-panel");
    var $infoMessage = $("#info-message-js");
    $infoPanel.fadeOut(500, () => {
        $infoMessage.html('');
    });
}

/**
 * setMapView Set view of map
 * @param {Array} currentCoords Array  with position coords to show on the map
 */
function setMapView(currentCoords) {
    if (currentCoords.latitude) {
        map.setCenter({lat: currentCoords.latitude, lng: currentCoords.longitude});
    } else {
        map.setCenter({lat: currentCoords.lat, lng: currentCoords.lng});
    }   
}

/**
 * toggleSearchMarker Show/hide search marker
 */
function toggleSearchMarker()
{
    if (searchMarker) {
        if (searchMarker.getVisibility()) {
            searchMarker.setVisibility(false);
        } else {
            searchMarker.setVisibility(true);
        }
    }
}

/**
 * removeLastWaypointMarker Remove last route marker
 */
function removeLastWaypointMarker()
{
    if(waypoints.length > 0) {
        var lastWaypoint = waypoints.pop();
        var lastMarker = markers.pop();
        map.removeObject(lastMarker);
        var lastPolyline = routePolylines.pop();
        if (lastPolyline) {
            map.removeObject(lastPolyline);
        }
        calculateDistance(true);
    } else {
        showMapResponse('No more markers to delete.');
    }
}

/**
 * removeAllWaypointsMarkers Reset route
 */
function removeAllWaypointsMarkers()
{
    if(waypoints.length > 0) {
        waypoints.length = 0;
        markers.length = 0;
        routePolylines.length = 0;
        map.removeObjects(map.getObjects());
        resetDistance();
    } else {
        showMapResponse('No more markers to delete.');
    }
}

/**
 * sendData Send workout data to server to process it
 * @param event 
 */
function sendData(event)
{
    disableButton($(event.target));
    event.preventDefault();
    removeFormErrors();
    if (waypoints.length > 1) {
        
        const $form = $('.js-new-workout-form');
        var formData = {};
        //Group formData because I don't want allow_extra_fields in form
        formData['formData'] = {}
        formData['formData']['durationSecondsTotal'] = {};

        for(let fieldData of $form.serializeArray()) {        
            if(fieldData.name == 'durationSecondsTotal[hour]'){
                formData['formData']['durationSecondsTotal']['hour'] = fieldData.value;
            } else if(fieldData.name == 'durationSecondsTotal[minute]') {
                formData['formData']['durationSecondsTotal']['minute'] = fieldData.value;
            } else if(fieldData.name == 'durationSecondsTotal[second]') {
                formData['formData']['durationSecondsTotal']['second'] = fieldData.value;
            } else {
                formData['formData'][fieldData.name] = fieldData.value;
            }
            if (!fieldData.value) {
                enableButton($(event.target));
                showMapResponse('Form data is missing');
                return;
            }
        }
        
        var url = document.getElementById('continue-js').getAttribute('data-url');
        map.capture((canvas) => {
            if (canvas) {
                var mapImage = canvas.toDataURL();
                formData['waypoints'] = waypoints;
                formData['distanceTotal'] = distanceTotal;
                formData['image'] = mapImage;

                saveWorkout(formData,url).then((result) => {
                    window.location.href = result.url;
                }).catch((errorData) => {
                    if (errorData.errorMessage) {
                        showMapResponse(errorData.errorMessage);
                    } else {
                        mapErrorsToForm(errorData);
                    }                    
                });
            } else {
                showMapResponse('Capturing is not supported.');
            }
        }, []);

    } else {
        showMapResponse('Draw your route first.');   
    }

    enableButton($(event.target));
}

/**
 * saveWorkout Send workout data to server and get response
 * @param  {Array} data Array with workout data
 * @param  {String} url Url to server method
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
 * getStatusError Get status error and return proper message
 * @param jqXHR 
 * @return {Array} Array with message
 */
function getStatusError(jqXHR) {
    if(jqXHR.status === 0) {
        return {
            "errorMessage":"Cannot connect. Verify Network."
        }
    } else if(jqXHR.status == 404) {
        return {
            "errorMessage":"Requested not found."
        }
    } else if(jqXHR.status == 500) {
        return {
            "errorMessage":"Internal Server Error"
        }
    } else if(jqXHR.status > 400) {
        return {
            "errorMessage":"Error. Contact with admin."
        }
    }
    return null;
}

/**
 * mapErrorsToForm Map errors to proper form fields
 * @param  {Array} errorData Array with form errors
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
 * removeFormErrors Remove all errors from form fields
 */
function removeFormErrors() {
    var $form = $('.js-new-workout-form');
    $form.find('.js-field-error').remove();
    $form.find('.form-group').removeClass('has-error');
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
 * setCustomMarker Set custom marker
 */
function setCustomMarker() {
    var svgMarkup = '<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg">' +
    '<circle fill="royalblue" cx="7" cy="7" r="7" /></svg>';

    return new H.map.Icon(svgMarkup);
}
