<?php
    
	## Define the credentials to connect to database.
	$user="demo_admin";
	$password="weltfrieden";
	$server = "localhost";
	$dbname = "demos";
    

	## Establish database connection.
	global $link;
	$link = new mysqli($server,$user,$password,$dbname);

	## In case of an error while connecting to database, print the system error message.
	if($link->connect_errno) echo $link->connect_error;

	## Ensure that connection is established to correct database, which is defined in $dbname.
	mysqli_query($link,"Set NAMES 'utf8'");
	mysqli_select_db($link,$dbname);
?>