/*
Automated checks to catch errors before publishing a news update:
- The <time> element's datetime attribute is set to the date of the update.
- The <time> element's text content is set to the date of the update.
- The id of the <article> is unique and follows the format 'news-YYYY-some-topic'.
- All <a> elements have a target="_blank" attribute.
- All <a> elements have an href attribute.

HTML validity checking is not performed.
*/

const newsEl = document.querySelector("#news");
const articleIDs = [];
