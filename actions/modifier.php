<?php
include 'connexion_db.php';
include 'fonction_nettoyage.php';

try {
	$query = "UPDATE remarques SET reponse = :reponse, position = :position, sujet = :sujet, auteur = :auteur, texte = :texte, notes = :notes, images = :images, fichiers = :fichiers, date = :date WHERE id = :id";
	$stmt = $con->prepare($query);
	$stmt->bindParam(':reponse', $_POST['reponse']);
	$stmt->bindParam(':position', $_POST['position']);
	$stmt->bindParam(':auteur', $_POST['auteur']);
	$baseTexte = $_POST['texte'];
	$baseTexte = nettoyage($baseTexte);
	$stmt->bindParam(':texte', $baseTexte);
	$baseNote = $_POST['notes'];
	$baseNote = nettoyage($baseNote);
	$stmt->bindParam(':notes', $baseNote);
	$stmt->bindParam(':images', $_POST['images']);
	$stmt->bindParam(':fichiers', $_POST['fichiers']);
	$stmt->bindParam(':id', $_POST['id']);
	$stmt->bindParam(':sujet', $_POST['sujet']);

	$date = new DateTime();
	$stmt->bindValue(':date', $date->getTimestamp());


	if ($stmt->execute()) {
		echo "Remarque modifiée.";
	} else {
		echo "Impossible de modifier la remarque.";
	}

	$id_base = $_POST['id']; 

	////// VERSIONING

	$auteur_courant = $_POST['auteur'];
	if (is_numeric($auteur_courant)) {
		$auteur_courant = intval($auteur_courant);
		$query_a = "SELECT nom FROM utilisateurs WHERE id = ?";
		$stmt_a = $con -> prepare($query_a);        
		$stmt_a->bindParam(1, $auteur_courant);
    	$stmt_a -> execute();
    	$row_a = $stmt_a->fetch(PDO::FETCH_ASSOC);
		$auteur_texte = $row_a['nom'];
    } else {
    	$auteur_texte = $auteur_courant;
    }

	$query_s = "SELECT sujet FROM sujets WHERE id = ?";
	$stmt_s = $con -> prepare($query_s);
	$sujet_courant = $_POST['sujet'];
	$sujet_courant = intval($sujet_courant);
	$stmt_s->bindParam(1, $sujet_courant);
    $stmt_s -> execute();
    $row_s = $stmt_s->fetch(PDO::FETCH_ASSOC);
	$sujet_texte = $row_s['sujet'];


	$query_archive = "INSERT INTO archivage_remarques SET auteur_texte = :auteur_texte, sujet_texte = :sujet_texte, id_base = :id_base, reponse = :reponse, position = :position, sujet = :sujet,  auteur = :auteur, texte = :texte, notes = :notes, images = :images, fichiers = :fichiers, date = :date";
	$stmt_archive = $con->prepare($query_archive);

	$stmt_archive->bindParam(':reponse', $_POST['reponse']);
	$stmt_archive->bindParam(':position', $_POST['position']);
	$stmt_archive->bindParam(':auteur', $_POST['auteur']);
	$baseTexte = $_POST['texte'];
	$baseTexte = nettoyage($baseTexte);
	$stmt_archive->bindParam(':texte', $baseTexte);
	$baseNote = $_POST['notes'];
	$baseNote = nettoyage($baseNote);
	$stmt_archive->bindParam(':notes', $baseNote);
	$stmt_archive->bindParam(':images', $_POST['images']);
	$stmt_archive->bindParam(':fichiers', $_POST['fichiers']);
	$stmt_archive->bindParam(':sujet', $_POST['sujet']);
	$stmt_archive->bindValue(':date', $date->getTimestamp());


	$stmt_archive->bindValue(':id_base', intval($id_base));
	$stmt_archive->bindValue(':auteur_texte', $auteur_texte);
	$stmt_archive->bindValue(':sujet_texte', $sujet_texte);


	if ($stmt_archive->execute()){
		echo "Remaque archivée pour versioning";
	} else {
		echo "Impossible d'archiver la remarque";
	}
}
catch(PDOException $exception) {
	echo "Erreur : " . $exception->getMessage();
}
?>

