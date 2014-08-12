<?php
$host = "mysql5-8.60gp";
$db_name = "cumulussacharn";
$username = "cumulussacharn";
$password = "kevinouu";

try {
	$con = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
	$con->exec("SET CHARACTER SET utf8");
} catch(PDOException $exception){ //to handle connection error
	echo "Erreur de connexion : " . $exception->getMessage();
}
?>