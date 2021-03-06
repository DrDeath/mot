'use strict';

document.getElementById('plz_search').onsubmit = function (evt) {
	getSurroundingUsers();
	evt.preventDefault();
}

function add_map_point(myMap, lat, lng, colour, myName) {
	var circleMarkerOptions = {
		radius:			4,
		color:			'black',
		weight:			1,
		opacity:		1.0,
		fillColor:		colour,
		fillOpacity:	1.0,
	}
	var circleMarker = new L.circleMarker([lat, lng], circleMarkerOptions);
	circleMarker.bindTooltip(myName);
	circleMarker.addTo(myMap);
}

function jumpTo(map, lat, lng, zoom) {
	var latlng = new L.latLng(lat, lng);
	map.setView(latlng, zoom);
//	    return false;
}

function distanceInKm(lat1, lon1, lat2, lon2) {
	var lat = ((lat1 + lat2) / 2) * 0.01745;
	var dx = 111.3 * Math.cos(lat) * (lon1 - lon2);
	var dy = 111.3 * (lat1 - lat2)
	var distance = Math.sqrt((dx * dx) + (dy * dy));

	return Math.abs(distance);
}

function getSurroundingUsers() {
	var currentUser = jsCurrentUser.split("|");
	var userID = currentUser[0];
	var userPLZ = currentUser[2];
	var userLat = parseFloat(currentUser[3]);
	var userLng = parseFloat(currentUser[4]);
	var jDistance;
	var umRadius = document.querySelector("#plz_choice").value;
	var outPut = "";
	var j = 0;
	var userData;
	var searchResult = new Array();
	while (j < mapDataLength) {
		userData = jsMapData[j];
		var userResult = new Array();
		jDistance = distanceInKm(userLat, userLng, parseFloat(userData['user_lat']), parseFloat(userData['user_lng']));
		if ((userID != userData['user_id']) && (jDistance <= umRadius)) {
			userResult.push(userData['username']);
			userResult.push(userData['user_colour']);
			userResult.push(parseInt(jDistance, 10));
			userResult.push(userData['user_id']);
			searchResult.push(userResult);
		}
		j++;
	}

	searchResult.sort(function(a, b){
		return a[2] - b[2];
	});

	var len = searchResult.length;
	for (i = 0; i < len; i++) {
		if (outPut != "") { outPut = outPut + ', '; }
		outPut = outPut + '<a href="' + jsServerConfig + '/memberlist.php?mode=viewprofile&u=' + searchResult[i][3] + '" target="_blank">'
						+ '<span style="color:#' + searchResult[i][1] + ';">'
						+ searchResult[i][0] + '</span>' + '</a>'
						+ ': '
						+ searchResult[i][2] + 'km';
	}

	if (outPut != "") {
		document.getElementById('seperation_hr').innerHTML = '<hr>' + jsMapResult + '<br />';
		document.getElementById('solution').innerHTML = outPut;
	} else {
		document.getElementById('seperation_hr').innerHTML = '<hr>' + jsMapNoResult + umRadius + 'km';
		document.getElementById('solution').innerHTML = outPut;
	}
	jumpTo(map, userLat, userLng, zoomFactor[umRadius]);
}

var zoomFactor = new Array();
zoomFactor[1] = 13;
zoomFactor[2] = 13;
zoomFactor[5] = 12;
zoomFactor[10] = 11;
zoomFactor[25] = 10;
zoomFactor[50] = 9;
zoomFactor[100] = 8;

var mapConfig, Lat, Lng, Zoom;	// variables used to initialize the map

mapConfig = jsConfig.split("|");
Lat = parseFloat(mapConfig[0]);
Lng = parseFloat(mapConfig[1]);
Zoom = mapConfig[2];

var mapOptions = {
	center: [Lat, Lng],
	zoom: Zoom,
	attributionControl: false,
	scrollWheelZoom: false,
}

var map = new L.map('map_container', mapOptions);

map.on('click', function() {
	if (map.scrollWheelZoom.enabled()) {
		map.scrollWheelZoom.disable();
	}
	else {
		map.scrollWheelZoom.enable();
	}
});

//		var layer = new L.TileLayer('http://\{s\}.tile.openstreetmap.org/\{z\}/\{x\}/\{y\}.png');	// international map colors
var layer = new L.TileLayer('http://\{s\}.tile.openstreetmap.de/tiles/osmde/\{z\}/\{x\}/\{y\}.png');	// German map colors

map.addLayer(layer);

var attribution = new L.control.attribution().addAttribution('Map Data &copy; <a href="https://www.openstreetmap.org/copyright" target=_blank">OpenStreetMap</a>').addTo(map);

var scale = new L.control.scale({imperial: false}).addTo(map);

var i = 0;
var userLocation;
var mapDataLength = jsMapData.length;								// get the number of user markers in the list
while (i < mapDataLength) {											// show all user markers on the map
	userLocation = jsMapData[i];
	add_map_point(map, parseFloat(userLocation['user_lat']), parseFloat(userLocation['user_lng']), '#'+userLocation['user_colour'], userLocation['username']);
	i++;
}
