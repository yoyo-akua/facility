<?php

	class Uploads{

		## Define variables prescribing the upload.
		private $upload_ID;	## ID of the upload.
		private $protocol_ID;	## ID of the patient's visit.
		private $filename;	## The name of the uploaded file.
		private $department_ID;	## The department for which it was uploaded.
		
		/*
		## This function is called, if a new upload object is needed for further actions.
		## Saves the information of that entry from database (identified by upload ID) in that new upload object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Uploads($upload_ID){
			global $link;
			$query = "SELECT * FROM uploads WHERE upload_ID = $upload_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->protocol_ID = $row->protocol_ID;
				$this->filename = $row->filename;
				$this->department_ID = $row->department_ID;
			}
			$this->upload_ID = $upload_ID;
		}
		
		/*
		## Constructor of uploads entry.
		## Is called, if a upload database entry is created.
		## The data of new upload is saved in database for all its parameters.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Uploads($protocol_ID,$filename,$department){
			global $link;
			$query = "INSERT INTO `uploads`(`protocol_ID`,`filename`,`department`) VALUES ('$protocol_ID','$filename','$department')";
			mysqli_query($link,$query);
			
			$protocol_ID = mysqli_insert_id($link);
			$instance = new self($protocol_ID);
			return $instance;	
		}
		
		
		/*
		## Getter function.
		## Returns the upload file's name, on which the function is called.
		*/
		public function getFilename(){
			return $this->filename;
		}

		/*
		## Getter function.
		## Returns the department in which the file has been uploaded, of which the function is called.
		*/
		public function getDepartment_ID(){
			return $this->department_ID;
		}

		/*
		## Getter function.
		## Returns the department in which the file has been uploaded, of which the function is called.
		*/
		public function getProtocol_ID(){
			return $this->protocol_ID;
		}

		public function getUploadArray($protocol_ID){
			global $link;
			$query = "SELECT * FROM uploads WHERE protocol_ID = $protocol_ID";
			$result = mysqli_query($link,$query);
			$IDs=array();
			while($row = mysqli_fetch_object($result)){
				$IDs[]=$row->upload_ID;
			}
			return $IDs;
		}

	}
?>