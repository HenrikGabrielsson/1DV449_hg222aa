var main = {

	///denna funktion körs alltid när en sida hämtats.
	init: function()
	{
		//om det är sidan med formuläret för att välja en användare så körs denna funktion.
		if(document.getElementById("forFriendForm") !== null)
		{
			main.submitFriendFormOnSelect();
		}

		//om ett element ska fyllas med produkter från ebay.
		if(document.getElementById("suggestions") !== null)
		{
			main.getSuggestionsForUser();
		}
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
					//hämtar cachade objekt.
					if(localStorage.getItem(id))
					{
						main.printSuggestions(id);
					}
					//finns inte
					else
					{
						console.log("error");
					}
					
				}
			});
		}
		//försöker hämta från servern.
		else
		{
			main.getSuggestionsFromServer(id);
		}
	},

	//visar för användaren att sidan laddar medan produkter hämtas.
	printLoadingScreen: function()
	{
		var suggestionsDiv = document.getElementById("suggestions");
		var loadingGif = document.createElement("img");
		loadingGif.setAttribute("id", "loadingGif");
		loadingGif.setAttribute("src", "view/img/loader.gif");

		suggestionsDiv.appendChild(loadingGif);
	},

	//kollar om det finns anslutning
	pingServer: function(callback)
	{
		var ajaxGetter = $.get("ajaxHelper.php?function=ping")
		.done(function(){callback(true)})
		.fail(function(){callback(false)});		
	},

	//skriver ut produkter
	printSuggestions: function(id)
	{
		//hämtar de cachade produkterna.
		var merchandise = JSON.parse(window.localStorage.getItem(id)).merchandise;

		//tar bort loading-gif och hämtar viktiga element.
		var suggestionsDiv = document.getElementById("suggestions");
		suggestionsDiv.removeChild(document.getElementById("loadingGif"));
		var suggestionList = document.createElement("ul");
		suggestionsDiv.appendChild(suggestionList);

		//skapar ett nytt listelement för varje produkt.
		merchandise.forEach(function(item)
		{
			suggestionList.appendChild(main.createListElement(item));
		});
	},

	//skapar ett listelement för en given produkt.
	createListElement: function(item)
	{
		//skapar alla element
		var li = document.createElement("li");
		var div = document.createElement("div");

		var h2 = document.createElement("h2");
		var a = document.createElement("a");
		var img = document.createElement("img");
		var p = document.createElement("p");

		var dl = document.createElement("dl");
		var dt_location = document.createElement("dt");
		var dd_location = document.createElement("dd");
		var dt_country = document.createElement("dt");
		var dd_country = document.createElement("dd");
		var dt_startTime = document.createElement("dt");
		var dd_startTime = document.createElement("dd");
		var dt_endTime = document.createElement("dt");
		var dd_endTime = document.createElement("dd");


		//fyller alla element
		div.setAttribute("class", "itemDisplay");
		a.appendChild(document.createTextNode(item.title));
		a.setAttribute("href", item.ebayURL);
		img.setAttribute("src", item.imageURL);
		p.appendChild(document.createTextNode("Game: " + item.gameTitle));
		dt_location.appendChild(document.createTextNode("Location: "));
		dd_location.appendChild(document.createTextNode(item.location));
		dt_country.appendChild(document.createTextNode("Country: "));
		dd_country.appendChild(document.createTextNode(item.country));
		dt_startTime.appendChild(document.createTextNode("Auction started at: "));
		dd_startTime.appendChild(document.createTextNode(item.startTime.date));
		dt_endTime.appendChild(document.createTextNode("Auction ends at: "));		
		dd_endTime.appendChild(document.createTextNode(item.endTime.date));


		//sätter ihop allt och returnerar
		dl.appendChild(dt_location);
		dl.appendChild(dd_location);
		dl.appendChild(dt_country);
		dl.appendChild(dd_country);
		dl.appendChild(dt_startTime);
		dl.appendChild(dd_startTime);
		dl.appendChild(dt_endTime);
		dl.appendChild(dd_endTime);

		h2.appendChild(a);

		div.appendChild(h2);
		div.appendChild(img);
		div.appendChild(p);
		div.appendChild(dl);
		li.appendChild(div);

		return li;
	},

	//hämtar produkter från servern
	getSuggestionsFromServer: function(id)
	{
		//här ska ajax användas för att hämta in data från servern och sedan skrivas ut
		$.get("ajaxHelper.php?function=getMerchandise&id=" + id, function(data)
		{
			localStorage.setItem(id, data);
			main.printSuggestions(id);
		});
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