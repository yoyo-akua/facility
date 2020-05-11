<!--
	This page is used at the beginning of every one of the pages that have a user interface (that does not include a pdf).
	It includes the html head and the turquoise headline with the link to "Home".
-->
<?php
	## Include all object files which are collected in "setup.php" and contain general functions and variables which are used within every page.
	include ("setup.php");
?>
<!DOCTYPE HTML PUBLIC "-//W§C/DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/HTML4/loose.dtd">
<html>

	<!--
		The html head includes:
				- the title of the page (which is used to label the tab and the browser chronic)
				- the fav icon (the small picture displayed on the tab or in the browser chronic)
				- meta dates (like the author's name and a help contact email address)
				- the stylesheet (which contains the style attributes which apply for the whole page)
	-->
	<head>
		<title><?php echo $FACILITY ?></title>
		<link rel='shortcut icon' href='<?php echo $LOGO ?>'>
		<meta name="author" content="Jorinde Schroeter">
		<meta name="help_contact" content="jorinde.schroeter@gmx.de">
		<link rel="stylesheet" href="Style/Main.css.php" type="text/css">
		<link href="Style/Icons/css/all.css" rel="stylesheet">
	</head>

	<!--
		The body will contain any content within the web page by inclusion of this file in other scripts.
		In this file, only the beginning of the page is defined.
		That is the head line which includes the link to the home page and the drop down menu to the page navigation.
		This navigation is dependent on the facility's general settings in "DEFAULTS.php" which is included by "setup.php" at the beginning of the script:
		$DEPARTMENTS tells which departments are activated in that page and prevents the display of departments in the navigation menu that are not availbale in this facility.
		$today is also defined in "setup.php" which is included at the beginning of the script.
	-->
	<body>
	<div class="header" id="headline">
			<ul id="drop-nav">
				<li><a href="index.php"><i class="fas fa-home shadow" id='home'></i></a>
					<ul>
		<?php if(in_array('OPD',$DEPARTMENTS)){?>
						<li><a href="OPD.php"><i class="fas fa-id-card-alt menu-left"></i><font class='menu-text'>OPD</font><i class="fa fa-chevron-right menu-right" aria-hidden="true"></i></a>
							<ul>
								<li><a href="search_patient.php"><i class="fas fa-search menu-left"></i><font class='menu-text'>Search Patients</font></a></li>
								<li><a href="vital_signs.php"><i class="fas fa-thermometer-half menu-left"></i><font class='menu-text'>Vital Signs</font><!--<span class="badge">5</span>--></a></li>
							</ul>
						</li>
		<?php }
		if(in_array('Consulting',$DEPARTMENTS)){?>
						<li><a href="current_patients.php"><i class="fas fa-stethoscope menu-left"></i><font class='menu-text'>Consulting</font></a></li>
		<?php }
		if(in_array('Laboratory',$DEPARTMENTS)){?>
						<li><a href="lab_patients.php"><i class="fas fa-microscope menu-left"></i><font class='menu-text'>Lab</font></a>
						</li>
		<?php }
		if(in_array('Dispensary',$DEPARTMENTS)){?>
						<li><a href="dispensary.php"><i class="fas fa-pills menu-left"></i><font class='menu-text'>Dispensary</font><i class="fa fa-chevron-right menu-right" aria-hidden="true"></i></a>
							<ul>
								<li><a href="disp_patients.php"><i class="fas fa-hand-holding menu-left"></i><font class='menu-text'>Patients in Dispensary</font></i></a></li>
								<li><a href="disp_drugs.php"><i class="fas fa-pills menu-left"></i><font class='menu-text'>Drugs in Dispensary</font></a></li>
								<li><a href="drug_report.php?from=<?php echo $today.'&to='.$today?>"><i class="fas fa-list menu-left"></i><font class='menu-text'>Drug Report</font></a></li>
							</ul>
						</li>
		<?php }
		if(in_array('Maternity',$DEPARTMENTS)){?>
						<li><a href="maternity_patients.php"><i class="fas fa-venus menu-left"></i><font class='menu-text'>Maternity</font></a></li>
		<?php }
		if(in_array('Nutrition',$DEPARTMENTS)){?>
			<li><a href="nutrition_patients.php"><i class="fas fa-weight menu-left"></i><font class='menu-text'>Nutrition</font></a></li>
		<?php }
		if(in_array('Store',$DEPARTMENTS)){?>
						<li><a href="store.php"><i class="fas fa-box-open menu-left"></i><font class='menu-text'>Store</font><i class="fa fa-chevron-right menu-right" aria-hidden="true"></i></a>
							<ul>
								<li><a href="store_drugs.php"><i class="fas fa-pills menu-left"></i><font class='menu-text'>Drugs</font></a></li>
								<li><a href="non_drugs.php"><i class="fas fa-syringe menu-left"></i><font class='menu-text'>Non Drugs</font></a></li>
							</ul>
						</li>
		<?php }?>
						<li><a href="patient_protocol.php?from=<?php echo $today.'&to='.$today; ?>&newold_column=on&insured_column=on"><i class="fas fa-list-ol menu-left"></i><font class='menu-text'>Protocol</font></a></li>

						<li><a href="settings.php"><i class="fas fa-cog menu-left"></i><font class='menu-text'>Settings</font></a></li>
					</ul>
				</li>
			</ul>
			
			<a href="help.php"><i class="fas fa-question shadow" id='help'></i></a>
		</div>
		<a href='#'><i id='toplink' class="fa fa-arrow-up fa-2x" aria-hidden="true"></i></a>
		<div class='content'>

		<!--Audio files are called when a javascript function includes a sound.-->
		<audio id="notification">
			<source src="Sounds/notification.mp3" type="audio/mpeg">
		</audio>