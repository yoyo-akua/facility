<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");
	
	## Print headline, icon, information, contact and link buttons to the user and administrator manual.
	echo'
			<h1>Help</h1>
			<div style="border-top:1px solid DarkGray; border-bottom:1px solid DarkGray;padding:30px;width:30%;margin-bottom:20px">
			<div style="display:inline;float:left;margin-right:20px;"><i class="fas fa-question fa-10x"></i></div>
					<br>The following manuals should help you with any problem you might be facing. <br>
					If they can\'t be solved after checking in the manual, please contact the creator of this application:<br><br>
				<h3>Jojo\'s Contact:</h3>
					<h4>WhatsApp:</h4> +49 1520 5963525<br>
					<h4>E-Mail:</h4> jorinde.schroeter@gmx.de
			</div>
			
			<a href="../manual/UserManual.pdf"><div class ="box">user manual</div></a>
			<a href="../manual/AdministrationManual.pdf"><div class ="box">administration manual</div></a>
			';
	
	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
?>