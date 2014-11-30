var mapElement = document.getElementById("mapDiv");
var map;

var socket = io.connect(); //används för att kommunicera med server

var loadMap = function()
{
    var mapOptions = {center: {lat:63, lng: 16}, zoom: 5};
    map = new google.maps.Map(mapElement, mapOptions);
}

//ladda in kartan när sidan laddat klart.
google.maps.event.addDomListener(window, 'load', loadMap);