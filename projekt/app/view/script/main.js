var main = {
	init: function()
	{
		//om det är sidan med formuläret för att välja en användare så körs denna funktion.
		if(document.getElementById("forFriendForm") !== null)
		{
			main.submitFriendFormOnSelect();
		}

		if(document.getElementById("suggestions") !== null)
		{
			main.getSuggestionsForUser();
		}
	},

	getSuggestionsForUser: function()
	{
		var id = document.URL.split("id=")[1].split("&")[0];

		if(window.localStorage)
		{	
			//det finns lokal data att hämta
			if(localStorage.getItem(id))
			{				
				//kolla om datan är up-to-date och skriv då ut den.
			}
			else
			{
				main.getSuggestionsFromServer(id);

				//data hämtas från metoden ovan och sparas lokalt.
				//skriv sedan ut.
			}
		}

	},

	getSuggestionsFromServer: function(id)
	{
		//här ska ajax användas för att hämta in data från servern och returneras
		$('#suggestions').load ("ajaxHelper.php?id=" + id);
	},

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
