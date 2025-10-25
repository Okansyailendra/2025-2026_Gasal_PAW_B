<?php
function old($key, $default = '') {
    global $old;
    if (!isset($old)) return $default;
    return isset($old[$key]) ? htmlspecialchars($old[$key]) : $default;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Form Self-Submission</title></head>
<body>
  <h2>Form Input Data Pribadi</h2>
  <form action="form.php" method="POST">
    <label>Surname:</label><br>
    <input type="text" name="surname" value="<?php echo old('surname'); ?>"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo old('email'); ?>"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" value=""><br><br>

    <input type="submit" value="Submit">
  </form>
</body>
</html>
