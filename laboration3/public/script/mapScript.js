var mapElement = document.getElementById("mapDiv");

var filterForm = document.getElementById("filterOptionsForm");
var listForm = document.getElementById("sortMethodForm");

var messages;
var map;
var serverData;

var markers = [];
var infowindows = [];

var defaultMapOptions = {center: {lat:63, lng: 16}, zoom: 5};

//Den kategori [0-3] (4 === alla) som ska visas på kartan. Alla ska visas från början
var categoryFilter = 4;
var areaFilter = 0;
var listSort = 0;


var socket = io.connect(); //används för att kommunicera med server

var loadMap = function()
{
    map = new google.maps.Map(mapElement, defaultMapOptions);
}

socket.on("trafficMessages", function(json)
{
    messages = json.messages;
    areas = json.areas;

    fillAreaDropdown(areas);
    updateAboutSection(json.dataRecievedTime, json.copyright);
    updatePage();
});

listForm.addEventListener("change", function(e)
{
        
    var dropdown = document.getElementById("listSortDropdown"); 
    
    listSort = Number(dropdown.options[dropdown.selectedIndex].value);
    
    updatePage();
})

filterForm.addEventListener("change", function(e)
{
    var categoryDropdown = document.getElementById("categoryDropdown"); 
    categoryFilter = Number(categoryDropdown.options[categoryDropdown.selectedIndex].value);

    var areaDropdown = document.getElementById("areaDropdown"); 
    var newAreaFilter = Number(areaDropdown.options[areaDropdown.selectedIndex].value);

    //bara om man har ändrat på fillAreaDropdown
    if(newAreaFilter !== areaFilter)
    {
        areaFilter = newAreaFilter;
        
        updateMapOptions();
    }
    updatePage();
   
},false);

var updateMapOptions = function()
{
    //0 : default
    if(areaFilter === 0)
    {
        map.setOptions(defaultMapOptions);
        return;
    }
    
    //hämta ut rätt trafikområde
    var area;
    for(var i = 0; i < areas.length; i++)
    {
        if(areas[i].trafficdepartmentunitid === areaFilter)
        {
            area = areas[i];
            break;
        }
    }

    var count = 0;
    var averageLat = 0;
    var averageLong = 0;
    for(i = 0; i < messages.length; i++)
    {
        if(messages[i].area === area.trafficdepartmentunitid)
        {
            count++;
            averageLat += messages[i].latitude;
            averageLong += messages[i].longitude;
        }
    }
    
    //om det inte finna några meddelanden i valt område så körs default-inställningen (hela sverige)
    if(count === 0)
    {
        map.setOptions(defaultMapOptions);
        return;
    }
    
    map.setOptions({center: {lat:averageLat/count, lng: averageLong/count}, zoom: area.zoom-1});
    
}

var fillAreaDropdown = function(areas)
{
    var dropdown = document.getElementById("areaDropdown");
    var option;
    
    option = document.createElement("option");
    option.setAttribute("value", 0);
    option.appendChild(document.createTextNode("Visa alla"));
    dropdown.appendChild(option);
    
    for(var i = 0; i < areas.length;i++)
    {
        option = document.createElement("option");
        option.setAttribute("value", areas[i].trafficdepartmentunitid);
        option.appendChild(document.createTextNode(areas[i].name));
        dropdown.appendChild(option);            
    }
   
}

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
        
        //ta bort alla listobjekt
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
    
    var subList_li;
    var subList_ul;  
    
    //om listan ska sorteras på area så skapas flera sub-lists
    if(listSort === 1)
    {
        for(var i = 0; i < areas.length; i++)
        {
            subList_li = document.createElement("li");    
            subList_li.setAttribute("class","areaListItem hidden");
            
            subList_ul = document.createElement("ul");
            subList_ul.setAttribute("class", "subList " + areas[i].trafficdepartmentunitid);
            
            subList_li.appendChild(document.createTextNode(areas[i].name));
            subList_li.appendChild(subList_ul);
            messageList.appendChild(subList_li);

        }
    }

    var position;
    var marker;
    
    var markerIcon;

    var subList;

    sortList();
    
    for(var j = 0; j < messages.length; j++)
    {
        if((categoryFilter === 4 || categoryFilter === messages[j].category) && (areaFilter === 0 || areaFilter === messages[j].area ))
        {
            markerIcon = {url: "../img/marker.png", origin: {x: (messages[j].priority -1 ) * 22 , y: 0}, size: {width:22, height:41}};
            
            position = new google.maps.LatLng(messages[j].latitude, messages[j].longitude);
            marker = new google.maps.Marker({position: position, map: map, icon: markerIcon });
            addInfoWindow(marker, messages[j]);
    
            if(listSort === 0)
            {
                messageList.appendChild(createListItem(messages[j], marker));
            }
            else if(listSort === 1)
            {
                subList = document.getElementsByClassName("subList " + messages[j].area)[0]; 
                subList.appendChild(createListItem(messages[j], marker));
                
                if(subList.parentNode.getAttribute("class") == "areaListItem hidden")
                {
                    subList.parentNode.setAttribute("class", "areaListItem");
                }
            }

            markers.push(marker);
        }
    }
}

