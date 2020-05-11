<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Print headline and links to pages, that are relevant in the store (lists of drugs and non drugs).
	echo'
			<h1>Store</h1>

			<div class="middle">

			<a href="store_drugs.php"><div class ="tile">Drugs<br>
			<i class="fas fa-pills fa-3x"></i></div></a>
			
			<a href="non_drugs.php"><div class ="tile">Non Drugs<br>
			<i class="fas fa-syringe fa-3x"></i></div></a>
			
			</div>
			';

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>