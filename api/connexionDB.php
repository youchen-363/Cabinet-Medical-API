<?php 
try {
    $linkpdo = new PDO("mysql:host=mysql-ycvcabinetapi.alwaysdata.net;dbname=ycvcabinetapi_db;", '350734', 'V6nk2TjnBGxM679');
}
// Capture des erreurs éventuelles
    catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

?>