<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of drug by a certain drug-ID, with which the page is called.
	$Drug_ID=$_GET['Drug_ID'];
	$Drug=new Drugs($Drug_ID);

	## Initialise variable with drug's name.
	$Name=$Drug->getDrugname();

	## If the user made some changes within the table, those are saved in the database.
	if (isset($_POST['submitintable'])){
		$Store_Drugs_ID=$_POST['Store_Drugs_ID'];
		$Store_Drugs=new Store_Drugs($Store_Drugs_ID);
		
		$Store_Drugs->setStoredate($_POST["Storedate"]);
		$Store_Drugs->setParticulars($_POST["Particulars"]);
		$Store_Drugs->setReceived($_POST["Received"]);
		$Store_Drugs->setIssued($_POST["Issued"]);
		$Store_Drugs->setInitials($_POST["Initials"]);
	}

	## If the user clicked on a delete button, next to a row, the store and dispensary register entry is deleted.
	if (! empty($_GET["Delete"])){
		$query="DELETE FROM store_drugs WHERE Store_Drugs_ID='".$_GET['Delete']."'";
		mysqli_query($link,$query);
		$query="DELETE FROM disp_drugs WHERE Store_Drugs_ID='".$_GET['Delete']."'";
		mysqli_query($link,$query);
	}
	
	## This if-branch is called, if the user is adding a new entry to the store register at the end of the list.
	if (! empty($_POST['submitbelowtable'])){
		
		## Initialising variable to check the available amount in the store.
		$available=Store_Drugs::getAmount($Drug_ID,(strtotime($_POST['Storedate'])+(23*3600)+59*60+59));
		
		## If the user is trying to issue more than available, notify him and do not enter in the database.
		if($available<$_POST['Issued']){
			$message='There has been a mistake. You can not issue so many '.$Name.'s';
			Settings::Messagebox($message);
		}
		
		## If the amount the user is trying to issue (or receive) is valid, call this if branch.
		else{
			
			## Add the new entry to the store register in the database.
			$Store_Drugs=Store_Drugs::new_Store_Drugs($_POST['Drug_ID'],$_POST['Storedate'],$_POST['Particulars'],$_POST['Received'],$_POST['Issued'],$_POST['Initials']);
			
			## If the drugs are issued to dispensary, add the entry also to the dispensary's drug register.
			if(! empty ($_POST["Issued"]) AND ($_POST['Particulars'])=="Dispensary"){
				$Store_Drugs_ID=$Store_Drugs->getStore_Drugs_ID();
				$disp_stock=Disp_Drugs::getLastCounts($Drug_ID,time());
				$Counts=$disp_stock+($_POST['Issued']);
				
				Disp_Drugs::new_Disp_Drugs($Drug_ID,$Store_Drugs_ID,'',$Counts,'','0');
			}		
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
	## Get list of all store register entries for this drug.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT * FROM store_drugs WHERE Drug_ID=$Drug_ID ORDER BY Storedate ASC";
	$result=mysqli_query($link,$query);

	## The following loop will be run once for each of the output store register entries from the database query.
	while($row=mysqli_fetch_object($result)){
		
		## Initialise variables with the entry's information.
		$Store_Drugs_ID=$row->Store_Drugs_ID;
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
					<form action='store_drug_protocol.php?Drug_ID=$Drug_ID' method='post'>
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
							<input type='hidden' name='Store_Drugs_ID' value='$Store_Drugs_ID'>
							<input type='submit' name='submitintable' value='save'>
							</form>
					</td>
					<td style=border:none>
						<a href=store_drug_protocol.php?Drug_ID=$Drug_ID&Delete=$Store_Drugs_ID><i class='fas fa-times-circle'></i></a>
					</td>
				</tr>
				</div>
				";
	}

	## Print an input form for adding new entries to the store register and prefill it ($today is defined in HTML_HEAD.php).
	echo"
				<form action='store_drug_protocol.php?Drug_ID=$Drug_ID' method='post'>
					<tr class='lasttable'>
						<td style=border-left:none>
							<input type='date' name='Storedate' value='$today' max='$today'>
						</td>
						<td>
							<input type='text' name='Particulars' value='Dispensary' id='last' oninput='Expired()'>
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
							<input type='hidden' name='Drug_ID' value='$Drug_ID'>
							<input type='hidden' name='token' value='$uniqueID'>
							<input type='submit' name='submitbelowtable' value='save new entry'>
						</td>
					</tr>
				</table>
				";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
	
?>