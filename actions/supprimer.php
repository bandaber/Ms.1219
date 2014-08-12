<?php
include 'connexion_db.php';

try {
	$query = "DELETE FROM remarques WHERE id = ?";
	$stmt = $con->prepare($query);
	$stmt->bindParam(1, $_POST['id']);
	
	if ($stmt->execute()){
		echo "Remarque supprimée";
	} else {
		echo "Impossible de supprimer la remarque";
	}
}

catch(PDOException $exception){
	echo "Erreur : " . $exception->getMessage();
}
?>