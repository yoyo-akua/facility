<?php
    ## This file is called by javascript to generate the list of autocompletion values for the corresponding input fields.

    /*
    ## Include files.
    ## DB.php initialises $link which contains credentials essential for connecting to the database.
    ## variables.php declares several variables: $today is used within this file.
    */
    include_once("../Objects/DB.php");
    include_once("variables.php");
   
    ## Initialise variable $thispage which is sent along when calling this function by jQuery ajax in autocomplete.js.
    $thispage = $_POST['thispage'];

    /*
    ## In case any of the pages with a search function which has autocompletion is opened, 
    ## initialise variables $column and $table which are used to create a database request that returns the autocompletion values.
    */
    if(strstr($thispage,"prescribe_drugs") OR strstr($thispage,"drug_report") OR strstr($thispage,"disp_drugs") OR strstr($thispage,"store_drugs")){
        $column="Drugname";
        $table="drugs";
    }else if(strstr($thispage,"patient_visit")){
        $column="DiagnosisName";
        $table="diagnoses";
    }else if(strstr($thispage,"search_patient") OR strstr($thispage,"patient_protocol")){
        $column="Name";
        $table="patient";
    }else if(strstr($thispage,"non_drugs")){
        $column="Non_Drugname";
        $table="non_drugs";
    }else if(strstr($thispage,"login.php")){
        $column="username";
        $table="staff";
    }else if(strstr($thispage,"current_patients") OR strstr($thispage,"disp_patients") OR strstr($thispage,"vital_signs") OR strstr($thispage,"lab_patients") OR strstr($thispage,"maternity_patients")){
        ## $condition is used to limit the search result further.
        if(strstr($thispage,"current_patients")){
            $condition=" WHERE patient.patient_ID=visit.patient_ID AND visit.visit_ID=protocol.visit_ID AND checkout_time like '0000-00-00 00:00:00' AND onlylab=0";
        }else if(strstr($thispage,"disp_patients")){
            $condition=",disp_drugs WHERE patient.patient_ID=visit.patient_ID  AND visit.visit_ID=protocol.visit_ID and disp_drugs.protocol_ID=protocol.protocol_ID AND onlylab=0 and checkout_time like '0000-00-00 00:00:00' AND checkin_time>(DATE_SUB('$today',INTERVAL 14 DAY)) GROUP BY protocol.protocol_ID";
        }else if(strstr($thispage,"vital_signs")){
            $condition=" WHERE patient.patient_ID=visit.patient_ID  AND visit.visit_ID=protocol.visit_ID and checkout_time like '0000-00-00 00:00:00' AND onlylab=0 AND protocol_ID NOT IN (SELECT protocol_ID FROM vital_signs) ";
        }else if(strstr($thispage,"lab_patients")){
            $condition=",lab_list WHERE patient.patient_ID=visit.patient_ID  AND visit.visit_ID=protocol.visit_ID AND lab_list.protocol_ID.protocol.protocol_ID AND lab_done=0 AND checkin_time>(DATE_SUB('$today',INTERVAL 14 DAY)) GROUP BY protocol.protocol_ID";
        }else if(strstr($thispage,"maternity_patients")){
            $condition=" WHERE patient.patient_ID=visit.patient_ID AND visit.visit_ID=protocol.visit_ID and checkout_time like '0000-00-00 00:00:00' AND onlylab=0 and Sex like 'female'";
        }
        $table="patient,protocol,visit$condition";
        $column="Name";
        
        ## $extra contains some extra information to be displayed in the autocompletion propositions which are not part of the actual search.
        $extra="OPD";
    }
        

    ## If $column and $table are set, meaning one of the pages with a search function that has autocompletion is opened, call this if-branch.
    if(isset($column) AND isset($table)){
        
        /*
        ## Create the database request to retrieve the proposed autocompletion values and turn them into an array $searchvalues.
        ## The second array $array2 is used to store some information that is to be displayed in the autocompletion propositions,
        ## but not used for the actual search.
        */
        $query="SELECT $column";
        if(isset($extra)){
            $query.=",$extra";
        }
        $query.=" FROM $table ORDER BY $column ASC";
        $result=mysqli_query($link,$query);
        
        $array=array();
        $array2=array();
        while($row=mysqli_fetch_object($result)){
            if(isset($extra)){
                $array2[]=$row->$extra;
            }else{
                $array2[]='';
            }
            $array[]=$row->$column;
        }
    }

    $arrays=array("array"=>$array,"array2"=>$array2);
    echo json_encode($arrays);

?>