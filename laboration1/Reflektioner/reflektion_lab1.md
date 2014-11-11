#Laboration 1: Reflektioner - Henrik Gabrielsson (hg222aa)

##Vad tror Du vi har för skäl till att spara det skrapade datat i JSON-format?
Genom att spara den skrapade datan i ett format som även datorer enkelt kan tolka, så blir et enklare att använda datan och visa upp den på olika sätt och i olika sammanhang. Det är enkelt att enbart plocka ut de delar som behövs, och eftersom många vanliga programmeringsspråk har funktioner för att tolka JSON så kan den sparade datan användas av flera olika applikationer.

##Olika jämförelsesiter är flitiga användare av webbskrapor. Kan du komma på fler typer av tillämplingar där webbskrapor förekommer?
Många webbplatser som säljer en tjänst eller produkt använder webbskrapor för att kolla priserna hos sina konkurrenter, för att på så sätt kunna hålla sig billigare än konkurrenten. Ibland används webbskrapor när den skrapade webbplatsen inte har någon API som kan arbetas emot.

##Hur har du i din skrapning underlättat för serverägaren?
Jag skrapar endast webbplatsen högst var 5:e minut, och med större mellanrum än så ifall ingen användare besöker min URL. Jag skickar också med min email-address i User-Agent-fältet för att serverägaren enkelt ska kunna kontakta mig, om han/hon vill att skrapningen ska avbrytas. 

##Vilka etiska aspekter bör man fundera kring vid webbskrapning?
Om serverägaren inte vill att en webbsida ska skrapas så ska man naturligtvis inte göra detta. Om man skrapar en sida så ska man försöka att göra det så sällan som möjligt, och inte skrapa alltför många sidor åt gången. Man bör använda sig av robots.txt för att se vad man får skrapa och inte (om det finns en sådan fil)

##Vad finns det för risker med applikationer som innefattar automatisk skrapning av webbsidor? Nämn minst ett par stycken!
Om man skapar en webbskrapa som läser av html-strukturen av en webbsida och hämtar information därifrån så kan det uppstå problem om webbsidans ägare ändrar dess uppbyggnad, så att webbskrapan inte längre "känner igen" strukturen och kommer då inte längre fungera. En webbskapa som följer länkar automatiskt kan fastna i en loop där den följer samma länkar om och om igen och "stannar kvar" på webbplatsen under en längre tid. Detta gör att den kräver mycket resurser från servern.  

##Tänk dig att du skulle skrapa en sida gjord i ASP.NET WebForms. Vad för extra problem skulle man kunna få då?
Många ASP.NET Web Forms-applikationer använder något som kallas ViewState, som finns för att spara en del formulärdata som användare kan ha skrivit in. Om man inte lyckas skicka med detta med sin webbskrapa så kan det uppstå problem när man ska nå en ASP.NET-sida.

##Välj ut två punkter kring din kod du tycker är värd att diskutera vid redovisningen. Det kan röra val du gjort, tekniska lösningar eller lösningar du inte är riktigt nöjd med.
1. Jag hade velat skapa någon lösning där webbskrapan märker av ifall en skrapning redan har påbörjats, och i så fall vänta in den istället för att sätta igång ännu en, som är nästan helt onödig. 
2. Jag är osäker på om alla callbacks verkligen behövs eller om det finns en bättre lösning. De gör koden mycket svårare att läsa och förstå.

##Hitta ett rättsfall som handlar om webbskrapning. Redogör kort för detta.
Ett exempel på ett rättsfall är AllaAnnonser.se (En webbplats som samlar annonser från flera olika köp/sälj-webbplatser) som blev stämda av blocket.se 2005 då de hade skrapat ner annonser från Blocket utan tillstånd och publicerat de på sin egen sida. Blocket ansåg att detta skulle minska
antalet besökare på Blocket, och att AllaAnnonser skulle tjäna mycket reklampengar på annonser som de tagit från andra ställen. Till slut gick Blocket och AllaAnnonser med på ett avtal där AllaAnnonser får fortsätta använda Blockets annonser men länkar från AllaAnnonser måste gå till Blockets
sökresultatssida istället för direkt till en annons. Dessutom får AllaAnnonser inte sälja vidare annonser till tredje part.

##Känner du att du lärt dig något av denna uppgift?
Jag har lärt mig mer om Node.js, HTTP och och hur man kan arbeta mot ett node-tree för att hämta ut data i ett HTML-dokument. Jag har också lärt mig mycket om webbskrapor och hur man bör bete sig när man använder dem.

