<?php
    ## This file is called by javascript to generate the list of autocompletion values for the corresponding input fields.

    /*
    ## Include files.
    ## DB.php initialises $link which contains credentials essential for connecting to the database.
    ## variables.php declares several variables: $today is used within this file.
    */
    include_once("../Objects/DB.php");
    include_once("../Objects/Patient.php");
    include_once("../Objects/Protocol.php");
    include_once("variables.php");
    include_once("../Objects/Nutrition.php");

    $protocol_ID=$_POST['protocol_ID'];
    $BMI=$_POST['BMI'];

    $output=Nutrition::classify_BMI($protocol_ID,$BMI);
    
    echo json_encode($output);
?>