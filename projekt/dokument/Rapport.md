#Slutrapport - SteamStuff
**Namn:** Henrik Gabrielsson (hg222aa)  
**Kurs:** Webbteknik II (1DV449)  
**Datum:** 2015-01-12  
**Publik URL:**  [SteamStuff](http://henrikgabrielsson.se/SteamStuff/)

##Inledning
Applikationen som jag har skapat heter SteamStuff. Det �r en mashup-applikation som l�ter en anv�ndare logga in med sitt Steam-konto (Steam �r en applikation d�r man enkelt kan hantera sina spel, k�pa nya, och spela mot andra spelare.) och sedan f� f�rslag p� kringprodukter (merchandise) fr�n Ebay. F�rslagen baseras p� vilka spel man �ger och p� hur mycket man spelat dem. Man kan ocks� f� f�rslag p� produkter till sina Steam-v�nner, om man vill k�pa en present till n�gon av dem.
API:erna som jag anv�nt f�r att skapa applikationen �r [Steam Web API](https://developer.valvesoftware.com/wiki/Steam_Web_API) och [Ebay Finding API](http://developer.ebay.com/DevZone/finding/Concepts/FindingAPIGuide.html). F�r att man ska kunna logga in med sitt  Steam-konto s� anv�nds Steams egna OpenId-tj�nst.  
Jag har letat runt en del och medan det finns m�nga sidor som s�ljer gaming merchandise, s� hittar jag inga som f�rs�ker skr�ddarsy f�rslag baserat p� vilka spel man spelar, s� d�rf�r t�nkte jag att det kunde bli intressant att f�rs�ka skapa en s�dan sajt.

##Datafl�desschema

###Lyckad inloggning
![Lyckad inloggning](successful_login.png)

###H�mta anv�ndare fr�n Steam
![H�mta anv�ndare fr�n Steam](server_getUserFromSteam.png)

###H�mta produkter fr�n Ebay
![H�mta produkter fr�n Ebay](server_getProductsFromEbay.png)

###Klient h�mtar data fr�n servern
![Klient h�mtar data fr�n servern](client_getProducts.png)

##Server
P� server-sidan s� har jag anv�nt mig av PHP och MySql. N�r jag b�rjade med projektet s� jobbade jag med Node.js, men eftersom jag inte fick det att fungera med OpenId och tiden b�rjade rinna iv�g s� hoppade jag �ver till PHP eftersom det �r ett spr�k som jag k�nner till v�l sedan tidigare, och d�r det var mycket enkelt att koppla ihop Steams openId-tj�nst med min webbplats. Detta gjorde jag med hj�lp av biblioteket [LightOpenId](https://gitorious.org/lightopenid). MySql-databasen anv�nder jag mig av eftersom jag har tillg�ng till den genom webbhotellet d�r jag publicerar applikationen. 

###Steam
####H�mta data
Jag anv�nder Steam Web API f�r att h�mta in data fr�n en anv�ndares profil (anv�ndarnamn, unikt id och profilbildens s�kv�g). D�refter g�r jag �nnu ett anrop f�r att h�mta in anv�ndarens lista med spel. Detta ger en lista d�r varje spel har en titel, ett id, och information om hur l�nge anv�ndaren har spelat spelet sammanlagt och hur mycket det har spelats under de senaste tv� veckorna.  
Till sist h�mtar jag ocks� in anv�ndarens v�nlista, och varje anv�ndare p� listan, och varje anv�ndares spellista. Detta blir ganska m�nga anrop i slut�ndan, men eftersom jag inte h�mtar en anv�ndare som redan finns cachad och uppdaterad i databasen, s� kan det bli f�rre �n man tror.  

####Cachning
Anv�ndare cachas i min databas i 24 timmar innan jag h�mtar den igen fr�n Steam, och bara om anv�ndaren efterfr�gas. 
Listan med v�nner uppdateras om det efterfr�gas och inte har uppdaterats under de senaste 3 dygnen. Detta eftersom jag inte tror att listan med v�nner f�r�ndras lika ofta som till exempel *antal spelade minuter* p� ett spel.  
Profilbilder cachas inte i databasen utan i filsystemet eftersom det blir enklare att bara spara s�kv�gen i databasen och sedan returnera den n�r bilden ska visas.

###Ebay
####H�mta data
N�r en anv�ndare ber om produkter, s� h�mtas f�rst listan med spel som anv�ndaren �ger. Sedan f�r varje spel en "po�ng" efter hur anv�ndaren har spelat spelet (och extra mycket tyngd l�ggs vid hur mycket det spelats nyligen). Sedan v�ljs max 10 spel ut som servern ska h�mta produkter till fr�n Ebay, och h�r anv�nds po�ngen f�r att det ska bli enklare f�r spel med h�g po�ng att bli valda. Produkter h�mtas och ett visst antal produkter skickas tillbaka till anv�ndaren. 

####Cachning
Om inga produkter h�mtats till ett spel under de senaste 24 timmarna s� h�mtas produkterna fr�n Ebay, och de som cachats i databasen tas bort. Annars h�mtas de alltid fr�n databasen. Varje g�ng som produkter till ett visst spel h�mtas s� kontrolleras varje produkt s� att Ebay-auktionens slutdatum inte har passerat. I dessa fall s� tas produkten bort fr�n databasen och kommer inte visas i resultaten.

###Felhantering
P� Server-sidan har jag v�ldigt enkel felhantering. Om ett undantag kastas n�gonstans i applikationen s� f�ngas det upp av min "Master Controller", som visar en allm�n felsida.

##Klient
N�r ett formul�r skickas p� klientsidan och ber om att f� produkter till en anv�ndare s� k�rs ett script som anropar servern via ajax. Tillbaka skickas en array i JSON med produkter som sedan visas f�r klienten.  
Varje g�ng ett anrop g�ra s� cachas ocks� produkterna f�r anv�ndaren hos klienten. Ifall det blir problem n�sta g�ng produkter ska h�mtas s� kan d� de cachade produkterna visas ist�llet, tillsammans med en varning om detta, och datum/tid n�r produkterna h�mtades. Ifall det inte finns n�gra produkter cachade sedan tidigare, och ett problem uppst�r s� visas ist�llet ett felmeddelande.  
Om det som skickas tillbaka fr�n servern inte kan tolkas som JSON, s� kastas ett undantag i scriptet. Detta f�ngas upp och ist�llet f�rs�ker man visa anv�ndaren de cachade produkterna som det beskrivs i f�reg�ende stycke.

##S�kerhet
###Skydd mot SQL-injektions
Alla anrop till databasen sker med *Prepared statements* vilket g�r att v�rden som skickas till databasen inte kan tolkas som SQL-fr�gor. Anv�ndare har inte m�nga ing�ngar till att skicka in indata som kommer att sparas i databasen, men d�remot kommer det ju data fr�n Steam och Ebay som sparas of�r�ndrat i databasen.

###Skydd mot Code injections
Ingen data som skickas till klienten f�r tolkas som kod av webbl�saren, eftersom en illasinnad hackare d� kan smyga in script som k�rs utan anv�ndarens till�telse. D�rf�r plockas alla html-taggar bort innan data skickas till klienten.

###Skydd mot CSRF
En slumpm�ssigt genererad *token* skickas alltid med fr�n formul�r som skickas till servern, eftersom man vill vara s�ker p� att formul�ret har skickats fr�n en inloggad klient och fr�n sj�lva formul�rsidan. D�rf�r sparas denna token alltid b�de i en se4ssionsvariabel och i ett dolt f�lt p� sidan med formul�ret s� att de kan j�mf�ras innan data returneras.

###Skydd mot sessionsst�ld
F�r att f�rsv�ra f�r sessionstjuvar att kopiera en klients cookies och sedan g� till SteamStuff som en inloggad anv�ndare, s� kollar servern s� att anv�ndaren har samma IP och "user agent" (webbl�sare, operativsystem etc.) som n�r han/hon loggade in. Om det inte �r samma s� loggas anv�ndaren ut.

###S�ker inloggning/utloggning
Inloggningen sk�ts av Steam vilket g�r att ingen k�nslig data som t.ex l�senord beh�ver hanteras av min server. Utloggningen ser till s� att alla sessionsvariabler f�rst�rs.

##Optimering och Prestanda
###Minimerat antal http-f�rfr�gningar
Det tar relativt l�ng tid att h�mta en fil, och d�rf�r m�ste man f�rs�ka anv�nda s� f� filer som m�jligt till sin webbplats.

###CSS l�ngst upp, JavaScript l�ngst ner
F�r att inte tvinga en sida att rendera n�gra element mer �n en g�ng s� l�gger man alla CSS-l�nkar i toppen av sidan s� att det �r det f�rsta som l�ses av webbl�saren n�r en sida laddas.
JavaScript l�gger jag dock i botten eftersom de flesta webbl�sare inte renderar sidan f�rr�n all JavaScript har blivit inl�st.

###Minifierad CSS/JavaScript
Genom att ta bort alla mellanrum, kommentarer och annat "on�digt" fr�n en CSS/JavaScript-fil s� kan filen l�sas n�got fortare av 

webbl�saren.

###Cachade statiska resurser
Egentligen anv�nds ju *Cache Manifest* i den h�r applikationen f�r att g�ra Offline-l�ge m�jligt. Dock hj�lper det ocks� enormt mycket med hastigheten eftersom webbl�saren inte beh�ver g�ra n�gra som helst anrop ut�t i de sidor som redan finns lagrade hos klienten.

##Offline First
jag har f�rs�kt att g�ra s� mycket av inneh�llet som m�jligt �tkomligt �ven om en person som bes�kt webbplatsen tidigare inte har n�gon internetanslutning, eller om han/hon av n�gon annan anledning inte kan komma �t SteamStuffs server. 

###Web Storage
Web Storage �r en HTML5-teknik som till�ter att applikationer sparar en del data hos klienten f�r att inte beh�va skicka mer data �n n�dv�ndigt fr�n servern. jag har anv�nt Web Storage f�r att spara p� de produkter som senast skickades till en anv�ndare, s� att �ven om anslutningen bryts eller om det blir n�got fel n�r nya produkter ska skickas, s� kan scriptet hos klienten fortfarande visa n�nting.

###Cache Manifest
Cache Manifest kan anv�ndas f�r att s�ga till en webbl�sare att vissa resurser kan cachas hos klienten s� att dessa endast beh�ver h�mtas en g�ng, och sen potentiellt aldrig mer (om inte cachen rensas hos klienten eller om manifestet �ndras). P� min sida cachas en del bilder, stylesheets och javascript-filerna. Dessutom cachas alla sidor som klienten n�n g�ng har bes�kt, med undantag f�r inloggningssidan, eftersom denna annars kan hindra anv�ndare fr�n att logga ut, vilket kan bli en s�kerhetsrisk. Produkter kan inte h�mtas i offline-l�ge, men om det finns produkter i webbl�sarens *Web Storage* s� kommer dessa visas ist�llet.  
Om anv�ndaren f�rs�ker komma �t en sida som inte tidigare visats, och som d�rf�r aldrig har cachars s� visas en felsida (problem.html). Denna sida k�r ig�ng en funktion i JavaScript-filen som automatiskt h�mtar sidan som anv�ndaren ville bes�ka n�r det �terigen finns anslutning till servern. Detta g�rs genom att skicka ett ajax-anrop som endast kollar om det finns n�gon anslutning till servern. 

##Egna reflektioner
Det har varit ett sp�nnande och delvis mycket kr�vande projekt att utveckla en mashup-applikation p� egen hand. Delen d�r jag k�nner att jag har utvecklats mest �r Offline First, eftersom att jag aldrig tidigare har reflekterat �ver hur man kan g�ra en webbapplikation mer anv�ndbar f�r klienter med d�lig uppkoppling. Jag �r ganska n�jd med hur jag till sist fick mitt cache manifest att spara de delar av sidan jag ville, men jag fick g�ra om det och fundera mer p� det m�nga g�nger p� grund av de m�nga of�ruts�gbara buggar som uppstod under arbetets g�ng. �ven Web Storage �r en sp�nnande, och betydligt enklare del som jag hoppas att f� anv�ndning f�r i framtiden, d� det f�renklar cachningsarbetet v�ldigt mycket. Att planera hur cachningen skulle se ut p� server-sidan var ocks� intressant, och det var m�nga saker att t�nka p�.   
Jag t�nkte till en b�rjan arbeta med web sockets f�r att skicka data asynkront mellan servern och klienten, men eftersom det inte verkade som att mitt webbhotellet st�djer denna teknik, och eftersom att det var v�ldigt sv�rt att komma ig�ng med det i PHP, s� gick jag till sist �ver till ajax ist�llet. Om jag hade haft mer tid p� mig s� hade jag nog f�rs�kt att se till s� att hela webbplatsen arbetade mer asynkront med servern. Detta f�r att undvika alla omladdningar av sidor, och redirects som tar tid och k�nns klumpigt.  
Jag hann inte heller med att skapa n�gra filtreringsfunktioner hos klienten som jag till en b�rjan hade velat. Till exempel att man skulle kunna v�lja sj�lv vilka spel som man vill f� f�rslag fr�n, och att sortera dem p� pris, land, datum etc. Detta kanske jag forts�tter att arbeta p� senare.  
P� serversidan skulle jag vilja ha lite b�ttre felhantering, med felsidor som s�ger mer om problemet, ist�llet f�r en allm�n felsida som inte s�ger n�nting om vad som egentligen h�nde. �ven p� klientsidan s� finns det viss felhantering men man f�r inte mycket mer hj�lp �n ett kryptiskt meddelande om att n�got gick fel.

##Risker
Jag har ingen koll p� hur m�nga anrop som g�rs mot n�got av API:erna. Dock vet jag att Ebay till�ter 5000 anrop om dagen, och eftersom det endast kan g�ras ett anrop per spel p� Steam om dagen, och det finns ca 4000 spel p� Steam i nul�get, s� kan man inte n� upp till denna siffra �n. Eftersom det hela tiden l�ggs till nya spel s� kan det snart vara dags att kontrollera s� att gr�nsen aldrig �verstigs dock. P� Steam till�ts 100 000 anrop om dagen, s� det kan ocks� bli sv�rt att uppn�, men inte helt om�jligt.  
Steam anv�nder OpenId 2.0 f�r inloggningen med Steam-kontot. Denna teknik anses nu vara "obsolete" vilket gjorde mig v�ldigt os�ker p� om jag ville anv�nda detta till min applikation i b�rjan. Dock kan man inte f� tag p� n�gra k�nsliga uppgifter genom att logga in, utan enbart s�dant som redan �r publikt p� till exempel Steams webbplats.  
Jag har inte haft n�gra m�jligheter att stresstesta applkationen men jag misst�nker att om m�nga anv�ndare f�rs�ker att h�mta produkter samtidigt, s� kan applikationen fort bli v�ldigt l�ngsam. Redan i nul�get, n�r bara jag sj�lv testar applikationen s� m�rker jag att det g�r l�ngsamt n�r anv�ndare, v�nner, och produkter m�ste h�mtas p� nytt fr�n respektive API.

