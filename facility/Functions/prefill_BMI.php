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

    $patient_ID = $_POST['patient_ID'];
    $protocol_ID=$_POST['protocol_ID'];

    $BMI=$_POST['BMI'];

    $patient=new Patient($patient_ID);
    $protocol=new Protocol($protocol_ID);

    $sex=$patient->getSex();

    $timestamp=strtotime($protocol->getVisitDate());
    $birthdate=strtotime($patient->getBirthdate());
    $age=floor(($timestamp-$birthdate)/(3600*24*7));
    if($age<=13){
        $query="SELECT * FROM BMI WHERE sex like '$sex' AND `in`='weeks' AND age='$age'";
        $result=mysqli_query($link,$query);
        $SDs=mysqli_fetch_object($result);
    }else{
        $age=floor(($timestamp-$birthdate)/(3600*24*30.4375));
        if($age<=228){
            $query="SELECT * FROM BMI WHERE sex like '$sex' AND `in`='months' AND age='$age'";
            $result=mysqli_query($link,$query);
            $SDs=mysqli_fetch_object($result);
        }else{
            $query="SELECT * FROM BMI WHERE sex like ''";
            $result=mysqli_query($link,$query);
            $SDs=mysqli_fetch_object($result);
        }
    }
    $last=0;
    foreach($SDs AS $key=>$SD){
        if((strstr($key,'SD') OR strstr($key,'Median')) AND $SD!=0){
            if($BMI>=$last AND $BMI<$SD){
                $class=$key;
            }
            $last=$SD;
        }
    }
    if(! isset($class)){
        $class=">+3SD";
    }
    if($class=='-3SD'){
        $output='severe underweight';
    }else if($class=='-2SD'){
        $output='underweight';
    }else if($class=='-1SD' OR $class=='Median' OR $class=='+1SD'){
        $output='normal weight';
    }else if($class=='+2SD'){
        $output='overweight';
    }else if($class=='+3SD'){
        $output='obesity';
    }else{
        $output='severe obesity';
    }
    echo json_encode($output);
?>