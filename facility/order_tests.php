<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of patient by a certain patient-ID, with which the page is .
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	## Initialise new object of visit by a certain visit ID, with which the page is .
	$visit_ID=$_GET['visit_ID'];
	$visit=new Visit($visit_ID);


	/*
	## Initialise variable $added which is used to differentiate in the notification sent to lab, 
	## whether tests have been ordered for the patient for the first time.
	*/
	$added=false;

	/*
	## Get data from database.
	## Get all tests, which are already ordered for the patient.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$test_query="SELECT * FROM lab,parameters,protocol WHERE visit_ID='$visit_ID' AND lab.protocol_ID=protocol.protocol_ID AND lab.parameter_ID=parameters.parameter_ID"; 
	$test_result=mysqli_query($link,$test_query);
	$last_tests=array();

	## Save all previously ordered tests in the array $last_tests.
	while($test_row=mysqli_fetch_object($test_result)){
		$test_ID=$test_row->test_ID;
		if(! in_array($test_ID,$last_tests)){
			$last_tests[]=$test_ID;
		}
		## Set $added true to indicate in the Lab notification that tests have been added (not ordered initially) for the patient.
		$added=true;
	}

	## This if-branch is called if the user clicked on submit after selecting tests.
	if(! empty($_GET['submit'])){

		## Initialise variable $delete_array as array which is used to find out, if any tests, that have been previously ordered are deselected now.
		$delete_array=array();

		/*
		## Initialise variables $ordered and $deleted whicch are used to determine whether the user is deleting or ordering any tests.
		## This information is then used to send push notifications to lab to inform them about orders. 
		*/
		$ordered=false;
		$deleted=false;

		/*
		## Get data from database.
		## Get a lists of all tests and their parameters.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/	
		$query="SELECT * FROM parameters,tests WHERE parameters.test_ID=tests.test_ID";
		$result=mysqli_query($link,$query);

		## The following loop will be run once for each of the output tests and their parameters from the database query.
		while($row=mysqli_fetch_object($result)){
			
			## Initialise variables with IDs of tests and parameters.
			$test_ID=$row->test_ID;
			$parameter_ID=$row->parameter_ID;

			/*
			## This if-branch is called if the test is selected for the patient.
			## Check, if the test has already been ordered before,
			## use this parameter ID to create a new entry in the lab register for the patient to order this test, if not.
			*/
			if(! empty($_GET["test_$test_ID"])){
				if(! in_array($test_ID,$last_tests)){
					
					/*
					## Find out if the patient already has a lab number.
					## If not, create a new lab number.
					## Update the patient's visit's data with this lab number 
					## and replace the previous lab number in the "departments" table of the database so that the next lab number can be calculated correctly.
					*/	
					if(empty($visit->getLab_number())){
						$lab_number=Settings::new_number('Laboratory','');
						Settings::set_new_number('Laboratory',$lab_number,'');
						$protocol=Protocol::new_Protocol($visit_ID,'Tests ordered');
						$protocol_ID=$protocol->getProtocol_ID();
						$lab_list=Lab_List::new_Lab_List($lab_number);
						$lab_list_ID=$lab_list->getLab_List_ID();
					}else if(! isset($protocol_ID)){
						$protocol=Protocol::new_Protocol($visit_ID,'Tests added');
						$protocol_ID=$protocol->getProtocol_ID();
						
						$query="SELECT lab_list_ID FROM lab,protocol WHERE visit_ID=$visit_ID AND protocol.protocol_ID=lab.protocol_ID";
						$result2=mysqli_query($link,$query);
						$object=mysqli_fetch_object($result2);
						$lab_list_ID=$object->lab_list_ID;
					}
					
					Lab::new_Lab($protocol_ID,$parameter_ID,$lab_list_ID);

					## Set $ordered true in case any tests have been ordered for the patient.
					$ordered=true;
				}
			}
			
			/*
			## This if-branch is called if the test was not selected.
			## Find out, if the test had been ordered previously.
			## If so, add the test to $delete_array.
			*/
			else{
				if(in_array($test_ID,$last_tests)){
					$test_name=$row->test_name;
					
					## Add the not selected test to $delete_array as first entry, if no other deleted test was found before. 
					if(! isset($delete_array[$test_name])){
						$delete_array[$test_name]=array($parameter_ID);
					
					## Add the not selected test to $delete_array, if other such deleted tests were found before.
					}else{
						$delete_array[$test_name][]=$parameter_ID;
					}
				}
			}
		}
		
		## This if-branch is called, if indeed previously ordered tests should no longer be ordered.
		if(! empty($delete_array)){
			
			## $first helps avoiding a comma at the beginning of the enumeration of tests to delete.
			$first=true;

			## Create an enumeration of all tests that are to be deleted in $display.
			foreach($delete_array AS $test_name=>$delete){
				if($first){
					$display=$test_name;
					$first=false;
				}else{
					$display.=', '.$test_name;
				}
			}
			
			/*
			## If the user didn't confirm that yet, use $display to have the user confirm its intention to delete the test for the patient.
			## If so, add the parameter "delete=on" to the url, which indicates the confirmation.
			*/
			if(empty($_GET['delete'])){
				echo"
					<script type='text/JavaScript'>;
						if(window.confirm('Are you sure you want to delete the following tests: $display?')){
							window.location.href='$thispage&delete=on';
						}
					</script>";

			## If the user confirmed its intention, deleting the tests from the lab register in the database.
			}else{
				foreach($delete_array AS $test_array){
					foreach($test_array AS $parameter_ID){
						$query="DELETE FROM lab WHERE protocol_ID IN (SELECT protocol_ID FROM protocol WHERE visit_ID='$visit_ID') AND parameter_ID=$parameter_ID";
						mysqli_query($link,$query);
					}
				}
				## Set $deleted true in case any tests have been deleted for the patient.
				$deleted=true;
			}
		}

		/*
		## Call this if-branch in case any tests have been added or deleted for the patient.
		## Depending on any previous tests and changes send a message text to Lab.
		## The new_Notification function is used to store this message in the database.
		*/
		if($deleted OR $ordered){
			$lab_patient=$patient->getName().'('.$patient->getOPD().')';
			if($deleted AND $ordered){
				$title="Tests changed";
				$text="Tests have been changed for $lab_patient";
			}else if($deleted){
				$title="Tests changed";
				$text="Tests have been deleted for $lab_patient";
			}else if($ordered AND $added){
				$title="Tests changed";
				$text="Tests have been added for $lab_patient";
			}else if($ordered){
				$title="New client";
				$text="Tests have been ordered for $lab_patient";
			}
			Push::new_Notification($title,$text,date("Y-m-d H:i:s",time()),"Laboratory");
		}
		
		## Automatically lead to lab list, if the patient is a self-paying lab client, otherwise lead to current patient list.
		if($visit->getOnlylab()==1){
			echo'<script type="text/JavaScript">;
						window.location.href="lab_patients.php";
					</script>';
		}else{
			echo'<script type="text/JavaScript">;
						window.location.href="current_patients.php";
					</script>';
		}
		
	}
	
	## This if-branch is called, when the user is calling the page, before clicking submit.
	else{
		
		/*
		## Initialise variable with name and sex of the patient.
		## The available tests are printed depending on the patient's sex.
		*/
		$name=$patient->getName();
		$sex=$patient->getSex();

		
		## Print a headline, a styling element for border spacing and the beginning of the form.
		echo"
				<h1>Tests on $name</h1>
					<form action='order_tests.php' method='get'>
				";

		/*
		## Get data from database.
		## Get a lists of all tests, which are available for the sex of the patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/	
		$query="SELECT * FROM tests WHERE (sex_limit='$sex' OR sex_limit='') ORDER BY frequency,test_name";
		$result=mysqli_query($link,$query);

		## $number contains the number of possible tests divided by 2, to put half of the selectable items in the right column.
		$number=mysqli_num_rows($result)/2;

		## $run is used to count the runs of the loop, to enable the splitting in left and right column.
		$run=0;

		## The following loop will be run once for each of the output tests from the database query.	
		while($row=mysqli_fetch_object($result)){
			
			## Divide the page into 2 div boxes: display half of the tests on the left side, half on the right.
			if($run==0){
				echo "<div class='columnleft'>";
			}else if($run==($number+1)){
				echo "</div><div class='columnright'>";
			}
			$run++;

			## Initialise variable with name and ID of test.
			$test_ID=$row->test_ID;
			$test_name=$row->test_name;
			
			## Print list of tests with checkboxes, the previously selected ones are checked and displayed in grey.
			if(in_array($test_ID,$last_tests)){
				echo"<input type='checkbox' name='test_$test_ID' checked='checked'> <font style='color:grey'>$test_name</font><br>";
			}else{
				echo"<input type='checkbox' name='test_$test_ID'> $test_name<br>";
			}
		}
		
		/*
		## Close the form and send $uniqueID which is defined in variables.php which is included by HTML_HEAD.php.
		## It is used to prevent double entries.
		*/
		echo"		
						<input type='hidden' name='visit_ID' value='$visit_ID'>
						<input type='hidden' name='patient_ID' value='$patient_ID'>
						<br><input type='submit' name='submit' value='submit'>
					</form>
				</div>
				";
	}

	## Contains HTML/CSS structure, which styles the graphical user interface in the browser
	include("HTMLParts/HTML_BOTTOM.php");

?>