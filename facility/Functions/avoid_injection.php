<?php
    ## Include script variables.php, declaring $thispage.
	include_once("variables.php");

	/*
	## Call this if-branch, if any submitted form in the application contains quotation marks (" - represented by %22) or apostrophes (' - represented by %27).
	## These can lead to database complications and can even be major security risks.
	## Therefore exit the script, show a popup window with the message and lead either to the previous page or the start page (index.php).
	*/
	if(strstr($thispage,'%27') OR strstr($thispage,'%22') OR strstr(http_build_query($_POST),'%27') OR strstr(http_build_query($_POST),'%22')){
		echo'<script type="text/JavaScript">;
					if(window.confirm("Please avoid apostrophes(\') and quotation marks(\") in any input fields")){
						window.history.back();
					}else{
						window.location.href="index.php";
					}
				</script>';
		exit();
    }
?>