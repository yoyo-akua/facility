<?php
	## If the user clicked on "treatment in clinic completed" for any patient, show a success notfication.
	if(! empty($_GET['completed']) OR ! empty($_POST['completed'])){
		$message="Treatment has been completed";
		Settings::messagebox($message);
	}
?>