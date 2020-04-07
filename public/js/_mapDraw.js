'use strict';

//config must be impelemented here
const platform = configPlatform();
const defaultLayers = configLayers();
var searchMarker;
var markers = [];
var waypoints = [];
var routePolylines = [];

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
    addClickEventListener(icon);
    document.getElementById("search-button-js").addEventListener("click", getLocationFromSearch);
    document.getElementById("toogle-marker-js").addEventListener("click", toggleSearchMarker);
    document.getElementById("remove-last-js").addEventListener("click", removeLastWaypointMarker);
    document.getElementById("remove-all-js").addEventListener("click", removeAllWaypointsMarkers);
    document.getElementById("continue-js").addEventListener("click", sendData);
    document.getElementById("remove-message-js").addEventListener("click", removeMapResponse);
});

//to do wyeksportowania
function getActualPosition()
{
    return new Promise((res, rej) => {
        navigator.geolocation.getCurrentPosition(res, rej);
    });
}

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

function addClickEventListener(icon) {
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

function showDistance(routeData)
{
    var distanceTotal = routeData.summary.distance;
    console.log('route data:');
    console.log(routeData);
    if (distanceTotal < 1000) {
        document.getElementById('distance-js').innerHTML = distanceTotal+" m";
    } else {
        document.getElementById('distance-js').innerHTML = (distanceTotal/1000)+" km";
    }
}

function resetDistance()
{
    document.getElementById('distance-js').innerHTML = "0 m";
}

function getLocationFromSearch()
{
    disableSearchButton();
    var locationSearch = document.getElementById("geolocation-js").value;

    if (locationSearch) {
        searchService.geocode({
            q: locationSearch
        }, (result) => {
            result.items.forEach((item) => {
                if (searchMarker) {
                    map.removeObject(searchMarker);
                }
                setMapView(item.position);
                searchMarker = new H.map.Marker(item.position);
                map.addObject(searchMarker);
                enableSearchButton();
            });
        }, (error) => {
            showMapResponse('Cannot find this place.');
            enableSearchButton();
        });
    } else {
        showMapResponse('You cannot search empty field');
        enableSearchButton();
    }   
}

function disableSearchButton()
{   
    var $searchButton = document.getElementById("search-button-js");
    $searchButton.disabled = true;
    $searchButton.innerHTML = '<span class="fas fa-spinner fa-spin"></span>';
}

function enableSearchButton()
{
    var $searchButton = document.getElementById("search-button-js");
    $searchButton.disabled = false;
    $searchButton.innerHTML = '<span class="fas fa-search-location"></span>';   
}

function showMapResponse(message)
{   
    $("#info-message-js").html(message);
    $('#info-panel').fadeIn('slow');
    
}

function removeMapResponse()
{
    var $infoPanel = $("#info-panel");
    var $infoMessage = $("#info-message-js");
    $infoPanel.fadeOut(500, () => {
        $infoMessage.html('');
    });
}

function setMapView(currentCoords) {
    if (currentCoords.latitude) {
        map.setCenter({lat: currentCoords.latitude, lng: currentCoords.longitude});
    } else {
        map.setCenter({lat: currentCoords.lat, lng: currentCoords.lng});
    }   
}

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
        showMapResponse('No more markers to delete');
    }
}

function removeAllWaypointsMarkers()
{
    if(waypoints.length > 0) {
        waypoints.length = 0;
        markers.length = 0;
        routePolylines.length = 0;
        map.removeObjects(map.getObjects());
        resetDistance();
    } else {
        showMapResponse('No more markers to delete');
    }
}

function sendData(event)
{

    event.preventDefault();
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
        }
        var url = document.getElementById('continue-js').getAttribute('data-url');
        map.capture((canvas) => {
            if (canvas) {
                var mapImage = canvas.toDataURL();
                formData['waypoints'] = waypoints;
                formData['image'] = mapImage;

                saveWorkout(formData,url);
            } else {
                showMapResponse('Capturing is not supported');
            }
        }, []);
    } else {
        showMapResponse('Draw your route first');   
    }
}

//config
function configPlatform() {
    return new H.service.Platform({
        'apikey': window.apikey,
    });
}

function configLayers() {
    return platform.createDefaultLayers();
}
//end of config

//custom marker
function setCustomMarker() {
    var svgMarkup = '<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg">' +
    '<circle fill="royalblue" cx="7" cy="7" r="7" /></svg>';

    return new H.map.Icon(svgMarkup);
}
//End of custom marker








function saveWorkout(data, url) {
    return new Promise( (resolve, reject) => {
        $.ajax({
            url,
            method: 'POST',
            data: JSON.stringify(data)
        });//.then(function(data, textStatus, jqXHR) {
                   // $.ajax({
                    //    url: jqXHR.getResponseHeader('Location')
                   // }).then(function(data) {
                   //     resolve(data);
                   // });
               // }).catch(function(jqXHR) {
               //     const errorData = JSON.parse(jqXHR.responseText);
               //     reject(errorData);
               // });
    });
}
