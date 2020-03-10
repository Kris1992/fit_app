//config must be impelemented here
const platform = configPlatform();
const defaultLayers = configLayers();
var markers = [];

$(document).ready(function() {
    initMap();
});

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
    // Instantiate (and display) a map object:
    var map = new H.Map(
        document.getElementById('mapContainer'),
        defaultLayers.vector.normal.map,
        {
          zoom: 10,
          center: { lat: currentCoords.latitude, lng: currentCoords.longitude }
        }
    );
    // Enable the event system on the map instance:
    var mapEvents = new H.mapevents.MapEvents(map);
    // Instantiate the default behavior, providing the mapEvents object:
    var behavior = new H.mapevents.Behavior(mapEvents);
    var icon  = setCustomMarker(map);

    const lineString = new H.geo.LineString();
    var polyline;

    addClickEventListener(map, icon, lineString, polyline);
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
function setCustomMarker(map){
    var svgMarkup = '<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg">' +
    '<circle fill="royalblue" cx="7" cy="7" r="7" /></svg>';


  return new H.map.Icon(svgMarkup);
}
//End of custom marker

function addClickEventListener(map, icon, lineString, polyline) {
    map.addEventListener('tap', (evt) => {
        const marker_mode = document.getElementById('marker_mode').checked;

        if (marker_mode) {
            var coord = map.screenToGeo(
                evt.currentPointer.viewportX,
                evt.currentPointer.viewportY
            );

            const routingService = platform.getRoutingService();
            const marker = new H.map.Marker({ lat: coord.lat, lng: coord.lng },{ icon: icon });
            markers.push(marker);
            map.addObject(marker);

            lineString.pushPoint(marker.getGeometry());
            if (markers.length > 1) {
                polyline = new H.map.Polyline(lineString);
                map.addObject(polyline);
            }
    
        }
    });
}



