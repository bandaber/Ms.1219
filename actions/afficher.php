<?php
include 'connexion_db.php';

$query = "SELECT id, position, sujet FROM remarques ORDER BY date DESC";
$stmt = $con->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();
if ($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $remarques[] = array(
            'id' => $row['id'],
            'position' => $row['position'],
            'sujet' => $row['sujet'],
        );
    }

}



$query_s = "SELECT * FROM sujets ORDER BY id ASC";
$stmt_s = $con -> prepare($query_s);
$stmt_s -> execute();



while ($row_s = $stmt_s->fetch(PDO::FETCH_ASSOC)) {

    $remarques_print = array();
    foreach($remarques as $remarque) {
        if ($row_s['id'] == $remarque['sujet']) {
            array_push($remarques_print, $remarque);
        }
    }

    $json[]= array(
        'id' => $row_s['id'],
        'sujet' => $row_s['sujet'],
        'remarques' => $remarques_print
    );
}


$jsonstring = json_encode($json);
echo $jsonstring;

?>


