
var modules = 
{
    http: require('http'),
    ns: require('node-static'),
    sio: null,
  
    traffic: require('./traffic.js'),
    maps: null 
};

var globals = 
{
    httpServer: null,
    fileServer: null,
    traffic: null, //objekt som hämtar trafikinformtation
    maps: null //objekt som hämtar karta.
    
};

//init-funktion, skapar modul-objekt och hämtar data från sr, när servern startar. Därefter var 5:e minut
var init = function()
{
    globals.httpServer = modules.http.createServer(handler);
    globals.fileServer = new modules.ns.Server("./public",{cache: 1});
    globals.traffic = new modules.traffic(modules.http);
 
    modules.sio = require('socket.io').listen(globals.httpServer);

    //globals.traffic.saveTrafficNewsFromSR();
    setInterval(function()
    {
        //globals.traffic.saveTrafficNewsFromSR()
        
    }, 300000);
};

//skickar json-fil med messages och areas till klient genom socket.
var broadcastMessages = function(socket)
{
    var json = globals.traffic.getMessages();
    
    //skicka med alla areas i samma fil;
    json.areas = globals.traffic.getAreas().areas;
    
    socket.emit("trafficMessages",json);
}


//När en klient ansluter körs denna funktion.
var handler = function(req, res) 
{
    
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
globals.httpServer.listen(8888);
modules.sio.sockets.on('connection', broadcastMessages);