<?php

include('config.php');
$user = $_POST['user'];

if ($user) {
    $filename = $file_path . '/' . $user . '.txt';
    file_put_contents($filename, '');
}

?>