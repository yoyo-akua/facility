/*
## This function is called when the user is selecting a patient as referred.
## It unfolds further input fields for the destination and reason for the referral. 
*/

function unfold(){
    var item=document.getElementById("unfold_item");
   if(item.checked){
        document.getElementById("unfold_content").style.display='block';
   }else{
        document.getElementById("unfold_content").style.display='none';
   }
 }