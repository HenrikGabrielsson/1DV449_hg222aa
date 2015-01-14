#Idébeskrivning

Skrivet av: Henrik Gabrielsson (hg222aa)
Kurs: Webbteknik II (1DV449)
Datum: 2014-12-14


##Idé
Min idé är att låta användare ange sina egna Steam-konton (Steam är en tjänst där användare kan köpa spel) för att låta applikationen hämta in listan med ägda spel och leta upp *Gaming Merchandise* på EBay. Man kan också få tips på julklappar eller presenter till sina vänner, då applikationen också hämtar in listan med vänner och kollar på deras listor med spel de äger.
Spel man spelat nyligen eller ofta kommer att prioriteras och sökresultat med dessa spel kommer att ha större chans att komma med.
Man ska också kunna filtrera listan (efter spel, maxpris etc).

##API:er
+ Steam Web API
+ Ebay Finding API

##OAuth 
Jag kommer att använda Steam OpenID som, likt OAuth, låter användaren logga in med sitt Steam-konto för att se sina vänner och spel. Man ska inte i min applikation kunna se en främlings spel eller förslag till merchandise. Man ska bara se sina egna, och sina vänners.