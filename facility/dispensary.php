<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");
	
	/*
	## Print headline and links to pages, that are relevant in the dispensary (list of patients, list of drugs, record analysis).
	## The value of the variabe $today is defined in the HTML_HEAD.php, that is included at the beginning of the page.
	*/
	echo'
			<h1>Dispensary</h1>

			<div class="middle">

			<a href="disp_patients.php"><div class ="tile">Dispense<br>
			<i class="fas fa-hand-holding fa-3x"></i></div></a>

			<a href="disp_drugs.php"><div class ="tile">Drugs<br>
			<i class="fas fa-pills fa-3x"></i></div></a>

			<a href="drug_report.php?from='.$today.'&to='.$today.'"><div class ="tile">Report<br>
			<i class="fas fa-list fa-3x"></i></div></a>
			
			</div>
			';

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>