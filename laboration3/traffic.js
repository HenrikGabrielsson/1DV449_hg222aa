var Traffic = function()
{
    this.sqlite = require('sqlite3').verbose();
    this.db = new this.sqlite.Database("traffic.db");
    this.url = "http://api.sr.se/api/v2/traffic/messages?format=json&size=100";
    
    
}

Traffic.prototype.getTrafficNews = function()
{
    this.getMessagesFromSR(function(json)
    {
        saveMessagesInDatabase(json);
    });
    
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
        
        //när all data kommit från sr så parsar vi det som json
        res.on('end', function()
        {

            //parsea som JSON och plocka ut intressanta bitar.
            json = JSON.parse(chunks);
            
            //kör callback och skickar med json-data som lästes ut.
            callback(json);
            
        })
    });

    request.end();
}


module.exports = Traffic;