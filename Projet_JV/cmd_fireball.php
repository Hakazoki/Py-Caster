<?php
$code = $_POST["code"];
$puzzle_id = $_POST["puzzle_id"];

file_put_contents("player_fireball.py", $code);

$python = "C:/wamp64/www/Projet_JV/venv/Scripts/python.exe";
$script = "C:/wamp64/www/Projet_JV/check_fireball.py";

$cmd = "\"$python\" \"$script\" player_fireball.py $puzzle_id";
$output = shell_exec($cmd);

echo $output;
?>