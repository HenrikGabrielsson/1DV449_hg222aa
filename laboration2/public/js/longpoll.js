var getNewMessages = function(timestamp)
{


    $.ajax("functions.php",{
        type: "GET",
        data: {function: "getNewMessages", lastTimeStamp: timestamp},
        success: recieveData,
        error: recieveData
    });
}

var recieveData =function(data)
{
    if(data === null)
    {
        //kör igen
        getNewMessages(MessageBoard.messages[MessageBoard.messages.length-1].getDate());
        return;
    }

    var messageArea = document.getElementById("messagearea");

    data = JSON.parse(data);

    for(var mess in data) {
        var obj = data[mess];
        var text = obj.name +" said:\n" +obj.message;
        var mess = new Message(text, new Date());
        var messageID = MessageBoard.messages.push(mess)-1;

        MessageBoard.renderMessage(messageID);
    }
    document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;

    //kör igen
    getNewMessages(MessageBoard.messages[MessageBoard.messages.length-1].getDate());
}