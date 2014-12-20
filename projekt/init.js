
var app = 
{
    //inladdade moduler
    modules: 
    {
        http: require("http"),
        ns: require("node-static"),
        sio: require("socket.io"),
        
        //egna moduler
        login: require("./own_modules/loginHandler")
    },
   
    //för att kunna skicka/ta emot http-anrop
    httpServer: null,
    fileServer: null,
    
    login: null,
 
    //skickar anropade filer
    serveFiles: function(req, res) 
    {
        if(!app.login.isLoggedIn() && req.url.substring(0,6) == "/page/")
        {
            if(req.url.substring(0,11) == "/page/login")
            {
                app.login.authenticate();
            }
            
            req.url = "/page/authenticate.html";
            
            
        }
        
        req.addListener('end', function() 
        {
            app.fileServer.serve(req, res); //skicka filer
    
        }).resume();                
    }, 
   
    //vid start. Lite initieringar.
    init: function() 
    {
        app.httpServer = app.modules.http.createServer(app.serveFiles);
        app.fileServer = new app.modules.ns.Server("./public",{cache: 10});

        app.login = new app.modules.login();
        app.login.setConfiguration();
        
    }
    
    
};

app.init(); //vid serverstart

//lyssnar efter anslutande klienter
app.httpServer.listen(8080);