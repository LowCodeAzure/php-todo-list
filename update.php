<?php

include('config.php');
$task = $_POST['task'];
$strike = $_POST['strike'];
$user = $_POST['user'];

if ($task && $strike && $user) {
    $filename = $file_path . '/' . $user . '.txt';
    $replace = $task . "," . $strike . "\n";

    $new_data = '';

    $data = file_get_contents($filename);
    $lines = explode("\n", $data);

    for ($i = 0; $i < sizeof($lines); $i++) {
        $line = $lines[$i];
        if ($task === explode(",", $line)[0]) {
            $new_data = $new_data . $replace;
        } else {
            $new_data = $new_data . $line . "\n";
        }
    }

    // src: https://stackoverflow.com/questions/709669
    $new_data = implode("\n", array_filter(explode("\n", $new_data)));
    $new_data = $new_data . "\n";

    file_put_contents($filename, $new_data);

}

?>