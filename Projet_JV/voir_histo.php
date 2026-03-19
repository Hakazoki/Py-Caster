<?php
$historyFile_1 = "history_degat.txt";
$historyFile_2 = "history_soin.txt";
$degat = file_exists($historyFile_1) ? file_get_contents($historyFile_1) : "Aucun dégât enregistré.";
$soin = file_exists($historyFile_2) ? file_get_contents($historyFile_2) : "Aucun soin enregistré.";
echo "Dernier dégât : " . $degat;
echo "Dernier soin : " . $soin;
?>