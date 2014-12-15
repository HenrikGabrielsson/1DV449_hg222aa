
var app = 
{
    
    modules: 
    {
        http: require("http"),
        ns: require("node-static"),
        sio: require("socket.io")
    },
   
    httpServer: null,
    fileServer: null,
 
    serveFiles: function(req, res) 
    {
        req.addListener('end', function() 
        {
            app.fileServer.serve(req, res); //skicka filer
    
        }).resume();                
    }, 
   
    httpHandler : function(req, res) 
    {
        //ignorera favicon
        if(req.url == "/favicon.ico")
        {
            res.writeHead(200, {"Content-Type": "text/html"});
            res.end();
            return;
        }
        
        //annars skicka filer
        app.serveFiles(req, res);
    },
   
    init: function() 
    {
        app.httpServer = app.modules.http.createServer(app.httpHandler);
        app.fileServer = new app.modules.ns.Server("./public",{cache: 10});
    }
};

app.init(); //vid serverstart

//lyssnar efter anslutande klienter
app.httpServer.listen(8888);