<?php 
//Konfiguracija baze podataka   
$dbHost     = "localhost";   
$dbUsername = "root";   
$dbPassword = "";   
$dbName     = "edukacija";    
//Kreiranje objekta za povezivanje sa bazom podataka  
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
 
 
 
//Provjera veze sa bazom podataka 
if ($db->connect_error) {   
    die("Nema konekcije. " . $db->connect_error);   
}