function Entry(title, writer, date)
{
    if(title === null || title.length === 0)
    {
        title = "no information";
    }
    if(writer === null || writer.length === 0)
    {
        writer = "no information";
    }
    if(date === null || date.length === 0)
    {
        date = "no information";
    }
    
    
    
    this.title = title;
    this.writer = writer;
    this.date = date;
}

module.exports = Entry;