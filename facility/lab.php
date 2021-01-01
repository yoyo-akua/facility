<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");
	
	## Initialise new objects of patient, visit and lab list by certain IDs, with which the page is called.
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	$visit_ID=$_GET['visit_ID'];
	$visit=new Visit($visit_ID);

	$lab_list=new Lab_List($visit_ID);
	$lab_list_ID=$lab_list->getLab_List_ID();


	## Initialise variable with patient's name.
	$name=$patient->getName();


	## This if-branch is called, if the user submitted the test results.
	if(! empty($_POST['submit'])){
		
		/*
		## Check, if the user has documented at least one test result.
		## If so, the system creates a protocol entry.
		*/		
		If (implode($_POST) !== "submit"){

			## The protocoled event differs depending on whether the tests are completed or not.
			If (! empty($_POST['labdone'])){
				$Protocol_ID_results=Protocol::new_Protocol($visit_ID, "test results submitted - tests completed");
			}
			else{
				$Protocol_ID_results=Protocol::new_Protocol($visit_ID, "test results submitted - tests incomplete");
			}
		}	

		## $allset is used to determine, if some tests are not dealt with yet.
		## TODO: Wird diese Variable noch gebraucht, wenn es doch mittlerweile $_POST['labdone'] gibt?
		$allset=true;

		/*
		## Get data from database.
		## Get a list with all the tests (or rather their parameters) which are ordered for the patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query="SELECT * FROM lab WHERE lab_list_ID=$lab_list_ID";
		$result=mysqli_query($link,$query);
		
		## The following loop will be run once for each of the parameters of the ordered tests for the patient.
		while($row=mysqli_fetch_object($result)){
			
			/*
			## Initialise variables with ID of parameter and lab entry.
			## Create new object of Lab and Parameter by these IDs.
			## Object lab contains the patient's result for the particular test parameter.
			## Object parameter contains general data that apply for the parameter whenever it is called (example: Unit of the test result).
			*/
			$lab_ID=$row->lab_ID;
			$lab=new Lab($lab_ID);
			
			$parameter_ID=$row->parameter_ID;
			$parameter=new Parameters($parameter_ID);
			
			## Determine the current test's name.
			$test_name=(new Tests($parameter->gettest_ID()))->getTest_name();
			
			## If the test is for blood group, also write the result in the list of patient's general data (database table "patient").
			if($test_name=='Blood Group' AND ! empty($_POST["parameter_$parameter_ID"])){
				$patient->setblood_group($_POST["parameter_$parameter_ID"]);
			}
			
			/*
			## Determine input type for the parameter.
			## This is necessary because (only) the input type checkbox enables a selection of multiple results.
			*/
			$type=$parameter->getInput_type();
			
			## This if-branch is called, if the input type is checkbox.
			if($type=='checkbox'){
				/*
				## The following loop creates an enumeration of all entered checkboxes for a parameter, separated by "&".
				## $first is used to avoid "&" at the beginning of the list.
				*/
				$first=true;
				for($j=1;$j<=10;$j++){
					if(! empty($_POST["parameter_$parameter_ID($j)"])){
						if($first){
							$checkboxes=' '.$_POST["parameter_$parameter_ID($j)"].' ';
							$first=false;
						}else{
							$checkboxes.='& '.$_POST["parameter_$parameter_ID($j)"].' ';
						}
					}
				}

				## If there have really checkboxes been entered, write them to database.
				if(! empty($checkboxes)){
					$lab->setTest_results($checkboxes);

				}
			}
			
			## This if-branch is used to write results with any other input type into the database.
			else{
				if(! empty($_POST["parameter_$parameter_ID"])){
					$lab->setTest_results($_POST["parameter_$parameter_ID"]);
				}
			}
			
			## TODO: Bezahlung soll später ganz anders werden. Dann hier mit bearbeiten. Bis dahin auskommentiert.
			## If the patient is paying for tests, enter that in the lab list.
			/*
			if(! empty($_POST['charge_checkbox']) AND ! empty($_POST['charge_number'])){
				$protocol->setCharge($_POST['charge_number']);
			}else if(empty($_POST['charge_checkbox']) AND empty($_POST['charge_number'])){
				$protocol->setCharge('0.00');
			}
			*/
			
			/*
			## Write in database lab table a reference to the protocol entry, 
			## which represents the submitted test results
			*/
			$lab->setProtocol_ID_results($Protocol_ID_results);


			## If the test was performed in a different facility, indicate that in the lab list
			if(! empty($_POST["other_facility_$test_name"])){
				$lab->setOther_facility(1);
			}else{
				$lab->setOther_facility(0);
			}
			
			## If the patient has only come for lab, not for OPD, indicate that in the protocol.
			if(empty($_POST['onlylab'])){
				$visit->setOnlylab(0);
			}
			
			## $allset is set false if at least one parameter is not set.
			if(empty($lab->getTest_results())){
				$allset=false;
			}
		}

		
		## This if-branch is opened, if the user uploaded a file to be attached to the report.
		if(!empty($_FILES["file"]["name"])) {
			
			/*
			## Define variables
			## 		- $targetDir contains the file upload path
			##		- $temp is used to buffer the original file name in an array
			##		- $newfilename uses the patient's name, the filename entered by the user 
			## 		  and the filetype to create the new name with which it will be saved 
			##		- $targetFilePath compiles $targetDir and $newfilename to the complete directory for saving
			##		- $allowTypes defines which file formats are allowed for the uploaded file
			*/
			$targetDir = "./uploads/";
			
			$temp = explode(".", $_FILES["file"]["name"]);
			$newfilename = $name. '_'.$_POST['filename'].'.'.end($temp);

			$targetFilePath = $targetDir . $newfilename;
			
			$fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

			$allowTypes = array('jpg','png','jpeg','pdf');

			

			## Check, if the uploaded file has an allowed file format.
			if(in_array($fileType, $allowTypes)){
				
				/*
				## Upload file to server.
				## Write information about upload to database.
				## Else give the user a message of failure.
				*/
				if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){
					$message = "The file ".$newfilename. " has been uploaded.";
					$protocol_ID = Protocol::new_Protocol($visit_ID, "file ".$newfilename. " uploaded.");
					$department = Departments::getDepartmentID("Laboratory");
					Uploads::new_Uploads($protocol_ID,$newfilename,$department);
				}else{
					$message = "Sorry, there was an error uploading your file.";
				}
			}else{
				$message = 'Sorry, only JPG, JPEG, PNG & PDF files are allowed to upload.';
			}
			## Display status message, containing information whether the file had the wrong format or the upload failed/succeeded.
			Settings::messagebox($message);
		}

		

		/*
		## If the user set the lab tests as completed, indicate that in the protocol.
		## Also send a message to Consulting that test's have been completed
		## The new_Notification function is used to store this message in the database.
		*/
		if(! empty($_POST['labdone'])){
			$lab_list->setLab_done(1);

			$title="Lab results ready";
			$notif_patient=$name."\'s (".$patient->getOPD().")";
			$text="$notif_patient lab results can be examined now.";
			Push::new_Notification($title,$text,date("Y-m-d H:i:s",time()),"Consulting");
		}else{
			$lab_list->setLab_done(0);
		}
		
		## Print a headline and the patient's test results (called by function Lab::display_results()).
		echo "
				<h1>Results of $name</h1>
				<div class='inputform'>
				".Lab::display_results($visit->getLab_number(),'tooltips on')."
				</div>
				";
		
		## If at least one parameter hasn't been entered yet, print a link to continue the entries of the results.
		if(! $allset){
			echo"<a href='lab.php?visit_ID=$visit_ID&patient_ID=$patient_ID'><div class='box'>continue entering this patient's test results</div></a>";
		}
		
		## Print links to reset the results, add further tests or go back to the list of patients in lab.
		echo"
				<a href='lab.php?visit_ID=$visit_ID&patient_ID=$patient_ID&reset=on'><div class='box'>edit this patient's test results</div></a>
				<a href='order_tests.php?visit_ID=$visit_ID&patient_ID=$patient_ID'><div class='box'>add tests</div></a>
				<a href='lab_patients.php'><div class='box'>back to all lab patients</div></a>
				";
	}
	
	/*
	## This if-branch is called, when the user calls the page from an external link and hasn't clicked "submit" yet.
	## Print the input form for test results.
	*/
	else{
		
		/*
		## Get data from database.
		## Get a list with all the tests (or rather their parameters) which are performed on the patient.
		## If the user hasn't decided to reset the test results, only tests of which the results haven't been entered yet are displayed.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		## $row is used to check, if there are any tests, that haven't been entered yet. If not, print links to edit or add tests.
		*/

		$query="SELECT * FROM lab,parameters WHERE lab.lab_list_ID = $lab_list_ID AND parameters.parameter_ID=lab.parameter_ID";

		## TODO: nächste Zeile löschen. Ist alt, wurde ersetzt durch Zeile über diesem Kommentar
		#$query="SELECT * FROM lab,parameters WHERE lab.protocol_ID=$protocol_ID AND parameters.parameter_ID=lab.parameter_ID ";
		/*
		## This if-brach is always called when the user did not click on the reset button.
		## All tests, which has an already documented result are left out. 
		*/
		if(empty($_GET['reset'])){
			$query.=" AND lab.test_results=''";
		}

		/*
		## Result list is firstly grouped by the type of an test,
		## And secondly by its parameters.
		*/
		$query.=" ORDER BY parameters.test_ID,parameters.parameter_ID";
		$result=mysqli_query($link,$query);
		$row=mysqli_fetch_object($result);
		
		/*
		If no tests of which the results haven't been entered yet exist,
		Print two Buttons instead.
		*/
		if(empty($row)){
			echo"
					<h1>This patient's results have been entered completely.</h1>
					<a href='lab.php?visit_ID=$visit_ID&patient_ID=$patient_ID&reset=on'><div class='box'>Reset Test Results</div></a>
					<a href='order_tests.php?visit_ID=$visit_ID&patient_ID=$patient_ID'><div class='box'>add tests</div></a>
					";
		}
		
		## This if-branch is called, if not all of the tests have been entered.
		else{
			
			/*
			## Initialise variables. 
			## $previous_test is used to determine, whenever a parameter belongs to a new test, so that the test can be displayed as headline.
			## $first is used to avoid printing a checkbox for tests which were performed in a different facility at the very beginning of the page.
			## $result contains a list with all the tests (or rather their parameters) which are performed on the patient from the database.
			*/
			$lab_number=$visit->getLab_number();
			$previous_test="";
			$first=true;
			$result=mysqli_query($link,$query);
			
			## Print headline, lab number and the beginning of the form.
			echo"	<h1>Tests on $name</h1>
					<h1>lab ID: $lab_number</h1>
					<form action='lab.php?patient_ID=$patient_ID&visit_ID=$visit_ID' method='post' enctype='multipart/form-data'>
					";
			
			## The following loop will be run once for each of the parameters of the ordered tests for the patient.
			while($row=mysqli_fetch_object($result)){
				
				/*
				## Initialise object of parameter and test, using the entry's parameter ID and the parameter's test ID.
				## Tests are groups of parameters that belong together.
				*/
				$parameter_ID=$row->parameter_ID;
				$parameter= new Parameters($parameter_ID);
				
				$test= new Tests($parameter->getTest_ID());
				
				## Initialise variables with the names of the test and the parameter, input type, possible outcomes, unit and previously entered results.
				$test_name=$test->getTest_name();
				$parameter_name=$parameter->getParameter_name();
				$input_type=$parameter->getInput_type();
				$test_outcomes=$parameter->getTest_outcomes();
				$unit=$parameter->getUnits();
				$test_results=$row->test_results;

				/*
				## This if-branch is called, if a new test begins.
				## It is used to print a checkbox for outsourced tests at the end of every test.
				## It also prints the headlines for the tests at the beginning of any new test.
				*/
				if($test_name!==$previous_test){
					if(! $first){
						echo"
								<br><br>
								<input type='checkbox' name='other_facility_$previous_test'";
								if($other_facility==1){
									echo "checked='checked' ";
								}
								echo"> <text style='color:grey'>test performed in different lab</text>
								<br></details>";

					}else{
						$first=false;
					}
					echo"
							<details><summary><h2><div>$test_name</h2></summary>
							";

				}
				
				## If the parameter has a name, print it.
				if($parameter_name!==''){
					echo"	
							<h3>$parameter_name</h3>
							";
				}

				/*
				## Depending on the input type of the parameter and the possible outcomes, print input fields for the results.
				## If there have been entered results before, prefill the input field with those.
				*/
				if($input_type=='radio'){
					$radio_arr=explode(',',$test_outcomes);
					foreach($radio_arr AS $radio){
						echo"
							<input type='$input_type' name='parameter_$parameter_ID' value='$radio'";
						if($test_results==$radio){
							echo"
							checked='checked'
							";
						}
						echo"
							> $radio
							";
					}
				}else if($input_type=='number'){
					$range_arr=explode('-',$test_outcomes);
					$min=$range_arr[0];
					$max=$range_arr[1];
					$step=Lab::getStep($min);
					
					echo"
							<input type='number' name='parameter_$parameter_ID' min='$min' max='$max' step='$step' ";
						if($test_results!==''){
							echo"
							value='$test_results'
							";
						}
						echo"
							> $unit
							";
				}else if($input_type=='select'){
					$option_arr=explode(',',$test_outcomes);
					echo"
							<blockquote><select name='parameter_$parameter_ID'>
								<option value=''></option>
							";
					foreach($option_arr AS $option){
						echo"
								<option value='$option' ";
						if($test_results==$option){
							echo"
							selected
							";
						}
						echo"
							>$option</option>
								";
					}
					echo"
							</select> $unit </blockquote>
							";
				}else if($input_type=='checkbox'){
					$checkbox_arr=explode(',',$test_outcomes);
					$j=0;
					foreach($checkbox_arr AS $checkbox){
						$j++;
						echo"
							<input type='checkbox' name='parameter_$parameter_ID($j)' value='$checkbox' ";
						if(strstr($test_results,' '.$checkbox.' ')){
							echo"
							checked='checked'
							";
						}
						echo"
							> $checkbox
							";
					}
				}else if($input_type=='text'){
					echo"
							<input type name='parameter_$parameter_ID' ";
						if(! empty ($test_results)){
							echo"
							value='$test_results'
							";
						}
						echo"
							>
							";
				}else if($input_type=='textarea'){
					echo"
							<blockquote><textarea name='parameter_$parameter_ID' maxlength='$test_outcomes' ";
						if(! empty ($test_results)){
							echo"
							$test_results
							";
						}
						echo"
							></textarea></blockquote>
							";
				}
				
				/*
				## Initialise variables for the next run of the loop.
				## Because the ending  of a test is only known after the loop for this test is run,
				## the checkox for outsourced tests is printed in the following run.
				## Thats's why these variables are initialised at the end of this loop.
				## Because of variable $first no checkbox is printed for the first run of the loop.
				*/
				$previous_test=$test_name;
				$other_facility=$row->other_facility;
			}

			

			## Print last checkbox for outsourced tests for the last run of the loop.
			echo"
					<br><br>
					<input type='checkbox' name='other_facility_$test_name'";
					if($other_facility==1){
						echo "checked='checked' ";
					}
					echo'> <text style="color:grey">test performed in different lab</text>
					<br></div></details>';
			
			## Define variable $upload which is used to create the IDs of the elements used for the upload.
			$upload='lab_upload';

			/*
			## Within a box in the upper right corner display an upload button.
			## In case the user selects a file for upload call the javascript function upload_name(),
			## which is used to display an input field for the file name.
			*/
			echo'
					<div class="tableright">
						
							<div class="tooltip">
								<input type="file" id="'.$upload.'" name="file" onChange="upload_name(\''.$upload.'\')">
								<label for="'.$upload.'">
									<i class="fas fa-file-upload fa-2x" id="submitbutton"></i>
								</label>
								<span class="tooltiptext" style="line-height:normal">
									upload file to<br> 
									attach to report
								</span>
							</div>
							<br><input type="text" name="filename" id="'.$upload.'_input" style="display:none;" maxlength="100"><font id="'.$upload.'_type"></font>
							
						
					<br>';

						/*
						## TODO: Bezahlung soll ganz anders werden. Muss dann hier wieder mit berücksichtigt werden.
						## If the patient has only come for lab, the user can change that here.
						## If the patient was only coming for lab initially, he did not appear in the other department's patient lists. 
						## If this checkbox is deselected, he will appear in those lists.
						*/
						/*
						if($visit->getOnlylab()==1){
							echo"<input type='checkbox' name='onlylab' checked='checked'>only coming for lab<br>";
						}
						*/
						## Print input field for the patient's charges, a checkbox to indicate, if the tests have been completed and the submit button.
						/*
						## TODO: Bezahlung soll anders werden
						## In nachstehendem echo stand noch folgendes zur Bezahlung drin:
						
						<input type='checkbox' name='charge_checkbox' ";
								if($protocol->getCharge()!=='0.00'){
									echo'checked="checked"';
								}
								echo'> charged:
						<input type="number" name="charge_number" min="0" step="0.01"';
								if($protocol->getCharge()!=='0.00'){
									echo'value='.$protocol->getCharge();
								}
								echo'> GhC <br>
						
						*/
						echo'
						<input type="checkbox" name="labdone"';
								if($lab_list->getLab_done()==1){
									echo'checked="checked"';
								}
								echo'>tests completed?
										
						<br>
								
								<div class="tooltip">
									<button type="submit" name="submit" value="submit"><i id="submitlab" class="far fa-check-circle fa-4x"></i></button>
									<span class="tooltiptext">
										submit
									</span>
								</div>
					</div>
				</form>
				';
		}
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>
