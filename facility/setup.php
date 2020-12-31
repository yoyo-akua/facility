<?php
	/*
	## This page doesn't have any graphical user surface.
	## It is mainly used to include the files from the Object folder,
	## in which the objects and functions are defined.
	*/
	include_once("Objects/Patient.php");
	include_once("Objects/Protocol.php");
	include_once("Objects/Drugs.php");
	include_once("Objects/Store_Drugs.php");
	include_once("Objects/Non_Drugs.php");
	include_once("Objects/Store_Non_Drugs.php");	
	include_once("Objects/Disp_Drugs.php");	
	include_once("Objects/Diagnoses.php");	
	include_once("Objects/Maternity.php");
	include_once("Objects/ANC.php");	
	include_once("Objects/Delivery.php");	
	include_once("Objects/Parameters.php");	
	include_once("Objects/Lab.php");	
	include_once("Objects/Tests.php");	
	include_once("Objects/Delivery_Categories.php");
	include_once("Objects/Diagnosis_IDs.php");
	include_once("Objects/Vital_Signs.php");
	include_once("Objects/Push.php");
	include_once("Objects/Seen.php");
	include_once("Objects/Nutrition.php");
	include_once("Objects/Complaints.php");
	include_once("Objects/Uploads.php");
	include_once("Objects/Staff.php");
	include_once("Objects/Visit.php");
	include_once("Objects/Insurance.php");
	include_once("Objects/Referral.php");
	include_once("Objects/Lab_List.php");
	
	## The Settings file contains object independent functions.
	include_once("Objects/Settings.php");

	## This file contains general database information and is used to establish any database connection.
	include_once("Objects/DB.php");	

	## The Function files are used to run certain functions on every opening of a page.
	include_once("Functions/top_update.php");
	include_once("Functions/restrictions.php");
	include_once("Functions/auto_logout.php");
	include_once("Functions/token.php");
	include_once("Functions/treatment_completed.php");
	include_once("Functions/avoid_injection.php");

	/*
	## This file is used to include the facility's general settings 
	## (like facility's name, departments etc.) to each file.
	*/
	include_once("defaults/DEFAULTS.php");
?>
