var Traffic = function()
{
    this.sqlite = require('sqlite3').verbose();
    this.db = new this.sqlite.Database("traffic.db");
    
    this.url = "http://api.sr.se/api/v2/traffic/messages?format=json&size=100";
}

Traffic.prototype.getTrafficNewsFromSR = function()
{
    //workaround..
    var saveMessages = this.saveMessagesInDatabase;
    var db = this.db;

    var workaround = function(messages)
    {
        saveMessages(messages,db);
    }

    this.getMessagesFromSR(function(json)
    {
        workaround(json.messages);
    });
    
}

Traffic.prototype.saveMessagesInDatabase = function(messages, db)
{
    var query; 
    var params;
    var stmt;
    
    //töm databasen
    query = "DELETE FROM message";
    stmt = db.prepare(query);
    stmt.run();
    
    for(var i  = 0; i < messages.length; i++)
    {
        query = "INSERT INTO message VALUES (?,?,?,?,?,?,?,?,?,?)";
        params = [messages[i].id, messages[i].priority, messages[i].createddate, messages[i].title, messages[i].exactlocation, messages[i].description, messages[i].latitude, messages[i].longitude, messages[i].category, messages[i].subcategory,];
    
        stmt = db.prepare(query);
        stmt.run(params);
    }
    
}

Traffic.prototype.getMessages = function(callback)
{
    var messages = [];

    var query = "SELECT * FROM message";
    
    this.db.each(query, function(err, row)
    {
        messages.push(row.id);
    },
    
    //körs när each är klar
    function()
    {
        callback(messages);
    }
    );

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