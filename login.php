<?php

include('config.php');

$username = $_POST['username'];
$password = $_POST['pw'];

$data = file_get_contents($file_path . '/users.txt');
$users = explode("\n", $data);
$matched = false;

for ($i = 0; $i <= sizeof($users); $i++) {
    $split_data = explode(",", $users[$i]);
    if ($username == $split_data[0] && $password == $split_data[1]) {
        $matched = true;
        setcookie('user', $username);
        header('Location: index.php');
        break;
    }
}

if (!$matched) {
    header('Location: index.php?login=failed');
}

?>