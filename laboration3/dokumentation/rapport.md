#Laboration 3 - Laborationsrapport
Skriven av: Henrik Gabrielsson (hg222aa)  
Kurs: Webbteknik II (1DV449)  

##Krav hos API:erna
###Sveriges Radios öppna API
Sveriges radio har några få krav på användningen av deras API. Det enda kravet som ställs i deras "Användarvillkor" är: 
>Materialet som tillhandahålls via API får inte användas på ett sådant sätt att det skulle kunna skada Sveriges Radios oberoende eller trovärdighet.

De ber också användare av API:et att inte göra mer anrop än nödvändigt, även om de inte sätter en specifik gräns. 


###Google Maps JavaScript API v3

Google Maps kräver att man har en nyckel som man skickar med som en GET-parameter när man läser i Google Maps-scriptet på klientsidan. De begränsar också det gratis användandet av API:n till 25 000 anrop per dag. Om man går över denna gräns så måste man till slut börja betala till Google för att få använda API:et.

##Cachning 

För att slippa anropa SR mer än nödvändigt så cachar jag alla meddelanden i en enkel text-fil på server-sidan. Dessa uppdateras sedan var 5:e minut genom att fråga SR efter de senaste 100 meddelandena igen. Då jag också vill ha ett trafikområde kopplat till varje meddelande så läser jag också in detta en gång per meddelande (Men bara om de inte redan finns cachade sedan tidigare). 
Trafikområdena kan läsas in manuellt med metoder på servern, men då jag antar att dessa förändras väldigt sällan så finns det ingen speciell tidsgräns för hur länge dessa ska cachas. 


##Risker

Då Sveriges Radio vill att utvecklare som använder deras API ska ha i åtanke att deras API är i beta, så finns det nog en ganska stor risk att deras API kommer att förändras, vilket eventuellt skapar problem när data hämtas därifrån. 

Om (mot all förmodan) antalet besökare på trafikkartan skulle öka väldigt mycket så finns det också en risk att antalet anrop mot Google Maps går över maxgränsen, vilket i så fall betyder att man får börja betala Google en mindre summa pengar för att använda API:et. De lovar dock att inte stänga ute trafik om man skulle övergå maxgränsen. Som sagt är denna risken inte jättestor.

##Säkerhet
Besökare på sidan kan inte göra mycket för att riskera säkerheten för någon annan på webbplatsen. Data från SR skrivs ut på sidan, vilket någon arg radiopratare kanske kan utnyttja för att göra en code injection-attack, men HTML-taggar och script körs inte, utan skrivs ut direkt på sidan. 

##Optimering

Jag har minskat antalet http-förfrågningar mot servern genom att enbart ha en fil för CSS och en fil för JavaScript. Detta gör att filerna blir något större, men det går ändå fortare än att göra flera anrop. De har dessutom blivit minimerade för att de ska gå fortare att läsa in. 

Det finns ingen intern JavaScript eller CSS i HTML-dokumentet, och all CSS läses in i sidhuvudet och all JavaScript i botten av sidkroppen. 

Filer som skicka till klienten kommer att se likadana ut varenda gång, vilket gör att jag satte en ganska lång max-age (2 veckor).

Alla meddelanden och områden skickas till klienten när de går till sidan. På detta sättet så behöver man aldrig efterfråga sidan igen om man vill till exempel filtrera händelserna efter område eller typ.




















