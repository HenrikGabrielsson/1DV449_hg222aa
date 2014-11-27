#Laboration 2 - Laborationsrapport
Skriven av: Henrik Gabrielsson (hg222aa)  
Kurs: Webbteknik II (1DV449)  
Datum: 2014-11-18

**Publik url:** http://henrikgabrielsson.se/laboration2/public/

##Del 1 - Säkerhetsproblem

###Säkerhetsrisk 1 - Inloggningsfunktionen är felbyggd.
Det första säkerhetshålet jag hittade var att programmet inte ens kollar om man är inloggad eller inte, på grund av en bugg där PHP tolkar en sträng ("Could not find the user") som *TRUE* vilket ger vilket namn/lösenord som helst användaren åtkomst till applikationen.

Jag rättade till detta igenom att returnera false istället och sedan visa felmeddelandet på 401-sidan.

###Säkerhetsrisk 2 - SQL-injektioner
Man kan lura databasen att köra SQL-frågor genom att skicka in *SQL-injektioner*. Till exempel kan man gå förbi inloggningsfunktionen genom att skriva **hack' OR 1=1;** som användarnamn och sedan valfritt lösenord. Detta ger användaren inträde, eftersom 1 faktiskt är lika med 1 och databasen returnerar TRUE. Man kan också använda detta för att skicka in data i chatten eller inloggningsformuläret som tar bort tabeller eller ändrar andra värden i databasen. 

För att skydda applikationen så har jag använt mig av *prepared statements* som innebär att man först förbereder databasen på den fråga som ska skickas in, och sedan lägger till värdena i parametrar som skickas in i efterhand. 

###Säkerhetsrisk 3 – Inget skydd mot sessionsstöld
Om någon lyckas komma över en användares session så är det inte så svårt att använda den för att logga in på sidan. *login_string* som sparas i sessioner hjälper inte eftersom den är samma för samma användare hela tiden. Om någon lyckas komma över någon annans session så kan de göra allt som sessionsägaren själv skulle kunna göra. 

Nu så kollar systemet istället så att IP-adressen är samma som tidigare samt att user_agent (webbläsare, operativsystem etc.) är samma, så att det blir lite krångligare för en hacker att identifiera sig som någon annan. Från allra första början så kontrolleras det om man är inloggad, så att vem som helst kan komma åt sidan *mess.php*. Detta är också åtgärdat.


###Säkerhetsrisk 4 – data i klarttext i databasen
Lösenord som sparas i databasen måste vara hashade så att ingen som får tag på databasen kan ta reda på vad det är för lösenord som sparas där. Om en hacker får tag på en databas med okrypterade lösenord så kan dessa användas för att logga in som vilken användare som helst på webbplatsen, men också på andra webbplatser där användare har samma användarnamn och lösenord (vilket många har). 

För att motverka skadorna vid eventuell stöld, så har alla lösenord hashats (de kan ej längre ”dekrypteras tillbaka” till sina ursprungliga lösenord.) För att försvåra arbetet ytterligare så saltas också alla lösenord, vilket gör användningen av ett så kallat *Rainbow table* meningslöst. 


###Säkerhetsrisk 5 -  Åtkomst till hemliga filer
Genom att skriva till exempel www.labbymezzage.com/db.db så kan man ladda ner hela databasen, full med användaruppgifter. Databasen får ju ingen få tag på, och även php_errors.log kan ge elaka hackare en uppfattning om vilka delar på sidan som är extra svaga.

Alla filer som man får komma åt ligger nu i mappen public och resten läggs utanför detta filträd, vilket gör att man inte kan få åtkomst till dessa hemliga filer.


