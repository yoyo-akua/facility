// This function is used to calculate and prefill the amount of drugs from the dosage recommendation when prescribing them.
function PrefillAmounts() {
    
    // Initialise variables drug and unit with parameters that are sent when calling on the function.
    var drug=arguments[0];
    var unit=arguments[1];

    // Get the input value of the dosage recommendation fields.
    var pattern=document.getElementsByName("pattern_"+drug)[0].value;
    var days=document.getElementsByName("days_"+drug)[0].value;
    
    // Initialise variable amount which will contain the value to be put into the "amount" input field of the form.
    var amount;

    // Provided both the "pattern" field and the "number of days" field are filled and the drug is not served in bottles, call this if-branch.
    if(pattern!=='' && days!=="0" && days.length!==0 && unit!=='bottle'){
        
        // Transform the abbreviations of the patterns into a numeric format.
        if(pattern=='od' || pattern=='stat' || pattern=='noct' || pattern=='prn'){
            pattern=1;
        }else if(pattern=='bd'){
            pattern=2;
        }else if(pattern=='tds'){
            pattern=3;
        }else if(pattern=='qid'){
            pattern=4;
        }
        
        amount=pattern*days;

    }
    // In case the drug is served in bottles, set amount to one.
    else if(unit=='bottle'){
        amount=1;
    }
    // If any of the fields of dosage recommendation is empty, leave amount empty too.
    else{
        amount='';
    }

    // Use amount to prefill the "amount" input field of the table.
    document.getElementsByName("amount_"+drug)[0].value= amount;
}