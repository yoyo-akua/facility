<?php
	class Store_Drugs{
		## A store drugs object represents a new entry in store register. In the following all parameters of such an entry are defined.
		private $Store_Drugs_ID;		## ID of the entry in store register.
		private $Drug_ID;			## ID of the corresponding drug.
		private $Particulars;			## Contains some details (receiver department, deliverer, drug's expiration). 
		private $Storedate;			## Date of store register entry.
		private $Received;			## Amount of a received drug.			
		private $Issued;			## amount of an issued drug.
		private $Initials;			## Initials of facility staff, which is responsible for store register entry.
		
		/*
		## This function is called, if a new store register entry object is needed for further actions.
		## Saves the information of that store register entry from database (identified by store drugs ID) in that new store register object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Store_Drugs($Store_Drugs_ID){
			global $link;
			$query = "SELECT * FROM store_drugs WHERE Store_Drugs_ID = $Store_Drugs_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->Drug_ID = $row->Drug_ID;
				$this->Storedate = $row->Storedate;
				$this->Particulars = $row->Particulars;			
				$this->Received = $row->Received;
				$this->Issued = $row->Issued;
				$this->Initials = $row->Initials;
			}
			$this->Store_Drugs_ID = $Store_Drugs_ID;
		}
		
		/*
		## Constructor of new store register entry.
		## Is called, if a new store register database entry is created.
		## The data of new store register entry is saved in database for all its parameters.
		## Save this data also in a new created store register entry object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Store_Drugs($Drug_ID,$Storedate,$Particulars,$Received,$Issued,$Initials){
			global $link;
			$query = "INSERT INTO `store_drugs`(`Drug_ID`,`Storedate`,`Particulars`,`Received`,`Issued`,`Initials`) VALUES ('$Drug_ID','$Storedate','$Particulars','$Received','$Issued','$Initials')";
			mysqli_query($link,$query);
		
			$Store_Drugs_ID = mysqli_insert_id($link);
			$instance = new self($Store_Drugs_ID);
			return $instance;
	    	}

		/*
		## Getter function.
		## Returns the ID of that store register entry, on which the function is called.
		*/
		public function getStore_Drugs_ID(){
			return $this->Store_Drugs_ID;
		}

		/*
		## Getter function.
		## Returns the date of that store register entry, on which the function is called.
		*/
		public function getStoredate(){
			return $this->Storedate;
		}

		/*
		## Getter function.
		## Returns the details (receiver department, deliverer, drug's expiration) of that store register entry, on which the function is called.
		*/	
		public function getParticulars(){
			return $this->Particulars;
		}

		/*
		## Getter function.
		## Returns the received drug's amount of that store register entry, on which the function is called.
		*/
		public function getReceived(){
			return $this->Received;
		}

		/*
		## Getter function.
		## Returns the drug's issued amount of that store register entry, on which the function is called.
		*/
		public function getIssued(){
			return $this->Issued;
		}

		/*
		## Getter function.
		## Returns the Initials of facility staff, which is responsible for that store register entry, on which the function is called.
		*/
		public function getInitials(){
			return $this->Initials;
		}

		/*
		## Setter function.
		## Updates the date of that store register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setStoredate($var){
			global $link;
			$query = "UPDATE store_drugs SET Storedate='$var' WHERE Store_Drugs_ID = $this->Store_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Storedate = $var;
		}

		/*
		## Setter function.
		## Updates the details (receiver department, deliverer, drug's expiration) of that store register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setParticulars($var){
			global $link;
			$query = "UPDATE store_drugs SET Particulars='$var' WHERE Store_Drugs_ID = $this->Store_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Particulars = $var;
		}

		/*
		## Setter function.
		## Updates the received drug's amount of that store register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setReceived($var){
			global $link;
			$query = "UPDATE store_drugs SET Received='$var' WHERE Store_Drugs_ID = $this->Store_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Received = $var;
		}

		/*
		## Setter function.
		## Updates the issued drug's amount of that store register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setIssued($var){
			global $link;
			$query = "UPDATE store_drugs SET Issued='$var' WHERE Store_Drugs_ID = $this->Store_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Issued = $var;
		}

		/*
		## Setter function.
		## Updates the Initials of facility staff, which is responsible for that store register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setInitials($var){
			global $link;
			$query = "UPDATE store_drugs SET Initials='$var' WHERE Store_Drugs_ID = $this->Store_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Initials = $var;
		}

		/*
		## Calculation of the amount of a certain drug at the time of a certain date depending on the store register entries until that date.
		## Both parameters are identified by the information, which is sent when calling this function ($Drug_ID and $date).
		## Calculated amount is returned.
		*/	
		public function getAmount($Drug_ID,$date){
			global $link;
			$date=date("Y-m-d",$date);
			$query="SELECT SUM(Received) AS received,SUM(Issued) AS issued FROM store_drugs WHERE Drug_ID=$Drug_ID AND Storedate<='$date 23:59:59'";
			$result = mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			if(! empty($object)){
				$amount=($object->received)-($object->issued);
			}else{
				$amount=0;
			}
			
			return $amount;
		}

		/*
		## Display the store drugs in a table with columns: drugname, store stock amount and unit of issue, amount of drugs to be issued, 
		## amount of drugs to be received and the amount of dispensary stock.
		## The variable $result, which is sent when calling this function, contains a list of all drugs and their parameters, which are to be shown.
		*/
		public function show_store_drugs($result){
			
			## This loop is run once for each drug, which is sent in $result when calling the function 'show_store_drugs'.
			while($row = mysqli_fetch_object($result)){
				
				/*
				## Initialising some variables, which are needed within this function.
				## Variable $unit contains the unit of issue of each drug.
				## Variable $amount contains the amount of each drug in store stock.
				## Variable $lastCounts contains the amount of each drug in dispensary stock.				
				*/
				$drug = new Drugs($row->Drug_ID);
				$Drugname=$drug->getDrugname();
				$unit=$drug->getUnit_of_Issue();
				$lastCounts = Disp_Drugs::getLastCounts($row->Drug_ID,time());
				$amount = Store_Drugs::getAmount($row->Drug_ID,time());
				
				/*
				## Colour the table row, if the amount of a drug in store is greater than 0:
				## 		- in dark turquoise, if the drug is nill in dispensary and has been prescribed to a patient nevertheless,
				##		- in light turquoise, if the drug is nill in dispensary,
				## Otherwise it is displayed in white.
				*/
				if($lastCounts<0 AND $amount>0){
					echo"<tr class='receivedtable'>";
				}else if ($lastCounts==0 AND $amount>0){
					echo "<tr class='adjustmenttable'>";
				}else{
					echo "<tr>";
				}
				
				/*
				## The table rows are printed.
				## The store stock is shown as 'Nill', if no more of that drug is available.
				## The dispensary stock is handled the same way.				
				*/				
				echo"
						<td style=border-left:none>
							<a href='store_drug_protocol.php?Drug_ID=$row->Drug_ID'>$Drugname</a>
						</td>
						<td>
						";
							if($amount==0){
								echo "<b>Nill</b>";
							}else{
								echo '<b>'.$amount.'</b> '.$unit.'s';
							}
							echo"
						</td>
						<td>
							<input type='number' name='Issued_$row->Drug_ID' ";
							if(! empty($_POST["Issued_$row->Drug_ID"]) AND ! isset($_POST['submit'])){
								echo "value=".$_POST["Issued_$row->Drug_ID"];
							}
							echo"
							min='0'>
						</td>
						<td>
							<input type='number' name='Received_$row->Drug_ID' ";
							if(! empty($_POST["Received_$row->Drug_ID"]) AND ! isset($_POST['submit'])){
								echo "value=".$_POST["Received_$row->Drug_ID"];
							}
							echo"
							min='0'>
						</td>
						<td>
						";

							if ($lastCounts<0){
								echo "REQUESTED";
							}else if ($lastCounts==0){
								echo "Nill";
							}else{
								echo $lastCounts;
							}
							echo"
						</td>
					</tr>
					";
			}
		}

		/*
		## The facility's staff, which is the head of store, is identified and returned.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function getIn_Charge(){
			global $link;
			$query = "SELECT * FROM departments WHERE department='Store'";
			$result = mysqli_query($link,$query);
			$object = mysqli_fetch_object($result);
			$person = $object->in_charge;
		
			return $person;
		}
	}
?>
