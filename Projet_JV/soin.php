<?php
$python = "C:/wamp64/www/Projet_JV/venv/Scripts/python.exe";
$script = "C:/wamp64/www/Projet_JV/hasard.py";
$cmd = "\"$python\" \"$script\" hp run";
$output = shell_exec($cmd);
file_put_contents("history_soin.txt", $output);
echo $output;
?>