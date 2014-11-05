var ns = require('node-static'); //för att serva filer till klienten
var app = require('http').createServer(handler); 

app.listen(8080); //lyssna genom denna port.


//Game/Client är mappen där alla publika filer ligger
var fileServer = new ns.Server('./1DV449_hg222aa/client', {cache: 10}); 


//När en klient ansluter körs denna funktion.
function handler (req, res) {
    req.addListener('end', function () {
        fileServer.serve(req, res); //skicka filer

    }).resume();
}