
// This function is used to subtract expired drugs and non drugs automatically from the stock.
function Expired(){
    
    // Initialise variables number and particulars with the number of rows and the value of the particulars input field at the end of the (non) drug list.
    var number=document.getElementsByName('Particulars').length;
    var particulars=document.getElementsByName('Particulars')[number-1].value;
    
    // If particulars is "Expired", call this if-branch.
    if(particulars.toUpperCase()=='EXPIRED'){
        
        //Initialise variables previous and value to get the current stock on hand and save it in value.
        var previous=document.getElementsByTagName('td').length-16;
        var value = parseInt(document.getElementsByTagName('td')[previous].innerHTML);
        
        // Enter the current stock on hand as Issued and write "Expired" in capital letters.
        document.getElementsByName('Issued')[number-1].value = value;
        document.getElementsByName('Particulars')[number-1].value = 'EXPIRED';
    }
}