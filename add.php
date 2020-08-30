<?php

include('config.php');
$task = $_POST['task'];
$user = $_POST['user'];

if ($task && $user) {
    $filename = $file_path . '/' . $user . '.txt';
    $line = $task . ",no\n";
    file_put_contents($filename, $line, FILE_APPEND);
}

?>