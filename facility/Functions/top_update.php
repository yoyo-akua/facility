<?php
	/*
	## Inquire current month and save in $month.
	## Retrieve months of last "top update" from database and save in $result.
	## Compare $month and $result and jump in if-branch in case they are different.
	## This ensures that the if-branch is only executed once a month.
	## This if-branch is used to update the list of the top five or ten drugs/diseases once a month to ensure,
	## that this really reflects the most common diseases/drugs. 
	## This top five (or ten) list is shown at the beginning of the diagnoses/drug list when selecting for a patient as a kind of "preview". 
	*/
	$month=date("n");
	$query="SELECT notice FROM departments WHERE Department like 'Consulting'";

	$result=mysqli_query($link,$query);
	$result=mysqli_fetch_object($result);
	$result=$result->notice;

	if($month!==$result){
		
		## Update the current month in the database, so that the if-branch will only be executed in the following month.
		$query="UPDATE `departments` SET `notice` = '$month' WHERE `departments`.`Department` = 'Consulting'";
		mysqli_query($link,$query);
		
		
		## Retrieve all diagnoses that were prescribed during the last month from the database.
		$query="SELECT diagnosis_ID FROM protocol,diagnosis_ids WHERE protocol.protocol_ID=diagnosis_ids.protocol_ID AND protocol.timestamp>=(DATE(NOW()) - INTERVAL 1 MONTH)";
		$result=mysqli_query($link,$query);
		
		## Create an array which will later be used to store the number of times a diagnosis has appeared.
		$disease_array=array();
		
		## This loop runs once for each patient that has come within the last month. 
		while($row=mysqli_fetch_object($result)){
			
			/*
			## Initialise a variable with the ID of the current diagnosis.
			## Then count the number of times each disease occurs and save it in $disease_array.
			*/
			$disease=$row->diagnosis_ID;
			
			if(isset($disease_array[$disease])){
				$disease_array[$disease]++;
			}else{
				$disease_array[$disease]=1;
			}
		}
			
		## Retrieve all diagnoses that were prescribed during the last month from the database.
		$query="SELECT Drug_ID FROM disp_drugs WHERE CountDate>=(DATE(NOW()) - INTERVAL 1 MONTH) AND prescription_protocol_ID!='0'";
		$result=mysqli_query($link,$query);
		
		## Create an array which will later be used to store the number of times a drug has appeared.
		$drug_array=array();
		
		## This loop runs once for each drug that has been prescribed within the last month. 
		while($row=mysqli_fetch_object($result)){
			/*
			## Initialise $drug with the Drug ID.
			## Then count the number of times each drug occurs and save it in $drug_array.
			*/
			$drug=$row->Drug_ID;
			if(isset($drug_array[$drug])){
				$drug_array[$drug]++;
			}else{
				$drug_array[$drug]=1;
			}	
		}
		
		## Sort the drugs and diagnoses descendingly by their  number of occurences. 
		arsort($drug_array);
		arsort($disease_array);
		
		## Cut the array with the number of occurences to the (five or ten) top common ones each.
		$disease_array=array_slice($disease_array,0,5,true);
		$drug_array=array_slice($drug_array,0,10,true);
		
		/*
		## Transform the array of occurences to a string.
		## Insert this string in the department list of the database.
		*/
		$drug=implode(",",array_keys($drug_array));
		$disease=implode(",",array_keys($disease_array));
		
		$query="UPDATE departments SET top='$drug' WHERE Department='Dispensary'";
		mysqli_query($link,$query);
		
		$query="UPDATE departments SET top='$disease' WHERE Department='Consulting'";
		mysqli_query($link,$query);
    }
?>