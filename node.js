var globals =
{
    app: require("http").createServer(handler), 
    lastScrape: null, //senaste tidpunkten som en skrapning genomfördes
    timeBetweenScrapes: 300000 //tid mellan varje skrapning (5 minuter i millisekunder)
};


//När en klient ansluter körs denna funktion.
function handler (req, res) 
{
    Scrape();
    
    res.writeHead(200, {"Content-Type": "text/html"});
    res.write
    (
        "<!doctype html>"+
        "<html>"+
        "<head>"+
        "</head>"+
        "<body>"+
        "<p>"+globals.lastScrape+"</p>"+
        "</body>"+
        "</html>"
    );
    res.end();
}


//den här funktionen ska "skrapa" ner resultat från en extern html-sida
function Scrape()
{
    //om det har gått en viss tid sedan senaste skrapningen så ska den göras igen.
    if(globals.lastScrape === null || Date.now()-globals.lastScrape >= globals.timeBetweenScrapes)
    {
        console.log("scrape");
        globals.lastScrape = Date.now();
    }
    
}

globals.app.listen(8080); //lyssna genom denna port.
