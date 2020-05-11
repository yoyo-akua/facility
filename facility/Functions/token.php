
<?php
    /*
	## If the page is called through an input form which should only be sent once (not refreshed!) 
	## to prevent double entries in the database, call this if-branch.
	*/
	if(! empty($_GET['token']) OR ! empty($_POST['token'])){
		
		/*
		## Initialise a variable $token which contains the "token" of the page 
		## - an exact time (stamp - in micro seconds) at which the page was called.
		## This is used as an identifier of the particular form submission.
		## The assumption is that if this is repeated that indicates that the page has been refreshed,
		## because it is highly improbable that two different forms (at different computers) are sent 
		## in the exact same micro second.
		*/
		if(! empty($_GET['token'])){
			$token=$_GET['token'];
		}else if (! empty($_POST['token'])){
			$token=$_POST['token'];
		}
		
		## Check in the database if the token has been used before.
		$query="SELECT token FROM token WHERE token like '$token'";
		$result=mysqli_query($link,$query);
		$object=mysqli_fetch_object($result);
		
		## If the token has been used before, show a message box and exit the current script.
		if(! empty($object)){
			echo'<script type="text/JavaScript">;
						if(window.confirm("You can not refresh this page without causing database complications. Do you want to go back to the start page?")){
							window.location.href="index.php";
						}
					</script>';
			exit("A refresh of this page would have caused database complications");
		}
		
		## If the token has not been used before, write it to the database (by overwriting the oldest entry in the token table).
		else{
			$query="UPDATE token SET `token`='$token' ORDER BY token ASC LIMIT 1";
			mysqli_query($link,$query);
		}
    }
?>