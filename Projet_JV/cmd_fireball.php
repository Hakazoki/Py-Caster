<?php
// 1. Récupération des données envoyées
$code = $_POST["code"];
$puzzle_id = $_POST["puzzle_id"];

// 2. Suppression du BOM UTF‑8 éventuel
$code = preg_replace('/^\xEF\xBB\xBF/', '', $code);

// 3. Suppression des lignes vides AVANT le def compute()
$lines = explode("\n", $code);
while (count($lines) && trim($lines[0]) === "") {
    array_shift($lines);
}
$code = implode("\n", $lines);

// 4. Écriture du fichier Python propre
file_put_contents("player_fireball.py", $code);

// 5. Exécution du script Python
$python = "C:/wamp64/www/Projet_JV/venv/Scripts/python.exe";
$script = "C:/wamp64/www/Projet_JV/check_fireball.py";

$cmd = "\"$python\" \"$script\" player_fireball.py $puzzle_id";
$output = shell_exec($cmd);

// 6. Retour du résultat au terminal JS
echo $output;
?>