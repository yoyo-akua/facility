/*
## This function is called when the user is reentering the vital signs of a client. 
## It changes the display style of certain elements: 
##  - the edit button is supposed to disappear.
##  - also an input field with the value of the vital signs parameter is inserted which enables the user to edit it. 
## The components mentioned above are already within the html code calling upon this function, 
## it only changes which parts are visible.
*/

function edit_radio(id,number){

    document.getElementById("option_1_"+id).style.display="block";
    document.getElementById("option_1_"+id).value="issued";
    document.getElementById("readonly_1_"+id).style.display="none";

    document.getElementById("option_2_"+id).style.display="block";
    document.getElementById("option_2_"+id).value="non_issued";
    document.getElementById("readonly_2_"+id).style.display="none";

    document.getElementById("edit_"+id).style.display="none";
 }