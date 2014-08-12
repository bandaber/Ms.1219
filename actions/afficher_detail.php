<?php

try {
    include 'connexion_db.php';

    ///////// REQUETE TOUS LES AUTEURS

    $query_a = "SELECT * FROM utilisateurs ORDER BY id asc";
    $stmt_a = $con -> prepare($query_a);
    $stmt_a -> execute();
    $auteurs = array();
    while ($row_a = $stmt_a->fetch(PDO::FETCH_ASSOC)){
        array_push($auteurs, $row_a['nom']);
    }


 
    $sujet_sql = $_POST['sujet'];
    $sujet_sql = intval($sujet_sql);
    $reponses = array();

    ///////// CHERCHER TOUTES LES REPONSES CORRESPONDANT À UN SUJET

    $query_r = "SELECT * FROM remarques WHERE sujet = ? AND reponse != 0 ORDER BY date ASC";
    $stmt_r = $con->prepare($query_r);
    $stmt_r->bindParam(1,  $sujet_sql);
    if ($stmt_r->execute()) {
        while ($row_r = $stmt_r->fetch(PDO::FETCH_ASSOC)) {

            if (is_numeric($row_r['auteur'])) {
                $auteur_courant = $auteurs[intval($row_r['auteur'])];
            } else {
                $auteur_courant = $row_r['auteur'];
            }

            
            $date_lisible = date("d.m.y", $row_r['date']);
            //$date_lisible = date("d.m.y - H:i:s", $row_r['date']);

            $reponses[]= array(
                'id' => $row_r['id'],
                'reponse' => $row_r['reponse'],
                'auteur' => $auteur_courant,
                'texte' => $row_r['texte'],
                'sujet' => intval($row_r['sujet']),
                'nombrePoints' => substr_count($row_r['position'], 'Point'),
                'nombreZones' => substr_count($row_r['position'], 'Polygon'),
                'notes' => $row_r['notes'],
                'images' => $row_r['images'],
                'fichiers' => $row_r['fichiers'],
                'date' => $date_lisible,
                'date_tri' => $row_r['date']
            );
        }
    } 

    ///////// CHERCHER TOUS LES COMMENTAIRES CORREPONDANT À UN SUJET

    $query = "SELECT * FROM remarques WHERE sujet = ? AND reponse = 0 ORDER BY date ASC";
    $stmt = $con->prepare($query);
    $stmt->bindParam(1, $sujet_sql);
    if ($stmt->execute()) {
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if (is_numeric($row['auteur'])) {
                $auteur_courant = $auteurs[intval($row['auteur'])];
            } else {
                $auteur_courant = $row['auteur'];
            }

            $date_lisible = date("d.m.y", $row['date']);
           // $date_lisible = date("d.m.y - H:i:s", $row['date']);
            $affichage_reponses = array();

            $date_tri = $row['date'];

            if ($reponses != array()) {
                foreach ($reponses as $reponse) {
                    if ($reponse['reponse'] == $row['id']) {
                        array_push($affichage_reponses, $reponse);
                        if ($date_tri < $reponse['date_tri']) {
                            $date_tri = $reponse['date_tri'];
                        }
                    }
                }
            } 

            $json[]= array(
                'id' => $row['id'],
                'reponses' => $affichage_reponses,
                'auteur' => $auteur_courant,
                'texte' => $row['texte'],
                'sujet' => intval($row['sujet']),
                'nombrePoints' => substr_count($row['position'], 'Point'),
                'nombreZones' => substr_count($row['position'], 'Polygon'),
                'notes' => $row['notes'],
                'images' => $row['images'],
                'fichiers' => $row['fichiers'],
                'date' => $date_lisible,
                'date_tri' => $date_tri
            );
        }

        foreach ($json as $key => $row) {
            $date_tri_tab[$key] = $row['date_tri'];
        }
        array_multisort($date_tri_tab, SORT_DESC, $json);
    }



     ///////// ENVOI

   // echo json_encode($reponses);
    //$jsonstring = json_encode($json);
    //echo var_dump($reponses);
    echo json_encode($json);
}

catch (PDOException $exception){
    echo "Erreur : " . $exception->getMessage();
}


?>





