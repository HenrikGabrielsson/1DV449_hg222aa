var globals =
{
    app: require("http").createServer(handler), 
    lastScrape: null, //senaste tidpunkten som en skrapning genomfördes
    readableLastScrape:null,
    timeBetweenScrapes: 5000, //tid mellan varje skrapning (5 minuter i millisekunder)
    scrapeURL: "http://coursepress.lnu.se/kurser/"
};


//När en klient ansluter körs denna funktion.
function handler (req, res) 
{
    scrape();
    
    res.writeHead(200, {"Content-Type": "text/html"});
    res.write
    (
        "<!doctype html>"+
        "<html>"+
        "<head>"+
        "</head>"+
        "<body>"+
        "<p>"+globals.readableLastScrape+"</p>"+
        "</body>"+
        "</html>"
    );
    res.end();
}

//funktion som returnerar datum/tid i korrekt format 
function createDateString()
{
    var date = new Date(globals.lastScrape);
    
    return date.getFullYear() + "-" + (date.getMonth()+1) + "-" + addZeroIfLessThan10(date.getDate()) + " " + addZeroIfLessThan10(date.getHours()) + ":" + addZeroIfLessThan10(date.getMinutes()) + ":" + addZeroIfLessThan10(date.getSeconds());
}

function addZeroIfLessThan10(number)
{
    if(number < 10)
    {
        return "0" + number;
    }
    return number;
}

//den här funktionen ska "skrapa" ner resultat från en extern html-sida
function scrape()
{
    //om det har gått en viss tid sedan senaste skrapningen så ska den göras igen.
    if(globals.lastScrape === null || Date.now()-globals.lastScrape >= globals.timeBetweenScrapes)
    {
        var request = require("http").request(globals.scrapeURL, function(res)
        {
            
            //vill ha sidan i utf-8. annars blir den ganska värdelös
            res.setEncoding('utf8');
            
            //om vi får nån data
            res.on("data", function(body)
            {
                console.log(body);    
            })
        })
        
        //stoppa http-requesten
        request.end();
        
        //uppdatera tid-objekten
        globals.lastScrape = Date.now();
        globals.readableLastScrape = createDateString();
    }
    
}

globals.app.listen(8080); //lyssna genom denna port.
