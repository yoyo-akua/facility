<?php
	## This page is used to declare some variables used within different scripts. It will be included at the beginning of every script.

    ## Initialise variable with the user's PC's IP address.
    global $own_IP;
    $own_IP= $_SERVER['REMOTE_ADDR'];

	## Initialise global variable which stores the url of the page.
	global $thispage;
    $thispage=$_SERVER['REQUEST_URI'];
    
    ## Initialise global variable which can be used to create a token within an input form.
	global $uniqueID;
	$uniqueID=number_format(microtime(true),4);


	## Create global variable $today with today's date in American time format which is used for database queries.
	global $today;
	$today=date("Y-m-d");
?>