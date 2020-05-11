<?php
	
	class Store_Non_Drugs{

		## A store non drugs object represents a new entry in store register. In the following all parameters of such an entry are defined.
		private $Store_Non_Drugs_ID;		## ID of the entry in store register.
		private $Non_Drug_ID;			## ID of the corresponding non drug.
		private $Particulars;			## Contains some details (receiver department, deliverer, non drug's expiration).
		private $Storedate;			## Date of store register entry.
		private $Received;			## Amount of a received non drug.
		private $Issued;			## amount of an issued non drug.
		private $Initials;			## Initials of facility staff, which is responsible for store register entry.
		
		/*
		## This function is called, if a new store register entry object is needed for further actions.
		## Saves the information of that store register entry from database (identified by store non drugs ID) in that new store register object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Store_Non_Drugs($Store_Non_Drugs_ID){
			global $link;
			$query = "SELECT * FROM store_non_drugs WHERE Store_Non_Drugs_ID = $Store_Non_Drugs_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->Non_Drug_ID = $row->Non_Drug_ID;
				$this->Storedate = $row->Storedate;
				$this->Particulars = $row->Particulars;			
				$this->Received = $row->Received;
					$this->Issued = $row->Issued;
				$this->Initials = $row->Initials;
			}
			$this->Store_Non_Drugs_ID = $Store_Non_Drugs_ID;
		}
		
		/*
		## Constructor of new store register entry.
		## Is called, if a new store register database entry is created.
		## The data of new store register entry is saved in database for all its parameters.
		## Save this data also in a new created store register entry object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Store_Non_Drugs($Non_Drug_ID,$Storedate,$Particulars,$Received,$Issued,$Initials){
			global $link;
			$query = "INSERT INTO `store_non_drugs`(`Non_Drug_ID`,`Storedate`,`Particulars`,`Received`,`Issued`,`Initials`) VALUES ('$Non_Drug_ID','$Storedate','$Particulars','$Received','$Issued','$Initials')";
			mysqli_query($link,$query);
			
			$Store_Non_Drugs_ID = mysqli_insert_id($link);
			$instance = new self($Store_Non_Drugs_ID);
			return $instance;
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
		## Returns the received non drug's amount of that store register entry, on which the function is called.
		*/		
		public function getReceived(){
			return $this->Received;
		}

		/*
		## Getter function.
		## Returns the non drug's issued amount of that store register entry, on which the function is called.
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
			$query = "UPDATE store_non_drugs SET Storedate='$var' WHERE Store_Non_Drugs_ID = $this->Store_Non_Drugs_ID";
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
			$query = "UPDATE store_non_drugs SET Particulars='$var' WHERE Store_Non_Drugs_ID = $this->Store_Non_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Particulars = $var;
		}

		/*
		## Setter function.
		## Updates the received non drug's amount of that store register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setReceived($var){
			global $link;
			$query = "UPDATE store_non_drugs SET Received='$var' WHERE Store_Non_Drugs_ID = $this->Store_Non_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Received = $var;
		}

		/*
		## Setter function.
		## Updates the issued non drug's amount of that store register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setIssued($var){
			global $link;
			$query = "UPDATE store_non_drugs SET Issued='$var' WHERE Store_Non_Drugs_ID = $this->Store_Non_Drugs_ID";
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
			$query = "UPDATE store_non_drugs SET Initials='$var' WHERE Store_Non_Drugs_ID = $this->Store_Non_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Initials = $var;
		}

		/*
		## Display the store non drugs in a table with columns: drugname, store stock amount, amount of drugs to be issued and 
		## amount of drugs to be received.
		## The variable $result, which is sent when calling this function, contains a list of all non drugs and their parameters, 
		## which are to be shown in database.
		*/
		public function show_store_non_drugs($result){
			
			## This loop is run once for each non drug, which is sent in $result when calling the function 'show_store_non_drugs'.
			while($row = mysqli_fetch_object($result)){
				
				/*
				## Initialising some variables, which are needed within this function.
				## Variable $Non_Drugname contains the name of the non drug.
				## Variable $amount contains the amount of each non drug in store stock.				
				*/
				$non_drug = new Non_Drugs($row->Non_Drug_ID);
				$Non_Drugname=$non_drug->getNon_Drugname();
				$amount =Store_non_Drugs::getAmount($row->Non_Drug_ID,time());

				/*
				## The table rows are printed.
				## The store stock is shown as 'Nill', if no more of that non drug is available.			
				*/
				echo"
					<tr>
						<td style=border-left:none>
							<a href='non_drug_protocol.php?Non_Drug_ID=$row->Non_Drug_ID'>$Non_Drugname</a>
						</td>
						<td>
						";
							if($amount==0){
								echo "Nill";
							}else{
								echo $amount;
							}
							echo"
						</td>
						<td>
							<input type='number' name='Issued_$row->Non_Drug_ID' min='0'";
							if(! empty($_POST["Issued_$row->Non_Drug_ID"]) AND (!isset($_POST['submit']) OR (! empty($_POST["Received_$row->Non_Drug_ID"]) AND empty($_POST['Particulars'])))){
								echo "value=".$_POST["Issued_$row->Non_Drug_ID"];
							}
							echo">
						</td>
						<td>
							<input type='number' name='Received_$row->Non_Drug_ID' min='0'";
							if(! empty($_POST["Received_$row->Non_Drug_ID"]) AND (! isset($_POST['submit']) OR (! empty($_POST["Received_$row->Non_Drug_ID"]) AND empty($_POST['Particulars'])))){
								echo "value=".$_POST["Received_$row->Non_Drug_ID"];
							}
							echo">
						</td>
					</tr>	
					";
			}
		}

		/*
		## Calculation of the amount of a certain non drug at the time of a certain date depending on the store register entries until that date.
		## Both parameters are identified by the information, which is sent when calling this function ($Non_Drug_ID and $date).
		## Calculated amount is returned.
		*/
		public function getAmount($Non_Drug_ID,$date){
			global $link;
			$date=date("Y-m-d",$date);
			$query="SELECT SUM(Received) AS received,SUM(Issued) AS issued FROM store_non_drugs WHERE Non_Drug_ID=$Non_Drug_ID AND Storedate<='$date 23:59:59'";
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
		## The department, which is receiver of an issued non drug, is identified and returned.
		## The variable $Non_Drug_ID is used to identify the issued non drug, for which the receiving department is to be inquired.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function getReceiving_Department($Non_Drug_ID){
			global $link;
			$query="SELECT Receiving_Department FROM non_drugs WHERE Non_Drug_ID='$Non_Drug_ID'";
			$result=mysqli_query($link,$query);
			$row=mysqli_fetch_object($result);
			$Particulars=$row->Receiving_Department;
			
			return $Particulars;
		}
	}
?>
