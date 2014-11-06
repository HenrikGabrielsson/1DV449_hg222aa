function Course(courseUrl, name, courseCode, syllabusUrl, introText, lastEntry)
{
    this.courseUrl = courseUrl;
    this.name = name;
    this.courseCode = courseCode;
    this.syllabusUrl = syllabusUrl;
    this.introText = introText;
    this.lastEntry = lastEntry;
}

module.exports = Course;