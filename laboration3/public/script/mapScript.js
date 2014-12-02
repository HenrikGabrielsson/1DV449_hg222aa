var mapElement = document.getElementById("mapDiv");
var map;

var serverData;

var markers = {};
var infowindows = [];

var socket = io.connect(); //används för att kommunicera med server

var loadMap = function()
{
    var mapOptions = {center: {lat:63, lng: 16}, zoom: 5};
    map = new google.maps.Map(mapElement, mapOptions);
}

var priorities = [1,2,3,4,5];


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
    alert(markers.length);
    
    var position;
    var marker;
    
    for(var i = 0; i < messages.length; i++)
    {
        
        position = new google.maps.LatLng(messages[i].latitude, messages[i].longitude);
        marker = new google.maps.Marker({position: position, map: map });
        addInfoWindow(marker, messages[i]);

        markers[messages[i].id] = marker;
        
    }
    
}

var addInfoWindow = function(marker, message)
{
    var infowindow;
    
    //infowindow ska komma fram vid klick
    infowindow = new google.maps.InfoWindow
    ({
        content: createInfoWindowContent(message)
            
    });
    google.maps.event.addListener(marker, "click", function()
    {
        //stäng alla eventuella öppna infowindows
        for(var i = 0; i < infowindows.length; i++)
        {
            infowindows[i].close();
        }
        
        infowindow.open(map,marker);
    })
    
    //spara alla infowindows i array;
    infowindows.push(infowindow);
    

}

var createInfoWindowContent = function(message)
{
    
    var infowindowDiv = document.createElement("div");
    var title = document.createElement("h2");
    var description = document.createElement("p");
    var date = document.createElement("p");
    var category = document.createElement("p");
    
    infowindowDiv.setAttribute("class", "infowindow");
    title.setAttribute("class", "infowindow-title");
    
    var formattedDate = getDateString(Number(message.createddate.split("+")[0].slice(6)));
    
    infowindowDiv.appendChild(title.appendChild(document.createTextNode(message.title)));
    infowindowDiv.appendChild(description.appendChild(document.createTextNode(message.description)));
    infowindowDiv.appendChild(date.appendChild(document.createTextNode(formattedDate)));
    infowindowDiv.appendChild(category.appendChild(document.createTextNode(message.subcategory)));
    
    return infowindowDiv;
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