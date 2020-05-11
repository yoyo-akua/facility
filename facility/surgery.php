<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of patient by a certain patient-ID, with which the page is called.
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	## Initialise new object of protocol by a certain protocol-ID, with which the page is called.
	$protocol_ID=$_GET['protocol_ID'];
	$protocol= new Protocol($protocol_ID);
	

	## This if-branch is called, if the user submitted the procedure.
	if(! empty($_POST['submit'])){
		
		/*
		## Set variable with the name of the procedure, if entered.
		## Set 'Circumcision' as procedure's name, if circumcision's checkbox is checked.  
		## Otherwise show notification and do not enter in database.
		*/
		if(! empty($_POST['circumcision'])){
			$surgery="circumcision";
		}else if(! empty($_POST['other']) AND ! empty($_POST['othername'])){
			$surgery=$_POST['othername'];
		}else{
			$message="Please enter a Procedure";
			Settings::Messagebox($message);
		}
		
		/*
		## Set variable with costs for the procedure, depending on the user's input.
		## If no input was taken, set charge to 0 GhC.
		*/
		if(! empty($_POST['charge'])){
			$charge=$_POST['charge'];
		}else{
			$charge=0;
		}
		$protocol->setCharge($charge);
		
		## If "treatment completed" was selected, set the patient's visit as completed.
		if(! empty($_POST['completed'])){
			$protocol->setcompleted(1);
		}
		
		## If a procedure was entered, write it to the patient's visit's information in the database and lead back to current patient list.
		if(isset($surgery)){
			$protocol->setsurgery($surgery);
			echo "<script>window.location.href=('current_patients.php')</script>";
		}
	}

	## Initialise variable with patient's name.
	$name=$patient->getName();

	## Print headline and input form for the procedure (circumcisions are only available for men) and its costs.
	echo"<h1>Surgery/Procedure on $name</h1>
		<div class='inputform'>
		<form action='surgery.php?protocol_ID=$protocol_ID&patient_ID=$patient_ID' method='post'>
			<h4>Surgery/Procedure</h4><br>";
			if($patient->getSex()=='male'){
				echo"<input type='checkbox' name='circumcision'><label>Circumcision</label><br>";
			}
			echo"<input type='checkbox' name='other'>";
			if($patient->getSex()=='male'){
				echo"<label>other:</label>";
			}
			echo"<input type='text' name='othername'><br><br>
			<h4>Charge</h4><br>
			<input type='number' name='charge' min='0' value='0'> <label>GhC</label><br><br>

			<input type='checkbox' name='completed'><label>treatment in clinic completed</label><br><br>

			<input type='submit' name='submit' value='submit'>
		</form>
		</div>";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>