var mapElement = document.getElementById("mapDiv");
var map;

var serverData;

var markers = {};

var socket = io.connect(); //används för att kommunicera med server

var loadMap = function()
{
    var mapOptions = {center: {lat:63, lng: 16}, zoom: 5};
    map = new google.maps.Map(mapElement, mapOptions);
}

socket.on("trafficMessages", function(json)
{
    messages = json.messages;
    
    updateAboutSection(json.dataRecievedTime, json.copyright);
    updateMap();
    updateMessageList();
    
    
})

var updateAboutSection = function(date, copyright)
{
    
    var lastUpdateDiv = document.getElementsByClassName("lastUpdate")[0];
    var copyrightDiv = document.getElementsByClassName("copyright")[0];
    
    var lastUpdateText = document.createTextNode("Senaste uppdatering: " + getDateString(date));
    var copyrightText = document.createTextNode(copyright);
    
    lastUpdateDiv.appendChild(lastUpdateText);
    copyrightDiv.appendChild(copyrightText);
}

var updateMessageList = function()
{
    var messageListDiv = document.getElementById("messageList");
    var messageList = document.createElement("ul");
    messageListDiv.appendChild(messageList);
    
    for(var i = 0; i < messages.length; i++)
    {
        messageList.appendChild(createListItem(messages[i]));
    }
}

var updateMap = function()
{
    var position;
    var marker;
    
    for(var i = 0; i < messages.length; i++)
    {
        position = new google.maps.LatLng(messages[i].latitude, messages[i].longitude);
        
        marker = new google.maps.Marker({position: position, map: map });

        markers[messages[i].id] = marker;
        
    }
    
}

var createListItem = function(message)
{
    
        //delar av ett listelement
        var listItem = document.createElement("li");
        
        var itemDiv = document.createElement("div");
        var itemHeader = document.createElement("div");
        var itemBody = document.createElement("div");
        
        //gör om detta till ett vettigt datum först
        var date = getDateString(Number(message.createddate.split("+")[0].slice(6)));
        
        //lägg till allt innehåll
        itemHeader.appendChild(document.createTextNode(message.title));
        itemHeader.appendChild(document.createTextNode(date));
        
        itemBody.appendChild(document.createTextNode("Var: " + message.exactlocation));
        itemBody.appendChild(document.createTextNode("Typ: " + message.subcategory));
        itemBody.appendChild(document.createTextNode("Beskrivning: " + message.description));
        
        //sätt ihop allt och returnera
        itemDiv.appendChild(itemHeader);
        itemDiv.appendChild(itemBody);
        listItem.appendChild(itemDiv);
        
        //marker ska studsa på kartan när man klickar på  ett listobjekt
        listItem.addEventListener("click", function()
        {
            markers[message.id].setAnimation(google.maps.Animation.BOUNCE);
            
            setTimeout(function() 
            {
                markers[message.id].setAnimation(null);
            }, 1400);
            
        }, false);
        
        
        return listItem;
}

var getDateString = function(timestamp)
{
    
    var addZero = function(time)
    {
        if(time < 10)
        {
            return "0" + time; 
        }
        return time;
    }
    
    var dateObject = new Date(timestamp);
    
    var months = ["Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"];
    
    var year = dateObject.getFullYear();
    var month = months[dateObject.getMonth()];
    var date = dateObject.getDate();
    var hour = dateObject.getHours();
    var minute = dateObject.getMinutes();
    var second = dateObject.getSeconds();
    
    hour = addZero(hour);
    minute = addZero(minute);
    second = addZero(second);
    
    return hour + ":" + minute + ":" + second + " " + date + " " + month + " " + year;
    
}

//ladda in kartan när sidan laddat klart.
google.maps.event.addDomListener(window, 'load', loadMap);