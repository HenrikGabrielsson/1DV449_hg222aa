#Laboration 2 - Laborationsrapport
Skriven av: Henrik Gabrielsson (hg222aa)  
Kurs: Webbteknik II (1DV449)  
Datum: 2014-11-18

##Del 1 - Säkerhetsproblem

###Säkerhetshål 1
Det första säkerhetshålet jag hittade var att programmet inte ens kollar om man är inloggad eller inte, på grund av en bugg där PHP tolkar en sträng ("Could not find the user") som *TRUE* vilket ger vilket namn/lösenord som helst användaren åtkomst till applikationen.

Jag rättade till detta igenom att ta bort hela if-satsen som skickar tillbaka denna sträng, då resultatet istället kommer returneras ( och detta tolkas som **FALSE** om ingen användare hittas). Strängen kommer ju ändå inte visas då sidan skickar ett 401-meddelande om man har fel lösenord.

###Säkerhetshål 2
Vid inloggningsformuläret kan man fortfarande logga in utan att kunna något lösenord genom att använda en SQL-injektion. Till exempel genom att ange **hack' OR 1=1 ;** som användarnamn och sedan vadsomhelst som password. 

Detta löste jag genom att använda "Prepared statements". Alltså genom att först förberedda sql-frågan och sedan köra den och då skicka med parametrarna.

###Säkerhetshål 3
Man kan manuellt skriva in URL:en för att komma till chattapplikationen och eftersom ingen kontroll görs att användaren faktiskt är inloggad så kommer man dit, vare sig man har en inloggningssession eller inte.

För att undvika detta så kallar jag på funktionen checkUser() som kollar så det finns en sessionsvariabel, annars får användaren en 401-sida. Denna funktion är dock inte helt perfekt. Den ska också fixas för att göra applikationen mer säker.

###Säkerhetshål 4
