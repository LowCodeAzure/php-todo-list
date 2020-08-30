<?php

include('config.php');
$filename = $file_path . '/users.txt';

if (isset($_POST['username']) && isset($_POST['pw'])) {
    $username = $_POST['username'];
    $password = $_POST['pw'];

    if (!ctype_alnum($username)) {
        header('Location: index.php?username=invalid');
        return;
    }

    $data = file_get_contents($filename);
    $users = explode("\n", $data);

    $valid = true;

    for ($i = 0; $i <= sizeof($users); $i++) {
        if ($users[$i]) {
            $info = explode(",", $users[$i]);
            if ($username == $info[0]) {
                header('Location: index.php?signup=failed');
                $valid = false;
                break;
            }
        }
    }

    if ($valid) {
        $line = $username . "," . $password . "\n";
        file_put_contents($filename, $line, FILE_APPEND);
        $new_file = $file_path . '/' . $username . '.txt';
        $fp = fopen($new_file, 'w');
        fwrite($fp, '');
        fclose($fp);
        chmod($new_file, 0777);
        
        setcookie('user', $username);
        header('Location: index.php');
    } else {
        header('Location: index.php?signup=failed');
    }
}

?>