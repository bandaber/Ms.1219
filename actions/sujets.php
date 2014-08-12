<?php
include 'connexion_db.php';
include 'fonction_nettoyage.php';

try {
    if(isset($_POST['sujets'])) {
        $sujets = $_POST['sujets'];
        $ids = $_POST['ids'];

        $stmt_supr = $con->prepare("DELETE FROM sujets");
        $stmt_supr->execute();

        $sujetsTab = explode("*", $sujets);
        $idsTab = explode("*", $ids);
        $compteur = 0;

        $requete = "INSERT INTO sujets (id, sujet) VALUES";
        foreach ($sujetsTab as $sujet) {
            if ($sujet != "") {
                $requete .= "(".$idsTab[$compteur].",'".nettoyage($sujet)."'),";
                $compteur++;
            }
        }
        $requete = substr($requete, 0, -1);
        echo $requete;
     

        $stmt = $con->prepare($requete);
        if ($stmt->execute()) {
            echo "Sujets modifiés.";
        } else {
            echo "Impossible de modifier les sujets.";
        }
    
    }
}
catch(PDOException $exception) {
    echo "Erreur : " . $exception->getMessage();
}
?>

