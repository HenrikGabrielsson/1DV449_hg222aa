var main = {

	///denna funktion körs alltid när en sida hämtats.
	init: function()
	{
		//om det är sidan med formuläret för att välja en användare så körs denna funktion.
		if(document.getElementById("forFriendForm") !== null)
		{
			main.submitFriendFormOnSelect();
		}

		if(document.getElementsByClassName("forMeFormText")[0] !== undefined)
		{
			
			main.submitMeFormOnTextClick();
		}

		//om ett element ska fyllas med produkter från ebay.
		if(document.getElementById("suggestions") !== null)
		{
			main.getSuggestionsForUser();
		}
	},

	//felsidan anropar denna funktion för att hämta sidan igen när det finns anslutning till servern igen.
	refreshWhenOnline: function()
	{
		var pingLoop = setInterval(function()
		    {
		        main.pingServer(function(response)
		        {
		            if(response)
		            {
		                clearInterval(pingLoop);
		                location.reload();
		            }
		        })
		    },10000);
	},

	//hämtar ebay-produkter från servern
	getSuggestionsForUser: function()
	{
		//id på användaren som ska få förslag.
		var id = document.URL.split("id=")[1].split("&")[0];

		//loading
		main.printLoadingScreen();

		//om det finnns möjlighet till cachning på klientsidan
		if(window.localStorage)
		{
			//kollar om klienten-servern kommer åt varandra.
			main.pingServer(function(response)
			{			
				//online
				if(response)
				{
					main.getSuggestionsFromServer(id);
				}
				else
				{
					main.getSuggestionsFromCache(id);
					
				}
			});
		}
		//försöker hämta från servern.
		else
		{
			main.getSuggestionsFromServer(id);
		}
	},

	//skriv ut lokala suggestions
	getSuggestionsFromCache: function(id)
	{
		//hämtar cachade objekt.
		if(localStorage.getItem(id))
		{
			main.printCacheWarning(id);
			main.printSuggestions(id);
		}
		//finns inte
		else
		{
			main.printError();
		}
	},

	//skriver ut ett felmeddelande om det av någon anledning inte gick att visa några förslag.
	printError: function()
	{
		var suggestionsDiv = document.getElementById("suggestions");
		main.removeLoadingGif();

		var h2 = document.createElement("h2");
		var p = document.createElement("p");
		h2.appendChild(document.createTextNode("Sorry!"));
		p.appendChild(document.createTextNode("Something went wrong when getting suggestions for you. Try again later!"));

		suggestionsDiv.appendChild(h2);
		suggestionsDiv.appendChild(p);
	},

	//visas när inga items hittades
	printNoMerchandise: function()
	{
		var suggestionsDiv = document.getElementById("suggestions");
		var noMerchandise = document.createElement("p");

		noMerchandise.appendChild(document.createTextNode("No merchandise found. Try to refresh the page, or buy more games..."));

		suggestionsDiv.appendChild(noMerchandise);
	},

	//Berättar för användaren att utskrivna objekt är gamla.
	printCacheWarning: function(id)
	{
		var suggestionsDiv = document.getElementById("suggestions");
		var timeReceived = JSON.parse(window.localStorage.getItem(id)).timeReceived;

		var warning = document.createElement("p");
		warning.appendChild(document.createTextNode("Note that these suggestions are received from the cache, because of some unknown problem! Try to refresh at a later time. These products were received from Ebay at: "  + new Date(new Date(JSON.parse(window.localStorage.getItem(id)).timeReceived))))
		
		suggestionsDiv.appendChild(warning);
	},

	//visar för användaren att sidan laddar medan produkter hämtas.
	printLoadingScreen: function()
	{
		document.getElementById("suggestions").setAttribute("class", "loadingGif");
	},

	removeLoadingGif: function()
	{
		document.getElementById("suggestions").removeAttribute("class");
	},

	//kollar om det finns anslutning
	pingServer: function(callback)
	{
		$.ajax
		({
			url:"ajaxHelper.php?function=ping",
			timeout:10000,	//10 sekunder timeout
			cache:false,
			success: function(){callback(true)},
			error: function(){callback(false)},
			fail: function(){callback(false)}
		});

	},

	//skriver ut produkter
	printSuggestions: function(id)
	{
		//hämtar de cachade produkterna.
		var merchandise = JSON.parse(window.localStorage.getItem(id)).merchandise;


		//tar bort loading-gif och hämtar viktiga element.
		var suggestionsDiv = document.getElementById("suggestions");
		main.removeLoadingGif();

		var suggestionList = document.createElement("ul");
		suggestionsDiv.appendChild(suggestionList);

		//inget hittades!
		if(merchandise.length === 0)
		{
			main.printNoMerchandise();
		}
		else
		{
			//skapar ett nytt listelement för varje produkt.
			merchandise.forEach(function(item)
			{
				suggestionList.appendChild(main.createListElement(item));
			});			
		}

	},

	//skapar ett listelement för en given produkt.
	createListElement: function(item)
	{
		//skapar alla element
		var li = document.createElement("li");
		var div = document.createElement("div");
		var div_about = document.createElement("div");

		var h2 = document.createElement("h2");
		var a = document.createElement("a");
		var img = document.createElement("img");
		var p_game = document.createElement("p");
		var p_location = document.createElement("p");
		var p_country = document.createElement("p");
		var p_startTime = document.createElement("p");
		var p_endTime = document.createElement("p");


		//fyller alla element
		div.setAttribute("class", "itemDisplay");
		div_about.setAttribute("class", "item_about");
		a.appendChild(document.createTextNode(item.title));
		a.setAttribute("href", item.ebayURL);
		img.setAttribute("src", item.imageURL);

		p_game.appendChild(document.createTextNode("Game: " + item.gameTitle));
		p_game.setAttribute("class", "gameTitle");
		p_location.appendChild(document.createTextNode("Location: " + item.location));
		p_country.appendChild(document.createTextNode("Country: " + item.country));
		p_startTime.appendChild(document.createTextNode("Auction started at: " + item.startTime.date));
		p_endTime.appendChild(document.createTextNode("Auction ends at: " + item.endTime.date));		

		//sätter ihop allt och returnerar
		h2.appendChild(a);

		div.appendChild(h2);	
		div.appendChild(div_about);
		div_about.appendChild(p_game);
		div_about.appendChild(p_location);
		div_about.appendChild(p_country);
		div_about.appendChild(p_startTime);
		div_about.appendChild(p_endTime);
		
		div.appendChild(img);

		li.appendChild(div);

		return li;
	},

	//hämtar produkter från servern
	getSuggestionsFromServer: function(id)
	{
		var token = document.getElementById("token").innerHTML;

		//här ska ajax användas för att hämta in data från servern och sedan skrivas ut
		$.get("ajaxHelper.php?function=getMerchandise&token="+token+"&id=" + id, function(data)
		{
			try
			{
				//testar om hämtad data kan tolkas som json
				var jsonTest = JSON.parse(data);

				//sparar lokalt och skriver ut
				if(jsonTest !== null && jsonTest !== undefined)
				{
					localStorage.setItem(id, data);
					main.printSuggestions(id);
				}
			}
			//fel inträffade, cachad data hämtas istället.
			catch(e)
			{
				getSuggestionsFromCache(id);
			}
			
		});
	},

	//skickar formulär även när man klickar på text.
	submitMeFormOnTextClick: function()
	{
		var forMeText = document.getElementsByClassName("forMeFormText")[0];
		var forMeForm = document.getElementById("forMeForm");
		forMeText.addEventListener("click", function()
		{
			forMeForm.submit();
		}, false);
	},

	//ser till så formulär skickas så fort man väljer en person i listan.
	submitFriendFormOnSelect: function()
	{
		var friendForm = document.getElementById("forFriendForm");

		friendForm.addEventListener("change", function()
		{
			friendForm.submit();
		}, false);
	}
}

window.addEventListener("load", main.init, false);