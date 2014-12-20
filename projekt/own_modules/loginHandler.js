var LoginHandler = function()
{
    this.passport = require("passport");
    this.strategy = require("passport-steam").Strategy;
    
    this.loggedIn = false;
}

LoginHandler.prototype.isLoggedIn = function()
{
    return this.loggedIn;
}

LoginHandler.prototype.authenticate = function()
{
    this.passport.authenticate("steam", function(req,res){console.log("derp")})
}

LoginHandler.prototype.setConfiguration = function()
{
    this.passport.use(new this.strategy
        ({
            returnURL: "http://www.steamstuff.com",
            realm: "SteamStuff",
            apiKey: "4B588F85F1EE7E8E49D6B5014505A3A7"
        },
        function(identifier, profile, done)
        {
            
        })
    )
}

module.exports = LoginHandler;