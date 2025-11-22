<?php
session_start();
include 'config.php';

function checkLogin($data) {
    global $conn;
    
    $username = $data['username'];
    $password = md5($data['password']); 

    $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['user'] = $user['nama'];
        
        $_SESSION['role'] = ($user['role'] == '1') ? 'Admin' : 'User'; 

        header("Location: index.php");
        exit;
    } else {
        return "Username atau Password salah!";
    }
}
?>