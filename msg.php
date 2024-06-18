<?php
$data = [ 
'Sender' => 'IT-Abteilung', 
'Message' => 'Achtung das ist noch ein TEST <b>fettgedruckt</b> geht auch<br>Hier noch weiterer Inhalt<br><h2>Unterüberschrift</h2>Hier noch weiterer Inhalt<br>',
'Active'=> true];
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8;');
echo json_encode($data);
