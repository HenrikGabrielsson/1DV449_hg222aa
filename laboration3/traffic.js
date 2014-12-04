var Traffic = function()
{
    this.http = require('http');
    this.fs = require("fs");
    this.messageFile = "traffic.json";

    this.messageUrl = "http://api.sr.se/api/v2/traffic/messages?format=json&size=100&sort=createddate";
}

Traffic.prototype.saveTrafficNewsFromSR = function()
{
    //workaround
    var fs = this.fs;
    var traffic = this;
    var messageFile = this.messageFile;

    this.getMessagesFromSR(function(json)
    {
        traffic.addAreaCodeToEachMessage(json.messages, function(messagesWithAreas)
        {

            json.messages = messagesWithAreas;
            fs.writeFileSync(messageFile, JSON.stringify(json));
        },0);
        
        
    });
    
}


Traffic.prototype.getMessages = function()
{
    var fileContent = this.fs.readFileSync(this.messageFile, {encoding:"utf8", flag:"r"});
    
    var cachedJSON;
    
    //fånga fel om det inte går att tolka innehållet som json.
    try
    {
        cachedJSON = JSON.parse(fileContent);
    }
    catch(error)
    {
        return null;
    }
    return cachedJSON;
}

Traffic.prototype.addAreaCodeToEachMessage = function(messages, callback, messNumber)
{
    var traffic = this;
    
    var cachedMessages = this.getMessages();

    if(messNumber < messages.length)
    {
        var alreadyCached = false;
        
        if(cachedMessages !== null)
        {
            for(var i = 0; i < cachedMessages.messages.length;i++)
            {
                if(messages[messNumber].id === cachedMessages.messages[i].id)
                {
                    alreadyCached = true;
                    traffic.addAreaCodeToEachMessage(messages, callback, ++messNumber);
                    break;
                }
            }            
        }

        if(!alreadyCached)
        {
            this.addAreaToMessage(messages[messNumber], function(messageWithArea)
            {
                messages[messNumber] = messageWithArea;
                traffic.addAreaCodeToEachMessage(messages, callback, ++messNumber);
            });
        }
        
    }
    
    else
    {
        callback(messages);
    }
}

Traffic.prototype.addAreaToMessage = function(message, callback)
{
    var areaUrl  = "http://api.sr.se/api/v2/traffic/areas?format=json&latitude="+message.latitude+"&longitude="+message.longitude;    
    
    var json;
    
    var request = this.http.request(areaUrl, function(res)
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
            
            //gör till json och lägg till datum för senaste hämtning
            var json = JSON.parse(chunks);
            
            message.area = json.area.trafficdepartmentunitid;
            
            //tillbaka till sträng och return
            //kör callback och skickar med json-data som lästes ut.
            callback(message);
            
        })
    });

    request.end();
}

Traffic.prototype.getMessagesFromSR = function(callback)
{
    var json;
    
    var request = this.http.request(this.messageUrl, function(res)
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
            
            //gör till json och lägg till datum för senaste hämtning
            var json = JSON.parse(chunks);
            json.dataRecievedTime = Date.now();
            
            //tillbaka till sträng och return
            //kör callback och skickar med json-data som lästes ut.
            callback(json);
            
        })
    });

    request.end();
}

module.exports = Traffic;