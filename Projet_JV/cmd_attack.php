<?php
$code = $_POST["code"];
$puzzle_id = $_POST["puzzle_id"];
$spell = $_POST["spell"];

// Nettoyage
$code = preg_replace('/^\xEF\xBB\xBF/', '', $code);
$lines = explode("\n", $code);
while (count($lines) && trim($lines[0]) === "") array_shift($lines);
$code = implode("\n", $lines);

// Fichier temporaire unique par sort
$player_file = "player_" . $spell . ".py";
file_put_contents($player_file, $code);

// Appel du script Python générique
$python = "C:/wamp64/www/Projet_JV/venv/Scripts/python.exe";
$script = "C:/wamp64/www/Projet_JV/check_attack.py";

$cmd = "\"$python\" \"$script\" $player_file $puzzle_id $spell";
$output = shell_exec($cmd);

echo $output;
?>