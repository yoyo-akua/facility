<?php
	## Include the file in which the database connection is described.
	include("DB.php");

	## This class contains functions used for the creation of push notifications.
	class Push {
		
		## This function is only used within this class to retrieve data from the database and return them in an array $data.
		private function getData($sqlQuery) {
			global $link;
			$result = mysqli_query($link, $sqlQuery);
			$data= array();
			while ($row = mysqli_fetch_array($result)) {
				$data[]=$row;            
			}
			return $data;
		}

		## This function is used to create a list of all notifications for the user's PC.
		public function listNotificationUser($IP){	
			$query = "SELECT * FROM notifications WHERE IP='$IP' AND notif_time <= CURRENT_TIMESTAMP()";
			return  $this->getData($query);
		}	

		/*
		## Constructor of new notification.
		## Is called, if a new notification database entry is created.
		## The IP addresses of all PCs entered for this department are retrieved from departments table of the database first.
		## For each IP address a new notification entry is created with all its parameters.
		## These parameters are:
		## 		- title of the notification ($title)
		## 		- text of the notification ($msg)
		## 		- point of time at which the notification is to be displayed for the next time ($time)
		##		- IP address of the PC on which the message is to be displayed (retrieved from $department)
		## Variable $link contains credentials to connect with database and is defined in DB.php.
		*/
		public function new_Notification($title, $msg, $time, $department){
			global $link;
			$query="SELECT * FROM departments WHERE Department='$department'";
			$result=mysqli_query($link,$query);
			$result=mysqli_fetch_object($result);
			$IPs=$result->IP;
			$IPs=explode(" & ",$IPs);
			foreach($IPs AS $IP){
				$query = "INSERT INTO notifications(title, notif_msg, notif_time, IP) VALUES('$title', '$msg', '$time', '$IP')";
				$result = mysqli_query($link, $query);
			}
			
		}

		/*
		## This function is called when a drug is finished in dispensary.
		## It first creates a new object of the class Drug as $drug using the Drug ID which is sent along when calling the function.
		## Set arrays $disp and $store which are used to store the text, title and department of the notification sent to Store and Dispensary.
		## Use both arrays to call the function new_Notification which writes the notification(s) to the notification table in the database.
		*/
		public function new_Drug_Notification($Drug_ID){
			global $link;

			$drug=new Drugs($Drug_ID);
			$drug=$drug->getDrugname();
			
			$disp=array();
			$disp['text']="Please get $drug from Store";
			$disp['title']="Drug finished";
			$disp['department']="Dispensary";

			$store=array();
			$store['text']="$drug finished in Dispensary";
			$store['title']="Stock out in Dispensary";
			$store['department']="Store";

			$department=array($disp,$store);

			foreach($department AS $dep){
				Push::new_Notification($dep['title'],$dep['text'],date("Y-m-d H:i:s",time()),$dep['department']);
			}
		}

		/*
		## This function is called when deleting or updating a notification.
		## If the sent parameter $nextTime equals "0", delete the notification,
		## otherwise use $nextTime to update the time of the next display of the notification in the database. 
		## $id is the ID of the notification entry in the notification table of the database and identifies the notification to be updated/deleted.
		*/
		public function updateNotification($id, $nextTime) {		
			global $link;
			if($nextTime==0){
				$query="DELETE FROM notifications WHERE notification_ID='$id'";
			}else{
				$query = "UPDATE notifications SET notif_time = '$nextTime' WHERE notification_ID='$id'";
			}
			mysqli_query($link, $query);
		}	
	}
?>