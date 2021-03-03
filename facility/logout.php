<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
    include("HTMLParts/HTML_HEAD.php");
    
    ## Reset the user's name and password from the session variable and forward him to the home page.
    unset($_SESSION['staff_name']);
    unset($_SESSION['staff_password']);
    unset($_SESSION['staff_ID']);
    unset($_SESSION['staff_department']);
    echo '<script>history.go(-1)</script>';

    ## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
?>