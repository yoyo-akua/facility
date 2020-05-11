// When the user is scrolling on any page, call the function FixHead.
window.onscroll = function() {FixHead()};

// Initialise a variable with the arrow link button to the top of the page and make sure it is not shown when calling the page.
var toplink = document.getElementById("toplink");
toplink.style.display = "none";

// This function fixes and defixes the headline when the user is scrolling down or going back to the beginning of the page.
// It also makes the arrow link to the top of the page appear when scrolling down 200px from the top of the page.
function FixHead() {
    // Initialise variables with the headline element and the scrolling position on the page.
    var header = document.getElementById("headline");
    var sticky = header.offsetTop;
    
    // In case the user scrolled down, add the CSS class "sticky", otherwise remove it.
    if (window.pageYOffset > sticky) {
        header.classList.add("sticky");
    } else {
        header.classList.remove("sticky");
    }

    // Show the top link arrow only when the user scrolled at least 200px down from the top of the page.
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        toplink.style.display = "block";
    } else {
        toplink.style.display = "none";
    }
}