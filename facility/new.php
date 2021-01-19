<?php

    include("HTMLParts/HTML_HEAD.php");

    /*
    $query="SELECT * FROM protocol WHERE attendant NOT LIKE ''";
    $result=mysqli_query($link,$query);

    while($row=mysqli_fetch_object($result)){
        if(! Staff::getStaffByName($row->attendant)){
            $staff=Staff::new_Staff($row->attendant,'','authorised','');
        }
        $query="UPDATE protocol SET staff_ID='".$staff->getStaff_ID()."' WHERE protocol_ID=".$row->protocol_ID;
        mysqli_query($link,$query);
    }

    $query="SELECT * FROM protocol";
    $result=mysqli_query($link,$query);

    while($row=mysqli_fetch_object($result)){
        $query="INSERT INTO visit (`patient_ID`,`new_p`,`checkin_time`,`checkout_time`,`pregnant`,`protect`) VALUES ('$row->patient_ID','$row->new_p','$row->VisitDate','$row->VisitDate','$row->pregnant','$row->protect')";
        mysqli_query($link,$query);
        $query="UPDATE protocol SET visit_ID=(SELECT MAX(visit_ID) FROM visit) WHERE protocol_ID='$row->protocol_ID'";
        mysqli_query($link,$query);
    }
    
    $query="SELECT * FROM protocol";
    $result=mysqli_query($link,$query);

    while($row=mysqli_fetch_object($result)){
        if(! empty($row->entered) OR ! empty($row->Expired) OR ! empty($row->CCC)){
            $query="INSERT INTO insurance (visit_ID,entered,expired,CCC) VALUES ('$row->visit_ID','$row->entered','$row->Expired','$row->CCC')";
            mysqli_query($link,$query);
        }
    }

    $query="SELECT * FROM protocol";
    $result=mysqli_query($link,$query);

    while($row=mysqli_fetch_object($result)){
        if(! empty($row->referral)){
            $query="INSERT INTO referral (protocol_ID,destination) VALUES ('$row->protocol_ID','$row->referral')";
            mysqli_query($link,$query);
        }
    }
    
    $query="SELECT * FROM protocol";
    $result=mysqli_query($link,$query);

    while($row=mysqli_fetch_object($result)){
        if(! empty($row->lab_number)){
            $query="INSERT INTO lab_list (protocol_ID,lab_done,lab_number) VALUES ('$row->protocol_ID','$row->labdone','$row->lab_number')";
            mysqli_query($link,$query);
        }
    }

    $query="SELECT * FROM lab_list,protocol WHERE lab_list.protocol_ID=protocol.protocol_ID";
    $result=mysqli_query($link,$query);

    while($row=mysqli_fetch_object($result)){
        $query="UPDATE lab SET lab_list_ID='$row->lab_list_ID' WHERE protocol_ID='$row->protocol_ID'";
        mysqli_query($link,$query);
        $query="DELETE FROM lab WHERE lab_list_ID=0";
        mysqli_query($link,$query);
    }


    $query="SELECT * FROM lab_list,protocol,lab WHERE lab.protocol_ID=protocol.protocol_ID AND lab_list.lab_list_ID=lab.lab_list_ID";
    $result=mysqli_query($link,$query);

    while($row=mysqli_fetch_object($result)){
        $query="UPDATE lab_list SET visit_ID='$row->visit_ID' WHERE lab_list_ID='$row->lab_list_ID'";
        mysqli_query($link,$query);
    }
    

    $query="SELECT * FROM visit,patient,protocol WHERE visit.patient_ID=patient.patient_ID AND protocol.visit_ID=visit.visit_ID AND NHIS NOT LIKE ''";
    $result=mysqli_query($link,$query);

    while($row=mysqli_fetch_object($result)){
        $query = "INSERT INTO `insurance`(`visit_ID`,`CCC`,`expired`) VALUES ('$row->visit_ID','$row->CCC','$row->Expired')";
        mysqli_query($link,$query);
    }
    */

?>