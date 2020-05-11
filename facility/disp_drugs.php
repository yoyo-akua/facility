<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	
	## If the user is submitting its stock taking, this if-branch is called.
	if(! empty($_GET['submit'])){
		/*
		## Get data from database.
		## Get a list of all drugs.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query = "SELECT * FROM drugs";
		$result = mysqli_query($link,$query);
		
		## The following loop will be run once for each of the output drugs from the database query.
		while($row=mysqli_fetch_object($result)){
			
			## Initialise variable with the drug's ID.
			$drug_ID=$row->Drug_ID;
			
			/*
			## This if-branch is called, if the stock taking input field for the drug was filled.
			## It saves the entered amount for the stock taking in the database.
			## If the drug is nill in dispensary, but not in store, send a notification to store and dispensary to issue the drug (from store to dispensary).
			*/
			if(isset($_GET["count_$drug_ID"]) AND $_GET["count_$drug_ID"]!==''){
				$counting=$_GET["count_$drug_ID"];
				$store_stock=Store_Drugs::getAmount($drug_ID,time());
				if ($store_stock>0 AND $counting=='0'){
					Push::new_Drug_Notification($drug_ID);
				}
				Disp_Drugs::new_Disp_Drugs($drug_ID,'0','0',$counting,'','0');
			}
		}
	}
	
	## Print headline and table head.
	echo"
			<form action='disp_drugs.php' method='get'  >

			<h1>Drugs in Dispensary</h1> 
				<table>
					<tr>
						<th style=border-left:none>
							Drug
						</th>
						<th>
							Amount available
						</th>
						<th>
							Stock taking
						</th>
						<th>
							Available in Store
						</th>
					</tr>
			";

	/*
	## Initialising $searchpara, on which the search is based (depending on the user's search).
	## $searchpara_exclude is used to display the not searched drugs below the search results,
	## to prevent loss of already entered (but not saved) data.
	*/
	if(! empty($_GET['search'])){
		$var=$_GET['search'];
		$searchpara=" WHERE Drugname like '%$var%'";
		$searchpara_exclude=" WHERE Drugname not like'%$var%'";
	}else{
		$searchpara = '';
		$searchpara_exclude=''; 
	}
	/*
	## Get data from database.
	## Get a list of all drugs (that are matching the search results).
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT * FROM drugs $searchpara ORDER BY Drugname ASC";
	$result=mysqli_query($link,$query);
	
	## This function is used to display the table of drugs.
	Disp_Drugs::drugs_in_disp($result);

	## This function is responsible for the spacing between the drugs, that match the search and those, that do not.
	Drugs::drugsearch_space(4);

	## If a search has taken place, the list of all drugs that do not match the search, are displayed with this if-branch,
	if(! empty($searchpara_exclude)){
		$query="SELECT * FROM drugs$searchpara_exclude ORDER BY Drugname ASC";
		$result=mysqli_query($link,$query);
		Disp_Drugs::drugs_in_disp($result);
	}

	## This function displays the search field and the submit button in the upper right corner of the screen.
	Drugs::search_submit();

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>