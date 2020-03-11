'use strict';
//config must be impelemented here
const platform = configPlatform();
const defaultLayers = configLayers();
var markers = [];

// map instantiate must be global too
var map = new H.Map(
    document.getElementById('mapContainer'),
    defaultLayers.vector.normal.map,
    {
      zoom: 10,
    }
);

var mapEvents = new H.mapevents.MapEvents(map);
var behavior = new H.mapevents.Behavior(mapEvents);
var icon  = setCustomMarker();

const lineString = new H.geo.LineString();
var polyline;
var routingService = platform.getRoutingService();
console.log(platform);
var routingParameters = {
  'mode': 'fastest;car',
  'waypoint0': 'geo!50.1120423728813,8.68340740740811',
  'waypoint1': 'geo!52.5309916298853,13.3846220493377',
  'representation': 'dragNDrop'
};
routingService.calculateRoute(routingParameters, success => {
    console.log(success);
});

addClickEventListener(icon, lineString, polyline);

//$(document).ready(function() {
initMap();    
//});

//to do wyeksportowania
function getActualPosition()
{
        return new Promise((res, rej) => {
            navigator.geolocation.getCurrentPosition(res, rej);
        });
}

function initMap() {
    if(navigator.geolocation) {
        getActualPosition().then((currentPos) => {
            var currentCoords = currentPos.coords;
            loadMap(currentCoords);
            
        });
    }  else {
        alert('navigator is not support');
    }
}
function loadMap(currentCoords) {
    /*map = new H.Map(
        document.getElementById('mapContainer'),
        defaultLayers.vector.normal.map,
        {
          zoom: 10,
          center: { lat: currentCoords.latitude, lng: currentCoords.longitude }
        }
    );*/

    map.setCenter({lat: currentCoords.latitude, lng: currentCoords.longitude});    
}

function addClickEventListener(icon, lineString, polyline) {
    map.addEventListener('tap', (evt) => {
        const marker_mode = document.getElementById('marker_mode').checked;
        calculateDistance();  
        /*if (marker_mode) {
            var coord = map.screenToGeo(
                evt.currentPointer.viewportX,
                evt.currentPointer.viewportY
            );

            const marker = new H.map.Marker({ lat: coord.lat, lng: coord.lng },{ icon: icon });
            markers.push(marker);
            map.addObject(marker);

            lineString.pushPoint(marker.getGeometry());
            if (markers.length > 1) {
                //polyline = new H.map.Polyline(lineString);
                //map.addObject(polyline); 
                
            }
    
        }*/
    });
}

function calculateDistance()
{
    /*var points = [];

    for (var i = 0; i < markers.length; i++) {
        points[i] = [];
        points[i][0] = markers[i].getGeometry().lat;
        points[i][1] = markers[i].getGeometry().lng;
    }*/
/*
    const params = {
        mode: 'shortest;pedestrian',
        //'waypoint0': 'geo!50.055246,19.991113',
        //'waypoint1': 'geo!50.0620007,20.0033978',
       // waypoint0: points[0][0]+','+points[0][1],
       // waypoint1: points[1][0]+','+points[1][1],
        representation: "display",
        routeAttributes: "summary"
    };
*/

 /*var routingParameters = {
  // The routing mode:
  'mode': 'fastest;car',
  // The start point of the route:
  'waypoint0': 'geo!50.1120423728813,8.68340740740811',
  // The end point of the route:
  'waypoint1': 'geo!52.5309916298853,13.3846220493377',
  // To retrieve the shape of the route we choose the route
  // representation mode 'display'
  'representation': 'display'
};*/
console.log(platform);
var routingService2 = platform.getRoutingService();
routingService2.calculateRoute(routingParameters, success => {
    //console.log(success);
}, error => { console.log(error);});
   





}




























//config
function configPlatform() {
    return new H.service.Platform({
        'apikey': window.apikey
    });
}

function configLayers() {
    return platform.createDefaultLayers();
}
//end of config
//custom marker
function setCustomMarker(){
    var svgMarkup = '<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg">' +
    '<circle fill="royalblue" cx="7" cy="7" r="7" /></svg>';


  return new H.map.Icon(svgMarkup);
}
//End of custom marker



















