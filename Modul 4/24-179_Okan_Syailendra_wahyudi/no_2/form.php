<?php
require 'validate.inc';
$errors = array();
$old = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['surname'])) {

    foreach ($_POST as $k => $v) $old[$k] = is_array($v) ? $v : trim($v);

    validateName($errors, $_POST, 'surname');

    if ($errors) {
        echo '<h3>Invalid, correct the following errors:</h3>';
        echo '<ul>';
        foreach ($errors as $field => $error) {
            echo "<li>" . htmlspecialchars("$field: $error") . "</li>";
        }
        echo '</ul>';
        include 'form.inc';
    } else {
        echo '<h2>Form submitted successfully with no errors</h2>';
        echo '<p>Data:</p><ul>';
        foreach ($_POST as $k => $v) {
            $val = is_array($v) ? implode(', ', $v) : htmlspecialchars($v);
            echo "<li><b>$k</b>: $val</li>";
        }
        echo '</ul>';
    }
} else {
    include 'form.inc';
}
