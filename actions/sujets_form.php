<?php
try {
    include 'connexion_db.php';

    $query_s = "SELECT sujet FROM sujets ORDER BY id ASC";
    $stmt_s = $con -> prepare($query_s);
    $stmt_s -> execute();
    $num_s = $stmt_s -> rowCount();
    $sujets = array();
    
    while ($row_s = $stmt_s->fetch(PDO::FETCH_ASSOC)){
        array_push($sujets, $row_s['sujet']);
    }
}

catch (PDOException $exception){
    echo "Erreur : " . $exception->getMessage();
}

?>
<div class='filetHaut'></div>
<form id='ajouterSujetForm' action='#' method='post' border='0'>
    <fieldset>
        <input type='hidden' name='sujets' value='' />
        <input type='hidden' name='ids' value='' />
        <?php
            for ($i=0; $i<$num_s; $i++) {
                echo '<div class="editionSujet">';
                echo '<input type="text" class="inputSujet" data-id="'.($i+1).'" value="'.$sujets[$i].'" placeholder="(Saisir le sujet)" />';
                echo '<div class="supprimerSujet"><img src="images/icone-supprimer3.gif" /></div>';
                echo '</div>';
            }
        ?>
    </fieldset>
    <div id='ajouterSujet'><img src='images/plus.gif' /></div>
    <input type='submit' id='boutonAjouter' value='Enregistrer' class='bouton' form="ajouterSujetForm" />  
</form>

<script type="text/javascript">

$(document).ready(function(){
    $("#ajouterSujet").click(function() {
        $("fieldset").append('<div class="editionSujet"><input type="text" class="inputSujet" value="" placeholder="(Saisir le sujet)" /><div class="supprimerSujet"><img src="images/icone-supprimer3.gif" /></div>');
    });

    $(document).on('click', '.supprimerSujet', function(e) {
         if(e.handled !== true) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce sujet ?')) {
                $(this).parent().remove();
            }
            e.handled = true;
        }

        
    });

});

</script>






