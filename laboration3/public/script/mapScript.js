var mapElement = document.getElementById("mapDiv");

var filterForm = document.getElementById("filterOptionsForm");

var map;
var serverData;

var markers = [];
var infowindows = [];

//Den kategori [0-3] (4 === alla) som ska visas på kartan. Alla ska visas från början
var categoryFilter = 4;

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
    updatePage();
});

filterForm.addEventListener("submit", function(e)
{
    //ladda inte om sidan
    e.preventDefault();
    
    var dropdown = document.getElementById("categoryDropdown"); 
    
    categoryFilter = Number(dropdown.options[dropdown.selectedIndex].value);

    updatePage();
   
},false);

var updateAboutSection = function(date, copyright)
{
    
    var lastUpdateDiv = document.getElementsByClassName("lastUpdate")[0];
    var copyrightDiv = document.getElementsByClassName("copyright")[0];
    
    var lastUpdateText = document.createTextNode("Senaste uppdatering: " + getDateString(date));
    var copyrightText = document.createTextNode(copyright);
    
    lastUpdateDiv.appendChild(lastUpdateText);
    copyrightDiv.appendChild(copyrightText);
}

//lägger in meddelanden i listan och lägger till markers på kartan. 
var updatePage = function()
{
    var messageListDiv = document.getElementById("messageList");
    var messageList;
    
    //om det redan finns markers utsatta och objekt i listan så töms dessa.
    if(markers.length === 0)
    {
        messageList = document.createElement("ul");
        messageList.setAttribute("id","messageList_ul");
        messageListDiv.appendChild(messageList);
    }
    else
    {
        messageList = document.getElementById("messageList_ul");
        
        while(messageList.firstChild)
        {
            messageList.removeChild(messageList.firstChild);
        }
        
        for(var i = 0; i < markers.length; i++)
        {
            markers[i].setMap(null);
        }
        markers.length = 0;        
    }

    var position;
    var marker;
    
    var markerIcon;
    
    for(var j = 0; j < messages.length; j++)
    {
        if(categoryFilter === 4 || categoryFilter === messages[j].category)
        {
            markerIcon = {url: "../img/marker.png", origin: {x: (messages[j].priority -1 ) * 22 , y: 0}, size: {width:22, height:41}};
            
            position = new google.maps.LatLng(messages[j].latitude, messages[j].longitude);
            marker = new google.maps.Marker({position: position, map: map, icon: markerIcon });
            addInfoWindow(marker, messages[j]);
    
            messageList.appendChild(createListItem(messages[j], marker));
    
            markers.push(marker);
        }
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

var createListItem = function(message, marker)
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
        marker.setAnimation(google.maps.Animation.BOUNCE);
        
        setTimeout(function() 
        {
            marker.setAnimation(null);
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