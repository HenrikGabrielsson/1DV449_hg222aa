var getNewMessages = function(timestamp)
{
    timestamp = timestamp/1000;

    $.ajax("functions.php",{
        type: "GET",
        data: {function: "getNewMessages", lastTimeStamp: Number(timestamp)},
        success: recieveData,
        error: recieveData
    });
}

var recieveData =function(data)
{

    if(data != null)
    {
        var messageArea = document.getElementById("messagearea");

        data = JSON.parse(data);


        for(var mess in data) {
            var obj = data[mess];
            var text = obj.name +" said:\n" +obj.message;
            var mess = new Message(text, obj.date);
            var messageID = MessageBoard.messages.push(mess)-1;

            MessageBoard.renderMessage(messageID);
        }
        document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
    }


    getNewMessages(MessageBoard.messages[MessageBoard.messages.length-1].getDate());
    

    
}