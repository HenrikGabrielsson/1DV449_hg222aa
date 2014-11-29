var Traffic = function()
{
    this.sqlite = require('sqlite3');
    this.db = new this.sqlite.Database("db.db");
    
}

Traffic.prototype.getTrafficNews = function()
{
    //this.db.run("CREATE TABLE messages(id              INTEGER     PRIMARY KEY     NOT NULL,priority        INTEGER                     NOT NULL,createddate     INTEGER                     NOT NULL,title           TEXT                        NOT NULL,exactlocation   TEXT                        NOT NULL,description     TEXT                        NOT NULL,latitude        REAL                        NOT NULL,longitude       REAL                        NOT NULL,category        INTEGER                     NOT NULL,subcategory     INTEGER                     NOT NULL);");
    console.log(this.db.run("SELECT * FROM messages"));
    
}

module.exports = Traffic;