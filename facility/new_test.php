<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Print styling element for border spacing.
	echo "<div style='margin:30px'>";

	## Print headline with (if set) name of the test.
	if(! empty($_POST)){
		if(empty($_POST['submittest'])){
			echo "<h1>".$_SESSION["test_name"]."</h1>";
		}
	}else{
		echo "<h1>New Test</h1>";
	}


	/*
	## This if-branch is called after the last step of adding a new test.
	## The information from the previous step of adding a new test are saved.
	## Each new test's parameter is saved in the database. 
	## as well as its default value, depending on its input method defined in the second step of adding a new test.
	## Two links for further actions are printed.
	*/
	if(! empty($_POST['submitfinals'])){
	
		## Inquire the new test's number of parameters from the corresponding session.
		$number=$_SESSION['par_number'];
		
		/*
		## This loop is run once for each of the test's parameter.
		## Depending on the parameter's input method (single and multiple choice) the information of the previous step are
		## saved in a corresponding session to be available for further actions.
		## Furthermore, each parameter is saved in the database.
		*/
		for($i=1;$i<=$number;$i++){
		
			## Inquire the new parameter's input method from the corresponding session.
			$type=$_SESSION["par_type_$i"];

			/*
			## If the parameter's input method is a single choice based on radio buttons,
			## save each of its provided option's values in an enumeration in the corresponding session variable. 
			## The variable $first is needed to prevent a comma in front of the first option's value.
			## Save furthermore the default option in a corresponding session.
			*/
			if($type=='radio'){
				$radio_number=$_SESSION["par_outcomes_$i"];
				$first=true;
				for($j=1;$j<=$radio_number;$j++){
					if($first){
						$test_outcomes=$_POST["radio_value_$i($j)"];
						$first=false;
					}else{
						$test_outcomes.=','.$_POST["radio_value_$i($j)"];
					}
					$_SESSION["par_outcomes_$i"]=$test_outcomes;

					if(! empty($_POST["radio_ref_$i"]) AND $_POST["radio_ref_$i"]==$j){
						$_SESSION["par_ref_$i"]=$_POST["radio_value_$i($j)"];
					}

				}
			}
			
			/*
			## If the parameter's input method is a multiple choice,
			## save each of its provided option's values in an enumeration in the corresponding session variable. 
			## The variable $first is needed to prevent a comma in front of the first option's value.
			## Save furthermore the default option in a corresponding session.
			*/
			else if($type=='checkbox'){
				$checkbox_number=$_SESSION["par_outcomes_$i"];
				$first=true;

				for($j=1;$j<=$checkbox_number;$j++){
					if($first){
						$test_outcomes=$_POST["checkbox_value_$i($j)"];
						$first=false;
					}else{
						$test_outcomes.=','.$_POST["checkbox_value_$i($j)"];
					}
					$_SESSION["par_outcomes_$i"]=$test_outcomes;

					if(! empty($_POST["checkbox_ref_$i"])){
						if($_POST["checkbox_ref_$i"]==$j){
							$_SESSION["par_ref_$i"]=$_POST["checkbox_value_$i($j)"];
						}
					}else{
						$_SESSION["par_ref_$i"]="";
					}
				}
			}
			
			/*
			## If the parameter's input method is a single choice based on a dropdown bar,
			## save each of its provided option's values in an enumeration in the corresponding session variable. 
			## The variable $first is needed to prevent a comma in front of the first option's value.
			## Save furthermore the default option in a corresponding session.
			*/
			else if($type=='select'){
				$select_number=$_SESSION["par_outcomes_$i"];
				$first=true;

				$_SESSION["par_ref_$i"]="";

				for($j=1;$j<=$select_number;$j++){
					if($first){
						$test_outcomes=$_POST["select_value_$i($j)"];
						$first=false;
					}else{
						$test_outcomes.=','.$_POST["select_value_$i($j)"];
					}
					$_SESSION["par_outcomes_$i"]=$test_outcomes;

					if(! empty($_POST["select_ref_$i"])){
						if($_POST["select_ref_$i"]==$j){
							$_SESSION["par_ref_$i"]=$_POST["select_value_$i($j)"];
						}
					}else{
						$_SESSION["par_ref_$i"]="";
					}
				}
			}
			
			## Write the current new test's parameter with all its data holding in corresponding sessions into the database.
			Parameters::new_Parameter($_SESSION["par_test_ID"],$_SESSION["par_name_$i"],$_SESSION["par_type_$i"],$_SESSION["par_outcomes_$i"],$_SESSION["par_units_$i"],$_SESSION["par_ref_$i"]);
		}
		
		## Print two links for further actions: one to add another test and one to go back to web page 'Settings'.
		echo "
				<a href='new_test.php'><div class='box'>Add another Test</div></a>
				<a href='settings.php'><div class='box'>Back to Settings</div></a>
				";
	}
	
	/*
	## This if-branch is called in the fourth step of adding a new test.
	## The information from the previous step of adding a new test are saved.
	## A form is printed to ask the user for the values of each parameter's option, 
	## as well as its default value, depending on its input method defined in the second step of adding a new test.
	## At least a link to continue with the next step of adding a new test is printed.
	*/
	else if(! empty($_POST['submitspecifics'])){
	
		## Inquire the new test's number of parameters from the corresponding session.
		$number=$_SESSION['par_number'];
		
		## Start printing the form.
		echo"<form action='new_test.php' method='post'>";
		
		/*
		## This loop is run once for each test's parameter.
		## Depending on the parameter's input method (single and multiple choice) ask the user for 
		##		- a value of each provided parameter's option,
		##		- a default option.
		## Furthermore, for all parameters the previous given information are saved each in corresponding session variable, 
		## to make them available during the whole further process of adding a new test.
		*/
		for($i=1;$i<=$number;$i++){
		
			## Inquire the new parameter's name and input method from the corresponding session variable.
			$name=$_SESSION["par_name_$i"];
			$type=$_SESSION["par_type_$i"];

			/*
			## If the parameter's input method is a single choice based on radio buttons,
			## save the number of provided options in a corresponding session variable. 
			## Print the parameter's name and ask the user for a value of each provided parameter's option,
			## as well as for a default option.
			## Set the parameter's unit to an empty value, because no unit is needed for this input method and 
			## save it in a corresponding session variable.
			*/
			if($type=='radio'){
				$radio_number=$_POST["radio_number_$i"];
				$_SESSION["par_outcomes_$i"]=$radio_number;
				echo"
						<h3>$name</h3>
						<h4>Input Values*</h4><br>
						(You can select a Standard Value for Reference)<br>
						";
				for($j=1;$j<=$radio_number;$j++){
					echo"
							<input type='radio' name='radio_ref_$i' value='$j'>
							<input type='text' name='radio_value_$i($j)' required  pattern='[^,]*' title='Please avoid commas'><br>
							";
				}

				$_SESSION["par_units_$i"]='';

			}
			
			/*
			## If the parameter's input method is a multiple choice,
			## save the number of provided options in a corresponding session variable. 
			## Print the parameter's name and ask the user for a value of each provided parameter's option,
			## as well as for a default option.
			## Set the parameter's unit to an empty value, because no unit is needed for this input method and 
			## save it in a corresponding session variable.
			*/
			else if($type=='checkbox'){
				$checkbox_number=$_POST["checkbox_number_$i"];
				$_SESSION["par_outcomes_$i"]=$checkbox_number;
				echo"
						<h3>$name</h3>
						<h4>Input Values*</h4><br>
						(You can select a Standard Value for Reference)<br>
						";
				for($j=1;$j<=$checkbox_number;$j++){
					echo"
							<input type='radio' name='checkbox_ref_$i' value='$j'>
							<input type='text' name='checkbox_value_$i($j)' required pattern='[^,]*' title='Please avoid commas'><br>
							";
				}

				$_SESSION["par_units_$i"]='';

			}
			
			/*
			## If the parameter's input method is a single choice based on a drop down list,
			## save the number of provided options in a corresponding session variable. 
			## Print the parameter's name and ask the user for a value of each provided parameter's option,
			## as well as for a default option.
			## Set the parameter's unit to an empty value, because no unit is needed for this input method and 
			## save it in a corresponding session.
			*/
			else if($type=='select'){
				$select_number=$_POST["select_number_$i"];
				$_SESSION["par_outcomes_$i"]=$select_number;
				echo"
						<h3>$name</h3>
						<h4>Input Values*</h4><br>
						(You can select a Standard Value for Reference)<br>
						";
				for($j=1;$j<=$select_number;$j++){
					echo"
							<input type='radio' name='select_ref_$i' value='$j'>
							<input type='text' name='select_value_$i($j)' required pattern='[^,]*' title='Please avoid commas'><br>
							";
				}

				$_SESSION["par_units_$i"]='';

			}
			
			/*
			## If the parameter's input method is a number, save the following information in a corresponding session variable:
			##		- the minimum of parameter's value range, if given. Otherwise it is automatically set to 0;
			##		- the maximum of parameter's value range, if given;
			##		- the parameter's unit, if given. Otherwise it is set to an empty value;
			##		- the parameter's default value, which the parameter gets for a healthy patient, if given. Otherwise,
			##		  it is set to an empty value.
			*/
			else if($type=='number'){
				if(! empty($_POST["number_minimum_$i"])){
					$test_outcomes=$_POST["number_minimum_$i"];
				}else{
					$test_outcomes='0';
				}
				if(! empty($_POST["number_maximum_$i"])){
					$test_outcomes.='-'.$_POST["number_maximum_$i"];
				}
				$_SESSION["par_outcomes_$i"]=$test_outcomes;

				if(! empty($_POST["number_unit_$i"])){
					$_SESSION["par_units_$i"]=$_POST["number_unit_$i"];
				}else{
					$_SESSION["par_units_$i"]='';
				}

				if(! empty($_POST["ref_$i"])){
					$_SESSION["par_ref_$i"]=$_POST["ref_$i"];
				}else{
					$_SESSION["par_ref_$i"]="";
				}

			}
			
			/*
			## If the parameter's input method is a short text, save the following information in a corresponding session variable:
			##		- the parameter's value is automatically set to an empty value;
			##		- the parameter's unit, if given. Otherwise it is set to an empty value;
			##		- the parameter's default value, which the parameter gets for a healthy patient, if given. Otherwise,
			##		  it is set to an empty value.
			*/
			else if($type=='text'){
				$_SESSION["par_outcomes_$i"]='';

				if(! empty($_POST["text_unit_$i"])){
					$_SESSION["par_units_$i"]=$_POST["text_unit_$i"];
				}else{
					$_SESSION["par_units_$i"]='';
				}

				if(! empty($_POST["ref_$i"])){
					$_SESSION["par_ref_$i"]=$_POST["ref_$i"];
				}else{
					$_SESSION["par_ref_$i"]="";
				}

			}
			
			/*
			## If the parameter's input method is a long text, save the following information in a corresponding session variable:
			##		- the parameter's maximal text lenght, if given. Otherwise it is automatically set to 1000;
			##		- the parameter's unit, if given. Otherwise it is set to an empty value;
			##		- the parameter's default value, which the parameter gets for a healthy patient, if given. Otherwise,
			##		  it is set to an empty value.
			*/
			else if($type=='textarea') {
				if(! empty($_POST["textarea_length_$i"])){
					$_SESSION["par_outcomes_$i"]=$_POST["textarea_length_$i"];
				}else{
					$_SESSION["par_outcomes_$i"]=1000;
				}

				if(! empty($_POST["_$i"])){
					$_SESSION["par_units_$i"]=$_POST["text_unit_$i"];
				}else{
					$_SESSION["par_units_$i"]='';
				}

				if(! empty($_POST["ref_$i"])){
					$_SESSION["par_ref_$i"]=$_POST["ref_$i"];
				}else{
					$_SESSION["par_ref_$i"]="";
				}
			}
		}

		## At least a button to complete adding a new test is printed.
		echo"
				<br>
				<input type='hidden' name='token' value='$uniqueID'>
				<input type='submit' name='submitfinals' value='complete'>			
				</form>";
	}
	
	/*
	## This if-branch is called in the third step of adding a new test.
	## The information from the previous step of adding a new test are saved.
	## A form is printed to ask the user for the number of provided options of each parameter, 
	## as well as other parameter's information, depending on its input method defined in the second step of adding a new test.
	## A button to continue with the next step of adding a new test is printed.
	*/
	else if(! empty($_POST['submitparameters'])){
		
		## Inquire the new test's number of parameters from the corresponding session.	
		$number=$_SESSION['par_number'];
		
		## Start printing the form.
		echo"<form action='new_test.php' method='post'>";

		/*
		## This loop is run once for each test's parameter.
		## Depending on the parameter's input method, ask the user for 
		##		- its number of provided options (input method: single and multiple choice),
		##		- the minimum and maximum of its value range (input method: number),
		##		- its unit (input method: number, short text),
		##		- its default value, which a parameter gets for a healthy patient (input method: number, short and long text),
		##		- its maximal text length (input method: long text).
		*/
		for($i=1;$i<=$number;$i++){
		
			/*
			## Inquire the parameter's input method and name (if given) from the step before and save it in a session variable.
			## This is necessary to make this information available during the whole further process of adding a new test.
			*/
			$type=$_POST["type_$i"];

			if(! empty($_POST["name_$i"])){
				$name=$_POST["name_$i"];
			}else{
				$name='';
			}

			$_SESSION["par_name_$i"]=$name;
			$_SESSION["par_type_$i"]=$type;

			## Continue printing the form by printing the parameter's name.
			echo "<h3>$name</h3>";
			
			/*
			## If the parameter's input method is a single choice based on radio buttons, 
			## ask the user for its number of provided options.
			*/
			if($type=='radio'){
				echo "
						<h4>Number of possible Outcomes*</h4><br>
						<input type='number' name='radio_number_$i' min='2' max='20' required><br>
						";
			}
			
			/*
			## If the parameter's input method is a multiple choice based, 
			## ask the user for its number of provided options.
			*/
			else if($type=='checkbox'){
				echo "
						<h4>Number of possible Outcomes*</h4><br>
						<input type='number' name='checkbox_number_$i' min='1' max='10' required><br>
						";
			}
			
			/*
			## If the parameter's input method is a single choice based on a drop down list, 
			## ask the user for its number of provided options.
			*/
			else if($type=='select'){
				echo "
						<h4>Number of possible Outcomes*</h4><br>
						<input type='number' name='select_number_$i' min='2' max='30' required><br>
						";
			}
			
			/*
			## If the parameter's input method is a number, ask the user for the minimum and maximum of its value range,
			## the unit and the default value, which the parameter gets for a healthy patient.
			*/
			else if($type=='number'){
				echo "
						<div class='tooltip'> 
							<h4>Minimal Value</h4><br>
							<input type='text' name='number_minimum_$i' pattern='\d*.\d*'><br>
							<h4>Maximal Value</h4><br>
							<input type='text' name='number_maximum_$i' pattern='\d*.\d*'>
							<span class='tooltiptext'>
								Please enter all the decimal places you want to be available as zeroes
								(Example: Minimal Value - 0.000, Maximal Value - 1000.000)
							</span>
						</div>
						<br>
						<h4>Unit</h4><br>
						<input type='text' name='number_unit_$i'><br>
						<h4>Normal Value/ Reference Range</h4><br>
						<input type='text' name='ref_$i'><br>
						";
			}
			
			/*
			## If the parameter's input method is a short text, ask the user for the unit and the default value,
			## which the parameter gets for a healthy patient.
			*/
			else if($type=='text'){
				echo "
						<h4>Unit</h4><br>
						<input type='text' name='text_unit_$i'><br>
						<h4>Normal Value/ Reference Range</h4><br>
						<input type='text' name='ref_$i'><br>
						";
			}
			
			/*
			## If the parameter's input method is a long text, ask the user for its maximal text length and the default value,
			## which the parameter gets for a healthy patient.
			*/
			else if($type=='textarea'){
				echo "
						<h4>Maximal Length</h4><br>
						<input type='number' name='textarea_length_$i' min='100' max='1000'><br>
						<h4>Normal Value/ Reference Range</h4><br>
						<textarea name='ref_$i'></textarea><br>
						";
			}
		}
		
		## A button to continue with the next step of adding a new test is printed.
		echo"
				<br>
				<input type='submit' name='submitspecifics' value='continue'>			
				</form>";
	}
	
	/*
	## This if-branch is called in the second step of adding a new test.
	## The information from the previous step of adding a new test are saved.
	## A form is printed to ask the user for a name and an input method of each new test's parameter.
	## A button to continue with the next step of adding a new test is printed.
	*/
	else if (! empty($_POST['submittest'])){
		
		/*
		## Inquire the new test's name from the step before and save it in a session variable.
		## This is necessary to make this information available during the whole further process of adding a new test.
		*/
		$_SESSION["test_name"]=$_POST['test_name'];
		
		/*
		## Inquires the new test's sex limitation.
		## If no information has been given, the limitation is set to an empty value, which means, no limitation exists.
		*/
		if(! empty($_GET['sex_limit'])){
			$sex_limit=$_GET['sex_limit'];
		}else{
			$sex_limit='';
		}
		
		## Create a new test object with the information given in the first step of adding a new test.
		$test=Tests::new_Test($_SESSION["test_name"],$_POST['frequency'],$sex_limit);
		
		## Print the new test's name as headline.
		echo "<h1>".$_SESSION["test_name"]."</h1>";

		/*
		## Inquire the new test's ID and the number of its parameters and save it in a session.
		## This is necessary to make these information available during the whole further process of adding a new test.
		*/
		$_SESSION['par_test_ID']=$test->getTest_ID();
		$number=$_POST['number'];
		$_SESSION['par_number']=$number;
		
		## Open the form.
		echo"<form action='new_test.php' method='post'>";

		/*
		## This loop is run once for each test's parameter.
		## Ask the user for its input method. Provided input methods are:
		##		- a single choice based on radio buttons for a small amount of parameter's options,
		##		- a multiple choice,
		##		- a single choice based on a drop down list for a big amount of parameter's options,
		##		- a number, of which the value range is defined by the user itself,
		##		- a short text,
		##		- a long text.
		*/
		for($i=1;$i<=$number;$i++){
		
			/*
			## The parameter's name is only printed, if the test's number of parameters is greater than 1.
			## Otherwise the test's name is also used as its one parameter's name.
			*/
			if($number!=='1'){
				echo"
						<h3>Parameter $i: </h3>
						<h4>Name*</h4><br>
						<input type='text' name='name_$i' required><br><br>
						";
			}
			echo"
					<h4>Input Method*</h4><br>

					<input type='radio' name='type_$i' value='radio' required> 
						<div class='tooltip'> 
							radio buttons 
								<span class='tooltiptext'>
									<input type='radio' name='example'> Option 1 
									<input type='radio' name='example'> Option 2 
									<input type='radio' name='example'> Option 3
									(only one option selectable, 2-20 options)
								</span>
						</div>
					<br>

					<input type='radio' name='type_$i' value='checkbox'> 
						<div class='tooltip'> 
							checkboxes
								<span class='tooltiptext'>
									<input type='checkbox'> Option 1 
									<input type='checkbox'> Option 2 
									<input type='checkbox'> Option 3
									(several options selectable, 1-10 options)
								</span>
						</div>
					<br>

					<input type='radio' name='type_$i' value='select'> 
						<div class='tooltip'> 
							selection field
								<span class='tooltiptext'>
									<select>
										<option></option>
										<option>Option 1</option> 
										<option>Option 2</option> 
										<option>Option 3</option> 
									</select>
									(only one option selectable, 2-30 options)
								</span>
						</div>
					<br>

					<input type='radio' name='type_$i' value='number'>
						<div class='tooltip'> 
							number
								<span class='tooltiptext'>
									<input type='number'>
								</span>
						</div>
					<br>

					<input type='radio' name='type_$i' value='text'>
						<div class='tooltip'> 
							short text
								<span class='tooltiptext'>
									<input type='text'>
								</span>
						</div>
					<br>

					<input type='radio' name='type_$i' value='textarea'>
						<div class='tooltip'> 
							long text
								<span class='tooltiptext'>
									<textarea></textarea>
								</span>
						</div>
					<br>";
		}
		
		## A button to continue with the next step of adding a new test is printed.
		echo"	
				<br>
				<input type='submit' name='submitparameters' value='continue'>			
				</form>";
	}
	
	/*
	## This if-branch is called in the first step of adding a new test.
	## Print a form to ask the user of the new test's name, its frequency, its number of test parameters and
	## its limitation to a certain sex. 
	## For the new test's frequency are three options provided: rare, normal and frequent.
	## Print a button to continue with the next step of adding a new test.
	*/
	else{
		echo "
				<form action='new_test.php' method='post'>
				<br>
				<h4>Test's Name*</h4><br>
				<input type='text' name='test_name' required><br>

				<h4>Frequency*</h4><br>
				<select name='frequency' required>
					<option value=''></option>
					<option value='rare'>rare</option>
					<option value='normal'>normal</option>
					<option value='frequent'>frequent</option>
				</select><br>

				<h4>Number of Parameters*</h4><br>
				<input type='number' name='number' min='1' max='100' required><br>

				<h4>Available for</h4>
				<select name='sex_limit'>
					<option name=''>anybody</option>
					<option name='male'>males</option>
					<option name='female'>females</option>
				</select><br>

				<br>
				<input type='hidden' name='token' value='$uniqueID'>
				<input type='submit' name='submittest' value='continue'>
				</form>
				";
	}
	
	/*
	## This function is call in every step of adding a new test. 
	## It prints a footnote with the explaination of the star sign.
	*/
	if(empty($_POST['submitfinals'])){
		echo"
				<br><br>
				* these values are required for the submission of the form
				";
	}
	echo "</div>";
	
	## Contains HTML/CSS structure to style the page in the browser.
	include("HTMLParts/HTML_BOTTOM.php");

?>