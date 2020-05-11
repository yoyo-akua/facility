<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of drug by a certain non-drug-ID, with which the page is called.
	$Non_Drug_ID=$_GET['Non_Drug_ID'];
	$Non_Drug=new Non_Drugs($Non_Drug_ID);

	## Initialise variable with non drug's name.
	$Name=$Non_Drug->getNon_Drugname();

	## If the user made some changes within the table, those are saved in the database.
	if (isset($_POST['submitintable'])){
		$Store_Non_Drugs_ID=$_POST['Store_Non_Drugs_ID'];
		$Store_Non_Drugs=new Store_Non_Drugs($Store_Non_Drugs_ID);
		
		$Store_Non_Drugs->setStoredate($_POST["Storedate"]);
		$Store_Non_Drugs->setParticulars($_POST["Particulars"]);
		$Store_Non_Drugs->setReceived($_POST["Received"]);
		$Store_Non_Drugs->setIssued($_POST["Issued"]);
		$Store_Non_Drugs->setInitials($_POST["Initials"]);
	}

	## If the user clicked on a delete button, next to a row, the store register entry is deleted.
	if (! empty($_GET["Delete"])){
		$query="DELETE FROM store_non_drugs WHERE Store_Non_Drugs_ID='".$_GET['Delete']."'";
		mysqli_query($link,$query);
	}
	
	## This if-branch is called, if the user is adding a new entry to the store register at the end of the list.
	if (! empty($_POST['submitbelowtable'])){
		
		## Initialising variable to check the available amount in the store.
		$available=Store_Non_Drugs::getAmount($Non_Drug_ID,(strtotime($_POST['Storedate'])+(23*3600)+59*60+59));
		
		## If the user is trying to issue more than available, notify him and do not enter in the database.
		if($available<$_POST['Issued']){
			$message='There has been a mistake. You can not issue so many '.$Name.'s';
			Settings::Messagebox($message);
		}
		
		## If the amount the user is trying to issue (or receive) is valid, call this if branch.
		else{
			
			## Add the new entry to the store register in the database.
			$Store_Non_Drugs=Store_Non_Drugs::new_Store_Non_Drugs($_POST['Non_Drug_ID'],$_POST['Storedate'],$_POST['Particulars'],$_POST['Received'],$_POST['Issued'],$_POST['Initials']);
		}
	}

	## Print headline and table head.
	echo"<h1>$Name</h1>
		<table>
			<tr>
				<th style=border-left:none>
					Date
				</th>
				<th>
					Particulars
				</th>
				<th>
					Received
				</th>
				<th>
					Issued
				</th>
				<th>
					Amount on Hand
				</th>
				<th>
					Initials
				</th>
			</tr>
		";

	## Initialise variable $amount which is used to calculate the stock at hand for each table row.
	$amount=0;
	
	/*
	## Get data from database.
	## Get list of all store register entries for this non drug.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT * FROM store_non_drugs WHERE Non_Drug_ID=$Non_Drug_ID ORDER BY Storedate ASC";
	$result=mysqli_query($link,$query);

	## The following loop will be run once for each of the output store register entries from the database query.
	while($row=mysqli_fetch_object($result)){
		
		## Initialise variables with the entry's information.
		$Store_Non_Drugs_ID=$row->Store_Non_Drugs_ID;
		$Storedate=$row->Storedate;
		$Particulars=$row->Particulars;
		$Received=$row->Received;
		$Issued=$row->Issued;
		$Initials=$row->Initials;

		## Colour the table: In dark turquoise if the entry is dealing with a receipt from an (external) drug store and in light turquoise if the drug is expired, otherwise in white.
		if($Received!=0){
			echo "<tr class='receivedtable'>";
		}else if ($Particulars=='EXPIRED'){
			echo "<tr class='adjustmenttable'>";
		}else{
			echo"<tr>";
		}
		
		/*
		## Print an input form, prefilled with the information for each entry in the store register.
		## In the last column, print a link for saving changes to a particular entry as well as a link for deleting this entry.
		*/
		echo"
					<form action='non_drug_protocol.php?Non_Drug_ID=$Non_Drug_ID' method='post'>
					<td style=border-left:none>
						<input type='date' name='Storedate' value='$Storedate'>
					</td>
					<td>
						<input type='text' name='Particulars' value='$Particulars'>
					</td>
					<td>
						<input type='number' name='Received' min='0' value='";
						if($Received==0){
							echo"";
						}else{
							echo $Received;
						}
						echo"'>
					</td>
					<td>
						<input type='number' name='Issued' min='0' value='";
						if($Issued==0){
							echo"";
						}else{
							echo $Issued;
						}
						echo"'>
					</td>		
					<td>					
						";
						$amount=$amount-$Issued+$Received;

						if ($amount!=0){
							echo $amount;
						}else{
							echo "Nill";
						}
						echo"
					</td>
					<td>
						<input type='text' name='Initials' value='$Initials'>
					</td>
					<td>
							<input type='hidden' name='Store_Non_Drugs_ID' value='$Store_Non_Drugs_ID'>
							<input type='submit' name='submitintable' value='save'>
							</form>
					</td>
					<td style=border:none>
						<a href=non_drug_protocol.php?Non_Drug_ID=$Non_Drug_ID&Delete=$Store_Non_Drugs_ID><i class='fas fa-times-circle'></i></a>
					</td>
				</tr>
				</div>
				";
	}
	

	## Print an input form for adding new entries to the store register and prefill it ($today is defined in HTML_HEAD.php).
	echo"
				<form action='non_drug_protocol.php?Non_Drug_ID=$Non_Drug_ID' method='post'>
					<tr class='lasttable'>
						<td style=border-left:none>
							<input type='date' name='Storedate' value='$today' max='$today'>
						</td>
						<td>
							<input type='text' name='Particulars' value='".Store_Non_Drugs::getReceiving_Department($Non_Drug_ID)."' oninput='Expired()'>
						</td>
						<td>
							<input type='number' name='Received' min='0'>
						</td>
						<td>
							<input type='number' name='Issued' min='0'>
						</td>
						<td>
						</td>
						<td>
							<input type='text' name='Initials' value='".Store_Drugs::getIn_Charge()."'>
						</td>
					</tr>
					<tr class='emptytable'>
						<td style=border-left:none>
						</td>
						<td>
						</td>
						<td>
						</td>
						<td>
						</td>
						<td>
						</td>
						<td>
							<input type='hidden' name='Non_Drug_ID' value='$Non_Drug_ID'>
							<input type='hidden' name='token' value='$uniqueID'>
							<input type='submit' name='submitbelowtable' value='save new entry'>
						</td>
					</tr>
				</table>
				";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
	
?>