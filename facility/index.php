<?php 
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	/*
	## Print a headline with the name of the facility.
	## $FACILITY is defined in "deafults/DEFAULTS.php", which is adapted by the administrator.
	*/
	echo'
			<h1>'.$FACILITY.'</h1>

			<div class="middle">
			';

	## Depending on which departments are activated in "defaults/DEFAULTS.php", print links to department's start pages.
	if(in_array('OPD',$DEPARTMENTS)){
		echo'
				<a href="OPD.php"><div class ="tile">OPD<br>
				<i class="fas fa-id-card-alt fa-3x"></i></div></a>
				';
	}
	if(in_array('Consulting',$DEPARTMENTS)){
		echo'
			<a href="current_patients.php"><div class ="tile">Consulting<br>
			<i class="fas fa-stethoscope fa-3x"></i></div></a>
			';
	}
	if(in_array('Laboratory',$DEPARTMENTS)){
		echo'
			<a href="lab_patients.php"><div class ="tile">Lab<br>
			<i class="fas fa-microscope fa-3x"></i></div></a><br>
			';
	}
	if(in_array('Dispensary',$DEPARTMENTS)){
		echo'
			<a href="dispensary.php"><div class ="tile">Dispensary<br>
			<i class="fas fa-pills fa-3x"></i></div></a>
			';
	}
	if(in_array('Maternity',$DEPARTMENTS)){
		echo'
			<a href="maternity_patients.php"><div class ="tile">Maternity<br>
			<i class="fas fa-venus fa-3x"></i></div></a>
			';
	}
	if(in_array('Nutrition',$DEPARTMENTS)){
		echo'
			<a href="nutrition_patients.php"><div class ="tile">Nutrition<br>
			<i class="fas fa-weight fa-3x"></i></div></a><br>
			';
	}
	if(in_array('Store',$DEPARTMENTS)){
		echo'
			<a href="store.php"><div class ="tile">Store<br>
			<i class="fas fa-box-open fa-3x"></i></div></a><br>
			';
	}
	## Print links to protocol and settings.
	echo'
			<a href="patient_protocol.php?from='.$today.'&to='.$today.'&newold_column=on&insured_column=on"><div class ="middletile" style="margin:25px">Protocol<br>
			<i class="fas fa-list-ol fa-3x"></i></div></a>

			<a href="settings.php"><div class ="middletile" style="margin:25px">Settings<br>
			<i class="fas fa-cog fa-3x"></i></div></a>
			';

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
		
?>
