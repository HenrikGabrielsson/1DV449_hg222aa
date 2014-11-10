var globals =
{
    http: require("http"),
    cheerio: require("cheerio"),
    fs: require("fs"),
    
    //fil där JSON-strängar ska sparas
    courseFile: "courses.json",
    
    cachedObjects: null,
    
    //två egenskapade klasser som jag vill kunna skapa instanser av
    Course: require("./course.js"),
    Entry: require("./entry.js"),

    timeBetweenScrapes: 60000, //tid mellan varje skrapning (5 minuter i millisekunder)
    scrapeHost:"coursepress.lnu.se",
    scrapeListPath:"/kurser/",
    
    userAgent: "hg222aaBot/1.0 (hg222aa@student.lnu.se)",
    
    linkList: [],
    courseList:[]
};

function checkIfTimeToScrape()
{
    var cachedObjects = globals.fs.readFileSync(globals.courseFile, {encoding:"utf8", flag:"r"})
    var cachedJSON;
    
    //kolla om det går att skapa en JSON av innehållet i filen. annars får sidan skrapas på nytt
    try
    {
        cachedJSON = JSON.parse(cachedObjects);
    }
    catch(error)
    {
        return true;
    }
    
    if(cachedJSON.statistics.lastScrape + globals.timeBetweenScrapes < Date.now())
    { 
        return true;
    }
    
    return false;
    
}


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
    if(checkIfTimeToScrape())
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
        "<p>Nothing here yet</p>"+
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
    
    //skapa ett node tree, och plocka ut listan med id "blogs-list"
    var blogsList = globals.cheerio.load(body)("#blogs-list");

    //returnera true om den finns.
    return blogsList.html() !== null;
    
}

function getCourseLinks(body)
{
    //skapa ett node tree, och plocka ut listan med id "blogs-list"
    var blogsList = globals.cheerio.load(body)("#blogs-list");
    
    //lägger till alla länkar till alla kurser i listan i en array som ska returneras
    for(var i = 0; i < blogsList.children().length; i++)
    {
        var link = blogsList.children(i).children(".item").children(".item-title").children("a").attr("href");
        
        //sorterar bort icke-kurser
        if(link.indexOf("http://coursepress.lnu.se/kurs/") !== -1)
        {
            globals.linkList.push(link);
        }
    }
}


//denna funktion hämtar alla länkar från alla listor
//bpage visar vilken sida i pagineringen som ska genomsökas
function getCourseList(bpage, callback)
{
    var linkList = [];
    
    
    var request = globals.http.request({hostname: globals.scrapeHost, path: globals.scrapeListPath + "?bpage=" + bpage, headers: {'user-agent': globals.userAgent}}, function(res)
    {
        var data;
        
        //vill ha sidan i utf-8. annars blir den svår att förstå...
        res.setEncoding('utf8');
        
        //om vi får lite data så lägger vi in det i variabeln data ( det kan komma mer..)
        res.on("data", function(body)
        {
            data += body;
        });
        
        //nu har vi fått hela sidans body
        res.on("end", function()
        {
            //om det finns länkar på sidan (listan är inte slut än)
            if(containsCourseLinks(data))
            {
                getCourseLinks(data);
                
                //här kallar funktionen på siog själv och hämtar ut länkarna från de andra sidorna också, genom att plussa på sidnumret
                //när den sista sidan har laddat klart så kommer alla callbacks köras i bakvänd ordning för att komma tillbaka till början
                getCourseList(++bpage, function(){callback() });
                
            }
            else
            {
                callback(); //we're done...
            }

        })
    })
    
    
    
    //skicka!
    request.end();    
}

function createJSONString()
{
    
    var statsObject = 
    {
        lastScrape: Date.now(),
        numberOfCoursesScraped: globals.courseList.length
    };
    
    var jsonString = JSON.stringify({statistics:statsObject, courses:globals.courseList}, null, "\n");
    

    return jsonString;
    
}

//denna funktion sparar alla Course-objekt i en json-fil
function saveToFile()
{
    jsonString = createJSONString();
    
    globals.fs.writeFile(globals.courseFile, jsonString, function(error)
        {
            if(error)
            {
                console.log("Something has gone horribly wrong when saving to file.");
                return;
            }
        });
}


//den här funktionen ska "skrapa" ner alla länkar till 
function scrape()
{
    //länkar till alla kurser ska läggas till här
    getCourseList(1, function()
    {
        
        //när alla länkar till kurser är hämtade så hämtas datan från alla kurssidor.
        getAllCourses(function()
        {
            
            //när all kursdata hämtats så sparas allt i en json-fil
            saveToFile();
        });
    });
}


function getAllCourses(callback)
{
    for(var i = 0; i < globals.linkList.length; i++)
    {
        getCourse(globals.linkList[i], coursePageScraped);
    }
    
    //function som håller reda på hur många kurser som har skrapats och när alla är klara så rapporterar den tillbaka till funktionen som kallade på getAllCourses
    var coursesDone = 0;
    function coursePageScraped()
    {
        coursesDone++;
        
        if(coursesDone == globals.linkList.length)
        {
            callback();
        }
    }
}

//skapar ett Course-objekt efter att ha hämtat data från länken till kursen.
function getCourse(link, callback)
{
    var course;
    var path = link.split(globals.scrapeHost)[1];
    
    var request = globals.http.request({hostname: globals.scrapeHost, path: path, headers: {'user-agent': globals.userAgent}}, function(res)
    {
        var data;
    
        //vill ha sidan i utf-8. annars blir den svår att förstå...
        res.setEncoding('utf8');
        
        res.on("data", function(body)
        {
            data += body;
        });
        
        res.on("end", function()
        {
            //skapa ett node-tree av html-kroppen
            var page = globals.cheerio.load(data);
            
            //hämta ut olika delar.
            var courseTitle = page("h1", "#header-wrapper").text();
            var courseCode = page("ul", "#header-wrapper").children(2).children("a").text();
            var syllabusLink = "http://prod.kursinfo.lnu.se/utbildning/GenerateDocument.ashx?templatetype=coursesyllabus&code=" +courseCode+ "&documenttype=pdf&lang=sv"; 
            var introText = page("p", ".entry-content").text();
            
            //inlägg har lite olika format så för att få med alla "senaste inlägg" så får vi först ta reda på hur html-sidan är uppbyggd.
            var lastHTMLEntry;
            if(page("#content").is("div"))
            {
                lastHTMLEntry = page("section", "#content").children("article").first();

            }
            
            else if(page("#content").is("section"))
            {
                lastHTMLEntry = page("#content").children(2);
                    
            }
        
            var entryHeader = lastHTMLEntry.children(".entry-header");
            var entryDate = entryHeader.children(".entry-byline").text().match(/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}/);
            
            if(entryDate !== null)
            {
                entryDate = entryDate[0];
            }
            else 
            {
                entryDate = null;
            }
            
            //skapa senaste inlägget (sem ett Entry-objekt)
            var entry = new globals.Entry
            (
                entryHeader.children(".entry-title").children("a").text(),
                entryHeader.children(".entry-byline").children("strong").text(),
                entryDate
            );
            
            //Skapa nytt Course-objekt
            var course = new globals.Course(link, courseTitle, courseCode, syllabusLink, introText, entry);
            
            globals.courseList.push(course);
            callback();
        });
    });
    
    request.end();
}


//lyssna genom denna port och kör handler när någon ansluter.
globals.http.createServer(handler).listen(8020);
