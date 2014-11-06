var globals =
{
    http: require("http"),
    cheerio: require("cheerio"),
    
    //två egenskapade klasser som jag vill kunna skapa instanser av
    Course: require("./course.js"),
    Entry: require("./entry.js"),
    
    lastScrape: null, //senaste tidpunkten som en skrapning genomfördes
    readableLastScrape:null,
    timeBetweenScrapes: 5000, //tid mellan varje skrapning (5 minuter i millisekunder)
    scrapeURL: "http://coursepress.lnu.se/kurser/"
};


//När en klient ansluter körs denna funktion.
function handler (req, res) 
{
    
    //be favicon att dra åt he..
    if(req.url == "/favicon.ico")
    {
        res.writeHead(200, {"Content-Type": "text/html"});
        res.end();
        return;
    }
    
    //om det har gått en viss tid sedan senaste skrapningen så ska den göras igen.
    if(globals.lastScrape === null || Date.now()-globals.lastScrape >= globals.timeBetweenScrapes)
    {
        scrape();
    }
    
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

//kollar om det finns någon lista med kurslänkar i given html
function containsCourseLinks(body)
{
    
    //skapa ett node tree, av html-elementen
    $ = globals.cheerio.load(body);

    //kolla om det finns en lista med länkar till kurssidor 
    console.log($("#blogs-list"));
    
}

//den här funktionen ska "skrapa" ner alla länkar till 
function scrape()
{
    
    //länkar till alla kurser ska läggas till här
    var courseLinks = [];
    
    var request = globals.http.request(globals.scrapeURL, function(res)
    {
        
        var data;
        
        //vill ha sidan i utf-8. annars blir den svår att förstå...
        res.setEncoding('utf8');
        
        //om vi får lite data så lägger vi in det i variabeln data ( det kan komma mer..)
        res.on("data", function(body)
        {
            data += body;
        })
        
        //nu har vi fått hela sidans body
        res.on("end", function()
        {
            if(containsCourseLinks(data))
            {
                //lägg till alla nya länkar
                courseLinks = courseLinks.concat(getCourseLinks(data));
            }
        })
    })
    
    //stoppa http-requesten
    request.end();
    
    //uppdatera tid-objekten
    globals.lastScrape = Date.now();
    globals.readableLastScrape = createDateString();
    
}

//lyssna genom denna port och kör handler när någon ansluter.
globals.http.createServer(handler).listen(8080);
