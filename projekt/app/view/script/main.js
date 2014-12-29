var main = {
	init:function()
	{
		if(document.getElementById("forFriendSubmit") !== null)
		{
			main.submitFriendFormOnSelect();
		}
		
	},

	submitFriendFormOnSelect:function()
	{
		var submit = document.getElementById("forFriendSubmit");
		var friendForm = document.getElementById("forFriendForm");

		friendForm.addEventListener("change", function()
		{
			friendForm.submit();
		}, false);
		submit.parentNode.removeChild(submit);

	}


}

window.addEventListener("load", main.init, false);