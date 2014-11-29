var modules = 
{
    http: require('http'),
    ns: require('node-static'),
    sqlite: require('sqlite3'),
  
    traffic: require('./traffic.js'),
    maps: null //require('./maps.js')
};

var globals = 
{
    fileServer: null,
    db:null,
    traffic: null, //objekt som hämtar trafikinformtation
    maps: null //objekt som hämtar karta.
    
}

var init = function()
{
    globals.db = new modules.sqlite.Database("db.db");
    
    console.log(globals.db);
    
    globals.fileServer = new modules.ns.Server("./public",{cache: 10});
    globals.traffic = new modules.traffic();
    
    globals.traffic.getTrafficNews();
    setInterval(globals.traffic.getTrafficNews, 5000);
};

//När en klient ansluter körs denna funktion.
var handler = function(req, res) 
{
    console.log(req.url);
    
    //ignorera favicon
    if(req.url == "/favicon.ico")
    {
        res.writeHead(200, {"Content-Type": "text/html"});
        res.end();
        return;
    }
    
    //annars skicka filer
    serveFiles(req, res);
    
    
};

//funktion som skickar efterfrågade filer till klient
function serveFiles(req,res)
{
    req.addListener('end', function() 
    {
        globals.fileServer.serve(req, res); //skicka filer

    }).resume();           
}


init(); //kör denna funktion när servern startar.

//lyssna genom denna port och kör handler när någon ansluter.
modules.http.createServer(handler).listen(8080);
