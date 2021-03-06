var MessageBoard = {

    messages: [],
    textField: null,
    messageArea: null,
    hiddenToken: null,

    init:function(e)
    {

        MessageBoard.textField = document.getElementById("inputText");
        MessageBoard.nameField = document.getElementById("inputName");
        MessageBoard.messageArea = document.getElementById("messagearea");
        MessageBoard.hiddenToken = document.getElementById("hiddenToken");

        // Add eventhandlers
        document.getElementById("inputText").onfocus = function(e){ this.className = "focus"; }
        document.getElementById("inputText").onblur = function(e){ this.className = "blur" }
        document.getElementById("buttonSend").onclick = function(e) {MessageBoard.sendMessage(); return false;}
        document.getElementById("buttonLogout").onclick = function(e) {MessageBoard.logout(); return false;}

        MessageBoard.textField.onkeypress = function(e){
            if(!e) var e = window.event;

            if(e.keyCode == 13 && !e.shiftKey){
                MessageBoard.sendMessage();

                return false;
            }
        }
        MessageBoard.getMessages(function()
        {
            getNewMessages(MessageBoard.messages[MessageBoard.messages.length-1].getDate());
        });
    },
    getMessages:function(callback) {
        $.ajax({
            type: "GET",
            url: "functions.php",
            data: {function: "getMessages"}
        }).done(function(data) { // called when the AJAX call is ready

            data = JSON.parse(data);


            for(var mess in data) {
                var obj = data[mess];
                var text = obj.name +" said:\n" +obj.message;
                var mess = new Message(text, obj.date);
                var messageID = MessageBoard.messages.push(mess)-1;

                MessageBoard.renderMessage(messageID);

            }
            document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
            callback();
        });


    },
    sendMessage:function(){

        if(MessageBoard.textField.value == "") return;

        // Make call to ajax
        $.ajax({
            type: "GET",
            url: "functions.php",
            data: {function: "add", name: MessageBoard.nameField.value, message:MessageBoard.textField.value, token:MessageBoard.hiddenToken.value}
        }).done(function(data) {
            alert("Your message is saved! Reload the page for watching it");
        });

    },
    renderMessages: function(){
        // Remove all messages
        MessageBoard.messageArea.innerHTML = "";

        // Renders all messages.
        for(var i=0; i < MessageBoard.messages.length; ++i){
            MessageBoard.renderMessage(i);
        }

        document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
    },

    renderMessage: function(messageID){
        // Message div
        var div = document.createElement("div");
        div.className = "message";

        // Clock button
        aTag = document.createElement("a");
        aTag.href="#";
        aTag.onclick = function(){
            MessageBoard.showTime(messageID);
            return false;
        }

        var imgClock = document.createElement("img");
        imgClock.src="pic/clock.png";
        imgClock.alt="Show creation time";

        aTag.appendChild(imgClock);
        div.appendChild(aTag);

        // Message text
        var text = document.createElement("p");
        text.innerHTML = MessageBoard.messages[messageID].getHTMLText();
        div.appendChild(text);

        // Time - Should fix on server!
        var spanDate = document.createElement("span");
        spanDate.appendChild(document.createTextNode(MessageBoard.messages[messageID].getDateText()))

        div.appendChild(spanDate);

        var spanClear = document.createElement("span");
        spanClear.className = "clear";

        div.appendChild(spanClear);

        MessageBoard.messageArea.appendChild(div);
    },

    showTime: function(messageID){

        var time = MessageBoard.messages[messageID].getDate();

        var showTime = "Created "+time.toLocaleDateString()+" at "+time.toLocaleTimeString();

        alert(showTime);
    }

}


function Message(message, date){

    //från s till ms
    this.date = Number(date) * 1000;
    this.message = message;

    this.getText = function() {
        return message;
    }

    this.getDate = function() {
        return new Date(this.date);
    }


}

Message.prototype.toString = function(){
    return this.getText()+" ("+this.getDate()+")";
}

Message.prototype.getHTMLText = function() {

    return this.getText().replace(/[\n\r]/g, "<br />");
}

Message.prototype.getDateText = function() {
    return this.getDate();
}



window.onload = MessageBoard.init;