var Traffic = function()
{
    this.fs = require("fs");
    this.file = "traffic.json";
    
    this.url = "http://api.sr.se/api/v2/traffic/messages?format=json&size=100&pagination=false&sort=createddate";
}

Traffic.prototype.getTrafficNewsFromSR = function()
{
    //workaround
    var fs = this.fs;
    var file = this.file;

    this.getMessagesFromSR(function(jsonString)
    {
        fs.writeFileSync(file, jsonString);
    });
    
}


Traffic.prototype.getMessages = function()
{
    var fileContent = this.fs.readFileSync(this.file, {encoding:"utf8", flag:"r"});
    
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



Traffic.prototype.getMessagesFromSR = function(callback)
{
    var http = require('http');
    
    var json;
    
    var request = http.request(this.url, function(res)
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
            callback(JSON.stringify(json));
            
        })
    });

    request.end();
}

module.exports = Traffic;