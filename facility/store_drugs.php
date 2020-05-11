<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## This if-branch is called, if the user submitted his entries in the list.
	if(! empty($_POST['submit'])){
		
		/*
		## Get data from database.
		## Get list of all drugs.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query = "SELECT * FROM drugs";
		$result = mysqli_query($link,$query);
		
		## The following loop will be run once for each of the output drugs from the database query.
		while($row = mysqli_fetch_object($result)){
			
			## Initialise variable with drug's ID.
			$Drug_ID=$row->Drug_ID;
			
			## Call this if-branch, if there was an entry for the drug (either a receipt or an issue).
			if (!empty($_POST["Issued_$Drug_ID"]) OR !empty($_POST["Received_$Drug_ID"])){
				
				## Get the available amount of the drug in the store (beside from stock in dispensary) and save it in $amount.
				$amount=Store_Drugs::getAmount($Drug_ID,time())+intval($_POST["Received_$Drug_ID"]);
				
				## If the available amount is smaller than, what the user is trying to issue, show notification and do not write entry to database.
				if($amount<$_POST["Issued_$Drug_ID"]){
					$Drugname=$row->Drugname;
					$message='There has been a mistake. You can not issue so many '.$Drugname.'s';
					Settings::Messagebox($message);
				}
				
				## If the entered data are valid, call this if-branch.
				else{
					
					## Add a new entry to the store register.
					$Store_Drugs=Store_Drugs::new_Store_Drugs($Drug_ID,$_POST["Storedate"],$_POST["Particulars"],$_POST["Received_$Drug_ID"],$_POST["Issued_$Drug_ID"],$_POST["Initials"]);
					
					## If the drugs are issued to dispensary, add the entry also to the dispensary's drug register.
					if(! empty ($_POST["Issued_$Drug_ID"]) AND ($_POST['Particulars'])=="Dispensary"){
						$Store_Drugs_ID=$Store_Drugs->getStore_Drugs_ID();
						$disp_stock=Disp_Drugs::getLastCounts($Drug_ID,time());
						$Counts=$disp_stock+($_POST["Issued_$Drug_ID"]);

						Disp_Drugs::new_Disp_Drugs($Drug_ID,$Store_Drugs_ID,'',$Counts,'','0');
					}
				}
			}
		}
	}

	
	/*
	## This if-branch is called, if the user searched for a particular drug.
	## It initialises $searchpara, on which the search is based.
	## $searchpara_exclude is used to display the not searched drugs below the search results,
	## to prevent loss of already entered (but not saved) data.
	*/
	if(! empty($_POST['search'])){
		$searchpara=" WHERE Drugname like '%".$_POST['search']."%'";
		$searchpara_exclude=" WHERE Drugname not like '%".$_POST['search']."%'";
	}

	## If the user didn't use the search, the variables $searchpara and $searchpara_exclude are initialised as empty variables.
	else{
		$searchpara="";
		$searchpara_exclude="";
	}
			
	/*
	## Print a headline and an input form for some general data (user's initials, the date and particulars).
	## If no other data is known, prefill these input fields with the person in charge of store, today's date ($today is defined in notice.php) and the "Dispensary".
	## Below that the table head for the store table is printed.
	*/
	echo"
		<h1>Drugs in Store</h1>
		<div class='inputform'>
			<form action='store_drugs.php' method='post'  >
				<div><label>Date:</label><br>
				<input type='date' name='Storedate'";
				if (! empty($_POST['Storedate'])){
					echo'value="'.$_POST['Storedate'].'"';
				}else{
					echo "value='$today'";
				}
				echo"max='$today'></div>

				<div><label>Particulars:</label><br>
				<input type='text' name='Particulars'";
				if (! empty($_POST['Particulars'])){
					echo'value="'.$_POST['Particulars'].'"';
				}else{
					echo "value='Dispensary'";
				}
				echo"></div>

				<div><label>Initials:</label><br>
				<input type='text' name='Initials'";
				if (! empty($_POST['Initials'])){
					echo'value="'.$_POST['Initials'].'"';
				}else{
					echo "value='".Store_Drugs::getIn_Charge()."'";
				}
				echo"></div>
			</div>
		<table>
		<tr>
			<th style=border-left:none>
				Drug
			</th>
			<th>
				Amount on Hand
			</th>
			<th>
				Quick Entry Issues
			</th>
			<th>
				Quick Entry Invoices
			</th>
			<th>
				in Dispensary
			</th>
		</tr>
		";

	/*
	## Get data from database.
	## Get a list of all drugs (that are matching the search results).
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query = "SELECT Drug_ID FROM drugs$searchpara ORDER BY Drugname ASC";
	$result = mysqli_query($link,$query);

	## This function is used to display the table of drugs.
	Store_Drugs::show_store_drugs($result);

	## This function is responsible for the spacing between the drugs, that match the search and those, that do not.
	Drugs::drugsearch_space(5);

	/*
	## If a search has taken place, the list of all drugs that do not match the search, are displayed with this if-branch,
	## This is necessary to display the not searched drugs below the search results,
	## to prevent loss of already entered (but not saved) data.
	*/
	if(! empty($searchpara_exclude)){
		$query = "SELECT Drug_ID FROM drugs$searchpara_exclude ORDER BY Drugname ASC";
		$result = mysqli_query($link,$query);
		Store_Drugs::show_store_drugs($result);
	}

	## This function displays the search field and the submit button in the upper right corner of the screen.
	Drugs::search_submit();

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>