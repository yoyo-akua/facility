<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of patient by a certain patient-ID, with which the page is called.
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	## Initialise new object of protocol by a certain protocol-ID, with which the page is called.
	$protocol_ID=$_GET['protocol_ID'];
	$protocol= new Protocol($protocol_ID);

	## Initialise variable with patient's name.
	$patientname=$patient->getName();

	## Print styling element for border spacing.
	echo "<div class='inputform'>";

	/*
	## If the user clicked on the delete symbol after a drug, delete the prescription from the patient's prescriptions,
	## and from the dispensary register in the database.
	*/
	if(! empty($_GET['delete'])){
		Disp_Drugs::delete_disp_drugs($_GET['delete']);
	}

	## This if-branch is called after the user submitted his selection of drugs.
	if(! empty($_GET['submit'])){
		
		/*
		## Get data from database.
		## Get list of all drugs.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query = "SELECT * FROM drugs";
		$result = mysqli_query($link,$query);
		
		## The following loop will be run once for each of the output drugs from the database query.
		while($row = mysqli_fetch_object($result)){
			
			## Initialise variables with the drug's ID, name and unit of issue.
			$Drug_ID=$row->Drug_ID;
			$Drugname=$row->Drugname;
			$unit=$row->Unit_of_Issue;
			
			/*
			## If the user entered an amount for a drug, but forgot to select the checkbox, ask if the drug should be added to the prescription anyway.
			## If so, add the parameter of the selected checkbox to the URL, from where the user's inputs are read (means: prescribe the drug), 
			## and reload the page.
			## Variable $thispage is defined in variables.php which is included by HTML_HEAD.php.
			## It describes the URL of the current page, from which the user's inputs are read.
			## The unique token within the URL, which is used to avoid, that a certain request is performed multiple times, is updated. 
			## If the user cancels his request, he will be forwarded to the summary of already prescribed drugs for the patient.
			*/
			if(! empty($_GET["amount_$Drug_ID"]) AND empty($_GET["filter_$Drug_ID"])){
				$thispage=preg_replace('/&token=(.*)&/',"&token=$uniqueID&",$thispage);
				echo'<script type="text/JavaScript">;
				if(window.confirm("You did not select the checkbox for '.$Drugname.'. Do you want to prescribe '.$_GET["amount_$Drug_ID"].' '.$unit.'(s)?")){
					window.location.href=("'.$thispage.'&filter_'.$Drug_ID.'=on")
				}
				</script>';
			}
			
			## If the checkbox is selected and an amount entered for the drug, call this if-branch.
			if(! empty($_GET["amount_$Drug_ID"]) AND ! empty($_GET["filter_$Drug_ID"])){
				
				## Initialise variable with the amount of the drug that should be prescribed to the patient.
				$amount=$_GET["amount_$Drug_ID"];
				
				/*
				## Variable $thispage is defined in variables.php which is included by HTML_HEAD.php.
				## It describes the URL of the current page, from which the user's inputs (prescribed drugs) are read.
				## Take the parameter with the amount of the current drug out of $thispage,
				## so that, if the page has to be refreshed, because the user forgot to select a particular checkbox,
				## the drug will not be prescribed again.
				*/
				$thispage=str_replace("amount_$Drug_ID=$amount",'',$thispage);
				
				## Get the current physical stock at hand in the dispensary and store and save in variables.
				$disp_stock=Disp_Drugs::getLastCounts($Drug_ID,time());
				$store_stock=Store_Drugs::getAmount($Drug_ID,time());
				
				## Initialise variable $recom with the dosage recommendation.
				$recom='';
				
				
				## If entered, save the mls recommendation in $recom.
				if(! empty($_GET["mls_$Drug_ID"])){
					$recom=$_GET["mls_$Drug_ID"].'mls x ';
				}
				
				## If the user entered it, save the dosage recommendation in $recom.
				if(! empty($_GET["pattern_$Drug_ID"]) AND ! empty($_GET["days_$Drug_ID"])){
					$recom.=$_GET["pattern_$Drug_ID"].' x '.$_GET["days_$Drug_ID"];
				}
				
				/*
				## Add a new entry to the dispensary's drug register, using the previously set variables.
				## The second sent parameter represents the Store drug's ID. In this case, no Store drug's ID exist.
				## That's why a "0" is sent.
				*/
				$Disp_Drug=Disp_Drugs::new_Disp_Drugs("$Drug_ID","0","$amount",'',$recom,$protocol_ID);
				
				
				/*
				## If the drug is not empty in the store, but in the dispensary, write a notice in the database,
				## that will show a notice on the store's and dispensary's PC,
				## that the drug is empty in the dispensary at next opportunity.
				*/
				if ($disp_stock-$amount<=0 AND $store_stock>0){
					Push::new_Drug_Notification($Drug_ID);
				}
			}
		}

		## Print a headline and the prescribed drugs for the patient.
		echo "<h1>Selected for $patientname</h1>";
		
		if(Disp_Drugs::drugs_prescribed($protocol_ID)){
			echo Disp_Drugs::display_disp_drugs($protocol_ID,'delete');
		}
		
		## Print links to the list of current patients and for adding more prescriptions for this patient.
		echo'
				<a href="current_patients.php"><div class ="box">current patients</div></a>
				<a href="prescribe_drugs.php?patient_ID='.$patient_ID.'&protocol_ID='.$protocol_ID.'"><div class ="box">prescribe more drugs</div></a>
				';
	}

	## This if-branch is called, if the user didn't submit the prescription form yet.
	else{
		
		## Print a headline and, if existing, previous presriptions.
		echo "<h1>Select Drugs for $patientname</h1>";
		if(Disp_Drugs::drugs_prescribed($protocol_ID)) {
			echo "<h2>previously prescribed drugs</h2>".
			Disp_Drugs::display_disp_drugs($protocol_ID,'delete');
		}
			
		## Print the table head and the beginning of the form.
		echo"
			<table>
				<form action='prescribe_drugs.php?' method='get' autocomplete='off'>
				<tr>
					<th style=border-left:none>
						prescribe?
					</th>
					<th>
						dosage recommendation
					</th>
					<th>
						drug
					</th>
					<th>
						amount
					</th>
					<th>
						available
					</th>
				</tr>
			";
		
		
		
		
		/*
		## Inquire, whether the user is searching a specific drug or not.
		## In case he is not, show the ten most common drugs as a "preview".
		## Initialise variable $searchpara on which the search of drugs is based, the value depends on the search field being used or not.
		*/
		if(empty ($_GET['search'])){
			
			/*
			## Get data from database.
			## Get the most frequent drugs from the departments table (Initialiszed in Functions/top_update.php).
			## Transform these to an array of values.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			*/
			$query="SELECT top FROM departments WHERE Department like 'Dispensary'";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			$top_ten=$object->top;
			$top_ten_array=explode(',',$top_ten);
			
			/*
			## $first is used to inquire later on, whether the loop is in its first run or not. 
			## This is necessary to create a correct database request.
			*/
			$first=true;
			
			/*
			## Initialise variable $searchpara on which the search is based. 
			## Within this branch it is used to prevent the top ten drugs to be shown twice 
			## (once at the beginning and once within the list).
			*/
			$searchpara="not like ";
			
			## This loop is run once for each of the top ten drugs. 
			foreach($top_ten_array AS $Drug_ID){
				
				/*
				## Get data from database.
				## Get all the information about the top ten drugs and print them in a table.
				## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
				*/
				$query="SELECT Drug_ID FROM drugs WHERE Drug_ID like '$Drug_ID'";
				$result=mysqli_query($link,$query);
				Disp_Drugs::patient_drugs($result);
				
				/*
				## Get data from database.
				## Get the names of the drugs.
				## Complete $searchpara by the name of the top ten drugs.
				## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
				*/
				$query="SELECT * FROM drugs WHERE Drug_ID like '$Drug_ID'";
				$result=mysqli_query($link,$query);
				$result=mysqli_fetch_object($result);
				$result=$result->Drugname;
				
				if($first){
					$searchpara.="'$result'";
					$first=false;
				}else{
					$searchpara.=" AND Drugname not like '$result'";
				}
			}
			## Spacing between the two parts of the table.
			Drugs::drugsearch_space(5);
		}
		
		## In case the user is searching a specific drug, complete $searchpara by this information.
		else{
			$searchpara="like '%".$_GET['search']."%'";
		}
		
		/*
		## Get data from database.
		## Get a list of all drugs, that match the search.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		## Print all results that match the search in a table.
		*/
		$query = "SELECT Drug_ID FROM drugs WHERE Drugname $searchpara ORDER BY Drugname ASC";
		$result=mysqli_query($link,$query);
		Disp_Drugs::patient_drugs($result);
		
		if(! empty ($_GET['search'])){
			
			## Spacing between the two parts of the table.
			Drugs::drugsearch_space(5);
			
			/*
			## Get data from database.
			## Get a list of all drugs, that do not match the search.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			## Print all results that do not match the search in the lower part of the table.
			## This is necessary to prevent the loss of entered, but not submitted data.
			*/
			$query = "SELECT Drug_ID FROM drugs WHERE Drugname not $searchpara ORDER BY Drugname ASC";
			$result=mysqli_query($link,$query);
			Disp_Drugs::patient_drugs($result);
		}
		
		## Close the table, print the search field and the submit button in the upper right corner and close the form.
		echo"
			</table>
			<div class='tableright'>
				<input type='text' name='search' id='autocomplete' placeholder='search' class='autocomplete'>
				<button type='submit' name='submitsearch'><i class='fas fa-search smallsearch'></i></button><br><br>
				<input type='hidden' name='protocol_ID' value='$protocol_ID'>
				<input type='hidden' name='patient_ID' value='$patient_ID'>
				<input type='hidden' name='token' value='$uniqueID'>
				<input type='submit' name='submit' value='submit'>
			</form>
			</div>
		";
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>
