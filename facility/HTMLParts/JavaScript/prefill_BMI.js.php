<script type="text/javascript">

    // This function is used to calculate and prefill the amount of drugs from the dosage recommendation when prescribing them.
    function PrefillBMI(protocol_ID) {
        var height=document.getElementsByName('height')[0].value/100;
        var weight=document.getElementsByName('weight')[0].value;

        var BMI=(weight/(height*height)).toFixed(2);

        document.getElementById('BMI').innerHTML= BMI;
        document.getElementById('BMIflag').style.display = "block";

        $.ajax({

            // Name and place of the php script to include.
            url : "Functions/prefill_BMI.php",

            // Method of sending data to the php script and the URL of this page which can be retrieved in the php file as $_POST['thispage'].
            type: "POST",
            data: {protocol_ID: protocol_ID, BMI: BMI},

            success:function(data){
                var classification =data;
                

                document.getElementById('BMIflag').innerHTML=classification;
            }
        })
    }
</script>