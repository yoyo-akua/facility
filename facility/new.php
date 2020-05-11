<?php


## Done in Wa
/*
include("setup.php");
$query="SELECT * FROM protocol";
$result=mysqli_query($link,$query);
while($row=mysqli_fetch_object($result)){
	$protocol_ID=$row->protocol_ID;
	$drugs=explode("//",$row->disp_drugs_IDs);
		foreach($drugs AS $drug){
			if(! empty($drug)){
				$drug=str_replace("/","",$drug);
				$query2="UPDATE disp_drugs SET protocol_ID='$protocol_ID' WHERE disp_drugs_ID='$drug'";
				mysqli_query($link,$query2);
			}
		}
}
$query="SELECT * FROM protocol";
$result=mysqli_query($link,$query);
while($row=mysqli_fetch_object($result)){
	$protocol_ID=$row->protocol_ID;
	$diagnoses=explode("//",$row->Diagnosis_IDs);
		foreach($diagnoses AS $diagnosis){
			$diagnosis=str_replace("/","",$diagnosis);
			if(strstr($diagnosis,'(2)')){
				$importance=2;
			}else if(strstr($diagnosis,'(1)')){
				$importance=1;
			}else{
				$importance=0;
			}
			if(! empty($diagnosis)){
				$query2="INSERT INTO `diagnosis_ids` (`protocol_ID`, `diagnosis_ID`,`importance`) VALUES ('$protocol_ID', '$diagnosis','$importance');";
				mysqli_query($link,$query2);
			}
		}
}
$query="SELECT * FROM protocol WHERE MUAC OR BP OR pulse OR temperature OR weight";
$result=mysqli_query($link,$query);
while($row=mysqli_fetch_object($result)){
	$query2="INSERT INTO `vital_signs` (`protocol_ID`,`MUAC`, `BP`,`pulse`,`temperature`,`weight`) VALUES ('$row->protocol_ID','$row->MUAC', '$row->BP','$row->pulse','$row->temperature','$row->weight');";
	mysqli_query($link,$query2);
}

*/

?>