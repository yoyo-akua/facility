<?php

	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");
	
	## Print headline and buttons in browser.
	echo'
			<h1>OPD</h1>
			
			<div class="middle">
			
			<a href="search_patient.php"><div class ="tile">Search<br>
			<i class="fas fa-search fa-3x"></i></div></a>

			<a href="vital_signs.php"><div class ="tile">Vitals<br>
			<i class="fas fa-thermometer-half fa-3x"></i></div></a>
			
			</div>
			';
	
	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>