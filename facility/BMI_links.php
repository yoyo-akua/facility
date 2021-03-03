<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
    include("HTMLParts/HTML_HEAD.php");
?>    
<h1>BMI classification sources</h1>
<h4>All values have been retrieved from the official website of the WHO. To see the complete lists, click on the corresponding link.</h4>
<table>
    <tr>
        <th style='border:none'></th>
        <th colspan='4' style='border-left:none'>sex</th>
    </tr>
    <tr>
        <th style='border-left:none'>age</th>
        <th colspan='2'>male</th>
        <th colspan='2'>female</th>
    </tr>
    <tr>
        <td style='border-left:none'>0-13 weeks</td>
        <td colspan='2'><a href='BMI/boys/BMI_boys_0_13_zscores.pdf'>table</a></td>
        <td colspan='2'><a href='BMI/girls/BMI_girls_0_13_zscores.pdf'>table</a></td>
    </tr>
    <tr>
        <td style='border-left:none'>0-2 years</td>
        <td><a href='BMI/boys/BMI_boys_0_2_zscores.pdf'>table</a></td>
        <td><a href='BMI/boys/cht_bfa_boys_z_0_2.pdf'>chart</a></td>
        <td><a href='BMI/girls/bmi_girls_0_2_zscores.pdf'>table</a></td>
        <td><a href='BMI/girls/cht_bfa_girls_z_0_2.pdf'>chart</a></td>
    </tr>
    <tr>
        <td style='border-left:none'>2-5 years</td>
        <td><a href='BMI/boys/BMI_boys_2_5_zscores.pdf'>table</a></td>
        <td><a href='BMI/boys/cht_bfa_boys_z_2_5.pdf'>chart</a></td>
        <td><a href='BMI/girls/BMI_girls_2_5_zscores.pdf'>table</a></td>
        <td><a href='BMI/girls/cht_bfa_girls_z_2_5.pdf'>chart</a></td>
    </tr>
    <tr>
        <td style='border-left:none'>5-19 years</td>
        <td><a href='BMI/boys/bmifa_boys_5_19years_z.pdf'>table</a></td>
        <td><a href='BMI/boys/bmifa_boys_z_5_19_labels.pdf'>chart</a></td>
        <td><a href='BMI/girls/bmifa_girls_5_19years_z.pdf'>table</a></td>
        <td><a href='BMI/girls/bmifa_girls_z_5_19_labels.pdf'>chart</a></td>
    </tr>
    <tr>
        <td rowspan='2' style='border-left:none'>adults</td>
        <td colspan='4'><a href='BMI/BMI_adults.png'>table</a></td>
    </tr>
    <tr>
        <td colspan='4'><a href='BMI/BMI_chart_adults.png'>chart</a></td>
    </tr>

</table>

<?php
    ## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
?>
    