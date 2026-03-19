<?php
$code = $_POST["code"];

file_put_contents("player_fireball.py", $code);

$python = "C:/wamp64/www/Projet_JV/venv/Scripts/python.exe";
$script = "C:/wamp64/www/Projet_JV/check_fireball.py";

$cmd = "\"$python\" \"$script\" player_fireball.py";
$output = shell_exec($cmd);

echo $output;
?>