var sortList = function()
{
    
    //sortera listan efter tid (ska alltid göras först)
    messages.sort(function(a,b)
    {
        return Number(b.createddate.split("+")[0].slice(6)) - Number(a.createddate.split("+")[0].slice(6));
    });

    //sortera listan efter område om användaren vill det
    if(listSort === 1)
    {
        var sortedMessages = [];
        
        for(var i = 0; i < areas.length;i++)
        {
            for(var j = 0; j < messages.length;j++)
            {
                if(areas[i].trafficdepartmentunitid === messages[j].area)
                {
                    sortedMessages.push(messages[j]);
                }
            }
        }
        
        messages = sortedMessages;
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
    title.setAttribute("class", "infowindow_title");
    
    var formattedDate = getDateString(Number(message.createddate.split("+")[0].slice(6)));
    
    title.appendChild(document.createTextNode(message.title));
    description.appendChild(document.createTextNode("Vad: " + message.description));
    date.appendChild(document.createTextNode("När: " + formattedDate));
    category.appendChild(document.createTextNode("Typ: " + message.subcategory));
    
    infowindowDiv.appendChild(title);
    infowindowDiv.appendChild(description);
    infowindowDiv.appendChild(date);
    infowindowDiv.appendChild(category);

    return infowindowDiv;
}

var createListItem = function(message, marker)
{
    //delar av ett listelement
    var listItem = document.createElement("li");
    
    var itemDiv = document.createElement("div");
    itemDiv.setAttribute("class", "message");
    
    var itemHeader = document.createElement("div");
    itemHeader.setAttribute("class", "message_header");
    
    var itemBody = document.createElement("div");
    itemBody.setAttribute("class", "message_body");

    var where = document.createElement("p");
    var type = document.createElement("p");
    var description = document.createElement("p");
    
    //gör om detta till ett vettigt datum först
    var date = getDateString(Number(message.createddate.split("+")[0].slice(6)));
    
    //lägg till allt innehåll
    
    var headerTitle = document.createElement("p");
    var headerDate = document.createElement("p");
    headerTitle.appendChild(document.createTextNode(message.title));
    headerDate.appendChild(document.createTextNode(date));    
    
    
    itemHeader.appendChild(headerTitle);
    itemHeader.appendChild(headerDate);
    
    where.appendChild(document.createTextNode("Var: " + message.exactlocation));
    type.appendChild(document.createTextNode("Typ: " + message.subcategory));
    description.appendChild(document.createTextNode("Beskrivning: " + message.description));
    
    itemBody.appendChild(where);
    itemBody.appendChild(type);
    itemBody.appendChild(description);
    
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
    
    return addZero(hour) + ":" + addZero(minute) + ":" + addZero(second) + " " + date + " " + month + " " + year;
    
}


//ladda in kartan när sidan laddat klart.
google.maps.event.addDomListener(window, 'load', loadMap);