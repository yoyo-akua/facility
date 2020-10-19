<?php
	class Settings{

		/*
		## This function displays each password request at the web page 'Settings'.
		## For that an input field for typing the password and a submit button is printed.
		## The variable which is sent when calling this function contains the department's name, to which the password is requested.
		*/
		public function Password($var){
			echo'
				<div class="inputform">
					<form action="settings.php" method="post">
						<div><label>Password:</label><br>
						<input type="password" name="'.$var.'"><br><br>
						<input type="submit" value="submit"></div>
					</form>
				</div>
			';
		}

		/*
		## This function displays a popup window for a password request as an overlay over the web page.
		## For that a notice for the user, an input field for typing the password and a submit button is printed.
		## The variables which are sent when calling this function have the following meaning:
		##		- variable $page contains the URL of that page, on which the password popup is shown,
		##		- variable $text contains the notice for the user and describes, what he has to do,
		##		- variable $hidden_array contains information, that are needed to rebuild the web page after the user was entering the password.
		*/	
		public function popupPassword($page,$text,$hidden_array){
			echo"
				<div class='popupbackground'>
				<div>
					$text
					<form action='$page' method='post'>
						<input type='password' name='password' autocomplete='off'>
						";
						foreach($hidden_array AS $key=>$value){
							echo"<input type='hidden' name='$key' value='$value'>";
						}
						echo"
						<input type='submit' value='submit'>
					</form>
				</div>
				</div>";
		}

		/*
		## This function displays a message like a warning or an error as a popup window.
		## The variable which is sent when calling this function contains this message.
		*/
		public function messagebox($message){
			echo "<script language=\"JavaScript\">
				<!--
				alert(\"$message\");
				//-->
				</script>
				";
		}

		/*
		## This function inquires the password of a specific department from the database and returns it.
		## The inquired password is used to compare it with the password, a user entered in a password request.
		## The variable which is sent when calling this function contains the specific department's name.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function passwordrequest($department){
			global $link;
			$query="SELECT password FROM departments WHERE Department like '$department'";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			$password=$object->password;
			return $password;
		}

		/*
		## This function displays a notice to the user, that he entered a wrong password.
		## The user is asked to enter the password again.
		## If the user cancels that request, he is automatically forwarded to the landing page (index.php).
		*/
		public function wrongpassword(){
			echo'<script type="text/JavaScript">;
						if(! window.confirm("Your password is wrong. Try again?")){
							window.location.href="index.php";
						}
					</script>';
		}

		/*
		## This function creates HTML commands for the header of each PDF-file, which can be generated within the system.
		## Amongst others, this function provides the HTML head for the PDF file's generation.
		## The variable which is sent when calling this function contains styling commands.		
		## The information of the PDF header contain a facility's name, logo, region and adress as well as contact information.
		## All these information are defined in Defaults.php, which is included by setup.php
		## The HTML commands are buffered in variable $html to be displayed later.
		## The function returns the HTML buffer.
		## The variables which are sent when calling this function contain
		##		- the commands for styling that pdf,
		## 		- some information, which are displayed on the right hand side in the PDF's header (e.g. patient's information in patient's visit summary).
		*/
		public function pdf_header($style,$rightcolumn){
			global $FACILITY;
			global $PHONE_NUMBERS;
			global $ADDRESS;
			global $REGION;
			global $LOGO;
			
			$facility = "
					<h2>$FACILITY</h2>
					<b>Tel:</b>
					";
			foreach($PHONE_NUMBERS AS $phone_number){
				$facility.="<br> $phone_number";
			}
			$facility.="
							<br><b>Adress:</b>
							<br> $ADDRESS
							<br> $REGION
							";
			$html='
					<head>
						<style>
							'.$style.'
						</style>
					</head>
					<body>
						<table cellpadding="5" cellspacing="0" style="width: 100%">
							<tr>
								<td style="border:none;text-align:left">
									'.$facility.'
								</td>
								<td style="text-align:center;border:none">
									<img src="'.$LOGO.'"  width="100px">
								</td>
								<td style="text-align: right;border:none">
									'.$rightcolumn.'
								 </td>
							 </tr>
						</table>';
			return $html;
		}
		
		/*
		## This function generates a PDF file
		## The variables which are sent when calling this function have the following meaning:
		##		- variable $pdfName contains the PDF file, which is going to printed,
		##		- variable $size contains the size of PDF file (e.g. A4),
		##		- variable $html contains the content, which is going to be printed within the PDF file.
		## To generate the PDF file, an external library is used, which can be found in the folder 'PDF' and
		## which is included below ('require_once('Style/PDF/tcpdf.php')').
		*/
		public function pdf_execute($pdfName,$size,$html){
			$html.='</body>';

			$html=str_replace('<h4>','<b>',$html);
			$html=str_replace('</h4>','</b>',$html);

			$html=str_replace('<span class="tooltiptext">','<font style="color:lightgrey">(',$html);
			$html=str_replace('</span>',')</font>',$html);

			global $FACILITY;
			
			require_once('Style/PDF/tcpdf.php');


			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $size, true, 'UTF-8', false);


			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor($FACILITY);
			$pdf->SetTitle($pdfName);
			$pdf->SetSubject($pdfName);

			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			$pdf->SetFont('times', '', 12);

			$pdf->AddPage();

			$pdf->writeHTML($html, true, false, true, false, '');
			ob_clean();
			$pdf->Output($pdfName, 'I');
			
		}

		/*
		## This function is used to create a new patient number for a certain department.
		## The variables which are sent when calling this function have the following meaning:
		##		- variable $department contains the department's name, for which a new number is going to generate,
		##		- variable $specification is only used for Maternity. It is set like "serial_number", if a new serial number is needed.
		## The new number is calculated as a combination of an increasing number and the current year.
		## The last patient's number is saved in notice of each department. 
		## The calculated new number is returned.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function new_number($department,$specification){
			global $link;
			global $OPD_FORMAT;
			$query="SELECT notice FROM departments WHERE Department like '$department'";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			$last_number=$object->notice;
			
			/*
			## Special case for a Maternity's patient, which has two numbers: a registration number and a serial number.
			## The serial number is defined by the facility and continuous.
			## The registration number is not in all cases continuous and not in all cases unique.
			## Depending on the content of variable $specification, either the last known serial number or the last known registration number is identified.
			*/
			if($department=='Maternity'){
				$number_array=explode(',',$last_number);
				if($specification=='serial_number'){
					$last_number=$number_array[0];
				}else{
					$last_number=$number_array[1];
				}
			}
			
			if($department=='OPD'){
				$form_array=explode("[//]",$OPD_FORMAT);
			}else{
				$form_array=explode("[//]","[0-9]{1,}[//]{1}[0-9]{2}");
			}
			
			## Convert the last number into an array.
			$last_array=explode("/",$last_number);

			$first=true;
			$year=date("y",time());
			foreach($form_array AS $key=>$component){
				if($first){
					$new_number='';
					$first=false;
				}else{
					$new_number.='/';
				}
				if(strstr($component,'[0-9]') AND $key<(count($form_array)-1)){
					if($last_array[count($form_array)-1]!==$year){
						$new_number.=1;
					}else{
						$new_number.=$last_array[$key]+1;
					}
					
				}else if($key==(count($form_array)-1)){
					$new_number.=$year;
				}else{
					$new_number.=$last_array[$key];
				}
			}
			
			return $new_number;
		}

		/*
		## This function is used to update a department's notice in the database by a new patient's number.
		## The variables which are sent when calling this function have the following meaning:
		##		- variable $department contains the department's name, for which the notice is going to update,
		##		- variable $number contains the new patient's number,
		##		- variable $specification is only used for Maternity. It is set like 'serial_number', if the new patient's number, which is going to update in database, is a serial number.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function set_new_number($department,$number,$specification){
			global $link;
			if($department=='Maternity'){
				$query="SELECT notice FROM departments WHERE Department like '$department'";
				$result=mysqli_query($link,$query);
				$object=mysqli_fetch_object($result);
				$last_number=$object->notice;
				$number_array=explode(',',$last_number);
				
				/*
				## Special case for a Maternity's patient, which has two numbers: a registration number and a serial number.
				## The serial number is defined by the facility and continued.
				## The registration number is not in all cases continued and not in all cases unique.
				## Depending on the content of variable $specification, either the serial number or the registration number is updated. The other number is not going to be changed.
				*/
				if($specification=='serial_number'){
					$number_array[0]=$number;
				}else{
					$number_array[1]=$number;
				}
				$number=implode(',',$number_array);
			}
			$query="UPDATE departments SET notice='$number' WHERE Department like '$department'";
			mysqli_query($link,$query);
		}	

		
		/*
		## This function displays a popup window, in which the user has to confirm its intention to delete something.
		## The variable which is sent when calling this function contains the name of the item, which is going to be deleted.
		## If the user cancels its intention, it is automatically forwarded to the web page 'Settings'.
		## This function only displays the confirmation popup. It does not delete any item.
		*/
		public function delete_request($item){
			global $thispage;
			echo'<script type="text/JavaScript">;
						if(window.confirm("Are you sure you want to delete '.$item.' and all its data?")){
							window.location.href="'.$thispage.'&delete=on";
						}
					</script>';
		}

		public function getColours(){
			global $link;

			## Initialise variable with the PC's IP address.
			$IP= $_SERVER['REMOTE_ADDR'];

			/*
			## Get data from database.
			## Get the department for which the user's PC is entered.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			*/
			$query="SELECT * FROM departments WHERE IP like '%$IP%'";
			$result=mysqli_query($link,$query);

			/*
			## In case the user's PC is entered for any department, call the corresponding colour profile, 
			## otherwise retrieve the default value entered in the active column of the colours database table.
			*/
			if(mysqli_num_rows($result)!==0){
				$colour=mysqli_fetch_object($result)->colour;
				$query="SELECT * FROM colours WHERE layoutname like '$colour'";
				
			}else{
				$query="SELECT * FROM colours WHERE active=1";
			}

			$result=mysqli_query($link,$query);
			$colours=mysqli_fetch_object($result);

			return $colours;
		}

		/*
		public function RgbToHex($rgb){
			$rgb_array=explode(',',$rgb);
			$hex=sprintf('%02x%02x%02x',$rgb_array[0],$rgb_array[1],$rgb_array[2]);
			return $hex;
		}
		*/
	}

?>
