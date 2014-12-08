//konstruktor
var Traffic = function(http)
{
    this.http = http; //för att skicka http-requests
    this.fs = require("fs"); //för att arbeta med filer (cachning)
    
    //flag för att visa om meddelanden har förändrats.
    this.hasChanged = false;
    
    //filnamn
    this.messageFile = "traffic.json";
    this.areaFile = "areas.json";

    //bra url:er till SR's api
    this.allAreasUrl = "http://api.sr.se/api/v2/traffic/areas?format=json&pagination=false";
    this.messageUrl = "http://api.sr.se/api/v2/traffic/messages?format=json&size=100&sort=createddate";
}

//Hämtar meddelanden från SR och sparar dem lokalt.
Traffic.prototype.saveTrafficNewsFromSR = function(callback)
{
    //workaround
    var traffic = this;

    //hämta 100 senaste meddelanden
    this.getFromURL(traffic.messageUrl, function(json)
    {
        json.dataRecievedTime = Date.now() + 3600000; //senaste hämtning (timestamp)
        
        //lägg till en area-code på varje meddelande.
        traffic.addAreaCodeToEachMessage(json.messages, function(messagesWithAreas)
        {
            json.messages = messagesWithAreas;
            traffic.fs.writeFileSync(traffic.messageFile, JSON.stringify(json));
            console.log("Last Message Update: " + new Date(json.dataRecievedTime));
            
            callback();
        },0);
    });
    
}

//hämtar områden från SR och sparar dem lokalt.
Traffic.prototype.saveAreasFromSR = function()
{
    //workaround
    var traffic = this;
    
    this.getFromURL(this.allAreasUrl, function(json)
    {
        traffic.fs.writeFileSync(traffic.areaFile, JSON.stringify(json));
    })
}

//Funktion som hämtar JSON från en given URL.
Traffic.prototype.getFromURL = function(url, callback)
{
    var json;
    
    var request = this.http.request(url, function(res)
    {
        res.setEncoding('utf8');
        
        var chunks = "";
        
        //varje gång vi får en "chunk" med data så lägger vi till det i var chunk.
        res.on('data', function(data)
        {
            chunks += data;
        })
        
        //när all data kommit från sr så kallar vi på callbackfunktionen och skickar med strängen
        res.on('end', function()
        {
            
            //kör callback och skickar med json-data som lästes ut.
            callback(JSON.parse(chunks));
            
        })
    });

    request.end();      
}


//hämta cachade meddelanden.
Traffic.prototype.getMessages = function()
{
    return this.getFromJSONFile(this.messageFile);
}

//hämta cachade områden
Traffic.prototype.getAreas = function()
{
    return this.getFromJSONFile(this.areaFile);
}

//tar emot namnet på en JSON-fil och hämtar dess innehåll.
Traffic.prototype.getFromJSONFile = function(filename)
{
    var fileContent = this.fs.readFileSync(filename, {encoding:"utf8", flag:"r"});
    
    var cachedJSON;
    
    //fånga fel om det inte går att tolka innehållet som json.
    try
    {
        cachedJSON = JSON.parse(fileContent);
    }
    catch(error)
    {
        return {error: "Gick inte att läsa från filen. Försök igen om en stund"};
    }
    return cachedJSON;
}

//rekursiv funktion som går igenom alla messages, lägger till area-koden i objektet, och sedan kör callback-funktionen.
Traffic.prototype.addAreaCodeToEachMessage = function(messages, callback, messNumber)
{
    //liten  workaround
    var traffic = this;
    
    var cachedMessages = this.getMessages(); 

    //om detta nummer inte är ett index i messages så körs callback-funktionen och rekursionen är slut.
    if(messNumber < messages.length)
    {
        var alreadyCached = false;
        
        //om det finns meddelanden cachade (annars ska alla få en area-kod)
        if(cachedMessages !== null && cachedMessages.error === undefined)
        {
            for(var i = 0; i < cachedMessages.messages.length;i++)
            {
                //kollar om meddelandet redan finns cachat. Då behöver vi inte kalla på SR's api i onödan. 
                if(messages[messNumber].id === cachedMessages.messages[i].id)
                {
                    messages[messNumber] = cachedMessages.messages[i]
                    alreadyCached = true;
                    traffic.addAreaCodeToEachMessage(messages, callback, ++messNumber); //nästa meddelande
                    break;
                }
            }            
        }

        //inte cachad sedan tidigare.
        if(!alreadyCached)
        {
            traffic.hasChanged = true;
            
            //hämta area-kod för detta meddelande, lägg till till i meddelandet, och kolla nästa meddelande.
            this.getFromURL("http://api.sr.se/api/v2/traffic/areas?format=json&latitude="+messages[messNumber].latitude+"&longitude="+messages[messNumber].longitude, function(json)
            {
                messages[messNumber].area = json.area.trafficdepartmentunitid;
                traffic.addAreaCodeToEachMessage(messages, callback, ++messNumber);
            });
        }
        
    }
    
    //slut på det roliga
    else
    {
        callback(messages);
    }
}

module.exports = Traffic;