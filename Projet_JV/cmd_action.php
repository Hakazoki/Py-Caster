<?php
$python = "C:/wamp64/www/Projet_JV/venv/Scripts/python.exe";
$script = "C:/wamp64/www/Projet_JV/hasard.py";
$cmd = "\"$python\" \"$script\" degat run";
$output = shell_exec($cmd);
file_put_contents("history_degat.txt", $output);
echo $output;
?>