###Säkerhetsrisk 6 - Cross-site request forgery
Genom att en medlem som har en inloggningssession på Labby Mezzage går till en sida och klickar på en länk, eller bara ser en bild (Ex: *http://127.0.0.1:8888/lab2/functions.php?function=add&name=user&message=samy%20is%20my%20hero*) så kommer man kunna skicka meddelanden i användarens namn, utan att användaren vet om det.  

Detta löste jag med hjälp av *Synchronized Token Pattern* som går ut på att ett token genereras slumpmässigt och läggs till i en sessionsvariabel hos användaren, och sedan också i ett dolt fält i formuläret för att skicka meddelanden. Sedan matchas denna token som kom från formuläret med den i sessionsvariablen så att användaren måste ha skickat meddelandet från formuläret på Labby Mezzage. 

###Säkerhetsrisk 7 -  Utloggningsfunktionen fungerar inte
Utloggningsfunktionen i nuläget gör ingenting, då sessionskakorna fortfarande finns kvar, och någon som lyckats sno åt sig kakorna kan fortfarande vara inloggad, även om användaren har "loggat ut"

Nu kan användaren klicka på *logga ut*, och så förstörs sessionen så att även en sessionstjuv blir utloggad. 


##Del 2 – Optimering

###Åtgärd 1 – minska antalet HTTP-förfrågningar

Att det krävs mer HTTP-förfrågningar för att få alla nödvändiga filer kan dra ut på laddningstiden för en sida, och därför har jag valt att ta bort så många onödiga sådana som möjligt. Främst genom att sätta ihop Script-filer (MessageBoard.js och Message.js) så att bara en hämtning krävs och att inte ta med filer som inte behövs (jquery.js, en mer kompakt fil av samma version finns redan tillgänglig.) 

####Tisåtgång innan/efter
Så här lång tid tog det att ladda sidan innan och efter optimeringen:

Innan (mess.php):
Medel: 504 millisekunder.

Efter (mess.php):
462 millisekunder.


####Reflektion

Att ta bort och sätta ihop filer tror jag gör stor skillnad på större webbplatser där många filer behöver hämtas in. I detta fallet sparade jag nog också lite tid på att använda den mer kompakta jquery-filen istället för den med mer kommentarer och mellanrum också, vilket egentligen inte hör till denna åtgärd.




###Åtgärd 2 – Flytta script-filer till botten av sidan.

När man har HTML-kod under en script-tagg så kommer renderingen av sidan stoppas tills javascript-filen har laddats klart. Dessutom kommer inga andra filer laddas ner under tiden, för att undvika att javascript-filerna kommer i fel ordning, vilket kan orsaka fel i vissa webb-applikationer.

 ####Tisåtgång innan/efter
Så här lång tid tog det att ladda sidan innan och efter optimeringen:

Innan (mess.php):
Medel: 444 millisekunder.

Efter (mess.php):
373 millisekunder

####Reflektion

Den här åtgärden verkar ha räddat väldigt mycket mer tid än jag trodde den skulle göra. Tror dock mycket har att göra med jquery-filen som är relativt stor (91 kb), och som stoppar sidan under en lång tid när den hämtas längst upp i html-koden.


###Åtgärd 3 – undvik intern CSS och JavaScript

Egentligen så går det lite fortare att ladda en sida med intern CSS/JS eftersom man då slipper att göra fler HTTP-requests för att hämta in de externa css-filerna och javascript-filerna. Om man dock antar att css/js-filerna cachas under en längre period än html-filer (som i allmänhet uppdateras mycket oftare), så blir ju html-filen mindre och går fortare att hämta, plus att man inte behöver tänka på att hämta externa filer varenda gång man vill komma åt sidan. 

 ####Tisåtgång innan/efter
Så här lång tid tog det att ladda sidan innan och efter optimeringen:

Innan (mess.php):
Medel: 444 millisekunder.

Efter (mess.php):
471 millisekunder

####Reflektion

Testet gick långsammare efter åtgärden, men detta var ju förväntat eftersom jag testar utan någon caching. Nu blir det fler filer att hämta så det tar längre tid. Dock kommer det ju gå fortare när css/js-filerna sparas undan och inte behöver hämtas varenda gång.

###Åtgärd 4 – Ta bort duplicerade scripts

Det händer ju att man råkar importera två exakt likadana script vilket är onödigt, då de inte ger något extra för applikationen samtidigt som det krävs resurser för att hämta och tolka scriptet.


 ####Tisåtgång innan/efter
Så här lång tid tog det att ladda sidan innan och efter optimeringen:


Innan (mess.php):
471 millisekunder

Efter (mess.php):
427 millisekunder

####Reflektion

Jag läste tidigare in filen bootstrap.js två gånger plus filen script.js som verkar vara en exakt kopia på bootstrap.js. Att ta bort två anrop hjälpte nog hastigheten lite med tanke på att dessa filer var ganska omfattande (66 kb).


###Åtgärd 5 – Minimera javascript och css.

Att ta bort alla blanka tecken och kommentarer kan till viss del hjälpa eftersom filerna då blir mindre, och går fortare att hämta. Detta är vanligt att man gör för javascript-filer men här vill jag också testa att göra detsamma för css-filerna.
Man kan också använda sig av *obfuscation* som är ett sätt att göra filerna ännu mindre genom att till exempel korta ner variabel-namn. Detta kan dock orsaka fel och jag vågar inte testa det, då jag inte har tillräckligt med koll på koden jag ska optimera.


Innan (mess.php):
467 millisekunder

Efter (mess.php):
396 millisekunder

####Reflektion

Då många av filerna var väldigt stora så var det inte någon större överraskning att hämtningen skulle gå lite fortare på detta sättet.






