function Course(courseUrl, name, courseCode, syllabusUrl, introText, lastEntry)
{
    //sätter tomma och nullvärden till "no information istället.
    if(courseUrl === null || courseUrl.length === 0)
    {
        courseUrl = "no information";
    }
    if(name === null || name.length === 0)
    {
        name = "no information";
    }
    if(courseCode === null || courseCode.length === 0)
    {
        courseCode = "no information";
    }
    if(syllabusUrl === null || syllabusUrl.length === 0)
    {
        syllabusUrl = "no information";
    }
    if(introText === null || introText.length === 0)
    {
        introText = "no information";
    }
    if(lastEntry === null || lastEntry.length === 0)
    {
        lastEntry = "no information";
    }
    
    this.courseUrl = courseUrl;
    this.name = name;
    this.courseCode = courseCode;
    this.syllabusUrl = syllabusUrl;
    this.introText = introText;
    this.lastEntry = lastEntry;
}

module.exports = Course;