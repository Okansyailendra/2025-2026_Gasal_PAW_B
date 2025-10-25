<?php
 require 'validate.inc';
$errors = array(); validateName($errors, $_POST, 'surname');
if ($errors)
{ echo 'Errors: surname tidak valid!<br/>';
foreach ($errors as $field => $error)
echo "$field $error</br>";
} else echo
'Data OK!';

?>
<form method="post" action="processData.php">
 Surname: <input type="text" name="surname">
 <input type="submit" value="Submit">