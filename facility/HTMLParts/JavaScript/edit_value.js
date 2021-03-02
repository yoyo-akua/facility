/*
## This function is called when the user is reentering the vital signs of a client. 
## It changes the display style of certain elements: 
##  - the edit button is supposed to disappear.
##  - also an input field with the value of the vital signs parameter is inserted which enables the user to edit it. 
## The components mentioned above are already within the html code calling upon this function, 
## it only changes which parts are visible.
*/

function edit_value(id,value){
    document.getElementById(id+"_div").style.display="block";
    document.getElementById(id+"_input").value=value;

    document.getElementById(id+"_text").style.display="none";
    document.getElementById("edit_"+id).style.display="none";
 }