<?php
require_once "Formulator.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>Formulator Example</title>
</head>
<body>
<script>
function blurred() {
	alert("Formulator set my onblur function!");	
}
</script>

<?php
//Create (or load in) your JSON-based form
$formdata = '{
    "action": "formtest.php",
    "method": "GET",
    "inputs": [
        {
            "label": {
                "content": "Name: ",
                "class": "inputLabel"
            },
            "id": "name",
            "name": "name",
            "type": "text",
            "size": "50",
            "onblur": "blurred();"
        },
        {
            "label": {
                "content": "Password: ",
                "class": "inputLabel"
            },
            "id": "password",
            "name": "password",
            "type": "password",
            "size": "25"
        },
        {
            "id": "submit",
            "name": "submit",
            "type": "submit",
            "value": "Submit"
        }
    ]
}';

//create the form
$formulator = new Formulator();
echo $formulator->formify( $formdata );

?>

</body>
</html>
