<?php
session_start();


define('DB_SERVER', 'localhost');
define('DB_login', 'natalia_user'); 
define('DB_haslo', 'BazyNatalia5.'); 
define('DB_NAME', 'nataliaba'); 

$conn = new mysqli(DB_SERVER, DB_login, DB_haslo, DB_NAME);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

define('haslo_REGEX', '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d\s]).{8,}$/');
define('haslo_REQUIREMENTS', 'Hasło musi mieć co najmniej 8 znaków, zawierać dużą i małą literę, cyfrę oraz znak specjalny.');
?>