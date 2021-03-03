
/*
## This function is called when the user uploaded a file to attach it to the report.
## Display an input field containing the current file name so that it can be changed. 
*/
function upload_name(name){
   
    document.getElementById(name+"_input").style.display = "inline";

    var value = document.getElementById(name).value;
    value = value.split("\\");
    value = value[value.length - 1];
    var filename = value.split(".");

    document.getElementById(name+"_input").value = filename[0];
    document.getElementById(name+"_type").innerHTML = '.'+filename[1];
    
}