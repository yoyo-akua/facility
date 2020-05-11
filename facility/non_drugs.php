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
		## Get list of all non drugs.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query = "SELECT * FROM non_drugs";
		$result = mysqli_query($link,$query);
		
		## The following loop will be run once for each of the output non drugs from the database query.
		while($row = mysqli_fetch_object($result)){
			
			## Initialise variable with non drug's ID.
			$Non_Drug_ID=$row->Non_Drug_ID;
			
			## Call this if-branch if there was an entry for the non drug (either a receipt or an issue).
			if (!empty($_POST["Issued_$Non_Drug_ID"]) OR !empty($_POST["Received_$Non_Drug_ID"])){
				
				/*
				## If the user entered particulars, save them in $Particulars.
				## Otherwise use the standard receiving department in the store register entry for the Particulars.
				*/
				if(!empty($_POST['Particulars'])){
					$Particulars=$_POST['Particulars'];
				}else{
					$Particulars=Store_Non_Drugs::getReceiving_Department($Non_Drug_ID);
				}
				
				## Get the available amount of the non drug and save it in $amount.
				$amount=Store_Non_Drugs::getAmount($Non_Drug_ID,time())+intval($_POST["Received_$Non_Drug_ID"]);
				
				## If the available amount is smaller than, what the user is trying to issue, show notification and do not write entry to database.
				if($amount<$_POST["Issued_$Non_Drug_ID"]){
					$Non_Drugname=$row->Non_Drugname;
					$message='There has been a mistake. You can not issue so many '.$Non_Drugname.'s';
					Settings::Messagebox($message);
				}
				
				## If the user wants to enter received non drugs, but left the particulars empty, show a notification and do not write to database.
				else if(! empty($_POST["Received_$Non_Drug_ID"]) AND empty($_POST['Particulars'])){
					$message='Please enter a supplier';
					Settings::Messagebox($message);
				}
				
				## If the entered data are valid, call this if-branch and add a new entry to the store register.
				else{
					Store_Non_Drugs::new_Store_Non_Drugs($Non_Drug_ID,$_POST["Storedate"],$Particulars,$_POST["Received_$Non_Drug_ID"],$_POST["Issued_$Non_Drug_ID"],$_POST["Initials"]);
				}
			}
		}
	}

	
	/*
	## This if-branch is called, if the user searched for a particular non drug.
	## It initialises $searchpara, on which the search is based.
	## $searchpara_exclude is used to display the not searched non drugs below the search results,
	## to prevent loss of already entered (but not saved) data.
	*/
	if(! empty($_POST['search'])){
		$searchpara=" WHERE Non_Drugname like '%".$_POST['search']."%'";
		$searchpara_exclude=" WHERE Non_Drugname not like '%".$_POST['search']."%'";
	}

	## If the user didn't use the search, the variables $searchpara and $searchpara_exclude are initialised as empty variables.
	else{
		$searchpara="";
		$searchpara_exclude="";
	}
	/*
	## Print a headline and an input form for some general data (user's initials, the date and particulars).
	## If no other data is known, prefill these input fields with the person in charge of store and today's date ($today is defined in notice.php).
	## Below that the table head for the store table is printed.
	*/
	echo"
		<h1>Non-Drugs in Store</h1>
		<div class='inputform'>
			<form action='non_drugs.php' method='post' autocomplete='off'>
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
		</tr>
		";

	/*
	## Get data from database.
	## Get a list of all non drugs (that are matching the search results).
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query = "SELECT Non_Drug_ID FROM non_drugs$searchpara ORDER BY Non_Drugname ASC";
	$result = mysqli_query($link,$query);
	
	## This function is used to display the table of non drugs.
	Store_Non_Drugs::show_store_non_drugs($result);

	## This function is responsible for the spacing between the non drugs, that match the search and those, that do not.
	Drugs::drugsearch_space(5);

	/*
	## If a search has taken place, the list of all non drugs that do not match the search, are displayed with this if-branch.
	## This is necessary to display the not searched non drugs below the search results,
	## to prevent loss of already entered (but not saved) data.
	*/
	if(! empty($searchpara_exclude)){
		$query = "SELECT Non_Drug_ID FROM non_drugs$searchpara_exclude ORDER BY Non_Drugname ASC";
		$result = mysqli_query($link,$query);
		Store_Non_Drugs::show_store_non_drugs($result);
	}

	## This function displays the search field and the submit button in the upper right corner of the screen.
	Drugs::search_submit();

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>