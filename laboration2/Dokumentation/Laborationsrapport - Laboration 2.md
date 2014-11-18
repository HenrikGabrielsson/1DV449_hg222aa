#Laboration 2 - Laborationsrapport
Skriven av: Henrik Gabrielsson (hg222aa)  
Kurs: Webbteknik II (1DV449)  
Datum: 2014-11-18

##Del 1 - Säkerhetsproblem

###Säkerhetshål 1
Det första säkerhetshålet jag hittade var att programmet inte ens kollar om man är inloggad eller inte, på grund av en bugg där PHP tolkar en sträng ("Could not find the user") som *TRUE* vilket ger vilket namn/lösenord som helst användaren åtkomst till applikationen.

Jag rättade till detta igenom att ta bort hela if-satsen som skickar tillbaka denna sträng, då resultatet istället kommer returneras ( och detta tolkas som **FALSE** om ingen användare hittas). Strängen kommer ju ändå inte visas då sidan skickar ett 401-meddelande om man har fel lösenord.
