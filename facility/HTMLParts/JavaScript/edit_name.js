/*
## This function is called when the user is editing the name of a diagnosis, drug or test. 
## It changes the display style of certain elements: 
##  - the edit button is supposed to disappearm, while a save button comes in its place.
##  - also an input field with the name is inserted which enables the user to edit it. 
## The components mentioned above are already within the html code calling upon this function, 
## it only changes which parts are visible.
*/

function edit_name(id){
    document.getElementById("edit_button"+id).style.display="none";
    document.getElementById("save_button"+id).style.display="block";
    document.getElementById("name"+id).style.display="none";
    document.getElementById("edit"+id).style.display="block";

    /*
    ## If the user is editing a drug also enable the selection of a different unit of issue.
    */
    if (id.includes('drug')){
        document.getElementById("unit"+id).style.display="none";
        document.getElementById("unit_select"+id).style.display="block";
    }
 }