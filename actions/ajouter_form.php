<?php
try {
    include 'connexion_db.php';

    $query_a = "SELECT nom FROM utilisateurs ORDER BY id asc";
    $stmt_a = $con -> prepare($query_a);
    $stmt_a -> execute();
    $num_a = $stmt_a -> rowCount();
    $auteurs = array();
    
    while ($row_a = $stmt_a->fetch(PDO::FETCH_ASSOC)){
        array_push($auteurs, $row_a['nom']);
    }

    $query_s = "SELECT * FROM sujets ORDER BY id asc";
    $stmt_s = $con -> prepare($query_s);
    $stmt_s -> execute();
    $num_s = $stmt_s -> rowCount();
    $sujets = array();
    $IDsujets = array();
    
    while ($row_s = $stmt_s->fetch(PDO::FETCH_ASSOC)){
        array_push($sujets, $row_s['sujet']);
        array_push($IDsujets, $row_s['id']);
    }

    
    if (isset($_REQUEST['remarque_id'])) {
        $reponse = $_REQUEST['remarque_id'];

        $query = "SELECT sujet FROM remarques WHERE id = ? LIMIT 1";
        $stmt = $con->prepare($query);
        $stmt->bindParam(1, $reponse);
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $sujet_reponse =  $row['sujet'];
        }
    } else {
        $reponse = '';
    }
}

catch (PDOException $exception){
    echo "Erreur : " . $exception->getMessage();
}

?>
<div class='filetHaut'></div>
<div class='editionCarteGauche'>
<div class='btmarqueur boutonIconeCont'><img src="images/lieu2.gif" /><div class="boutonIcone">Ajouter un point</div></div>
<div class='btpolygone boutonIconeCont'><img src="images/zone.gif" /><div class="boutonIcone">Ajouter une zone</div></div>
</div>

<div class='editionCarteDroite'>
<div class='btsupr boutonIconeCont' style='display:none;'><img src="images/icone-supprimer3.gif" /><div class="boutonIcone">Supprimer</div></div>
</div>
<form id='ajouterForm' action='#' method='post' border='0'>
    <fieldset>
        <input type='hidden' name='position' value='' />
        <input type='hidden' name='fichiers' value=''/>
        <input type='hidden' name='images' value='' />
        <input type='hidden' name='reponse' value='<?php echo $reponse; ?>' />  
        <?php
            if ($reponse == '') {
                echo "<select class='select' name='sujet' form='ajouterForm'>";
                echo '<option value="vide" disabled selected>Choisir un sujet</option>';
                for ($i=0; $i<$num_s; $i++) {
                    echo '<option value="'.$IDsujets[$i].'">'.$sujets[$i].'</option>';
                }
                echo "</select>";
            } else {
                echo "<input type='hidden' name='sujet' value='".$sujet_reponse."' />";
            }
        ?>

        <select class="select" name="auteur" form="ajouterForm"  <?php if ($reponse == '') { echo 'style="float:right;"'; } else { echo 'style="float:left;"'; } ?>>
            <option value="" disabled selected>Choisir un auteur</option>
            <?php for ($i=0; $i<$num_a; $i++) {
                echo '<option value="'.$i.'">'.$auteurs[$i].'</option>';
            } ?>
            <option value="invite">Invité</option>
        </select>
 
        <textarea id="redactor_content" name="texte" placeholder="(Texte)"></textarea>
        <textarea id="redactor_notes" name="notes"><ol><li>(Note)</li></ol></textarea>

    </fieldset>
</form>
<form action class="uploadform dropzone no-margin dz-clickable"> </form>
<form action class="uploadform2 dropzone no-margin dz-clickable"> </form>
<input type='submit' id='boutonAjouter' value='Ajouter' class='bouton' form="ajouterForm" />    



<script type="text/javascript" src="js/editeur.js" ></script> 
<script type="text/javascript" src="js/modif-carte.js" ></script> 
<script type="text/javascript">

$(document).ready(function(){
    var _ref;
    Dropzone.autoDiscover = false;


    var tableauImages = new Array();

    $(".uploadform").dropzone({ 
        acceptedFiles: ".jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF",
        url: 'fichiers/upload_images.php',
        maxFiles: 9999,
        maxFilesize: 1000,
        clickable: false,
        addRemoveLinks: true,
        dictRemoveFile: "",
        dictCancelUpload: "",
        dictDefaultMessage: "Glisser les images ici",

        success: function (response) {
            var x = JSON.parse(response.xhr.responseText);
            var increment_images = $('[name=images]', '#ajouterForm').val();
            $(".uploadform > .dz-message").css('display','none');
            var element_courant = response.previewElement;
            $(element_courant).children('.dz-progress').css('display',"none");
            //$(".uploadform").css("opacity",1);
            $(element_courant).append("<input type='text' data-img-src="+x.img+" class='legende' placeholder='(Légende de l&rsquo;image)' />");
        },   
    
        removedfile: function(file) {
            var x = JSON.parse(file.xhr.responseText)
            if ($(".uploadform > .dz-preview").length <= 1) {
                $(".uploadform > .dz-message").css('display','block'); 
                // $(".uploadform").css("opacity","");
            } else {
               // $(".uploadform").css("opacity",1);
            }
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },

        error: function(file) {
            if ($(".uploadform > .dz-preview").length <= 1) {
                $(".uploadform > .dz-message").css('display','block');
            }
            alert('Une erreur est survenue, le fichier envoyé est-il bien une image jpg, gif ou png ?')
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;  
        }
    });

    $(".uploadform2").dropzone({ 
        //acceptedFiles: ".zip,.ZIP,.pdf,.PDF,",
        url: 'fichiers/upload_fichiers.php',
        maxFiles: 9999,
        maxFilesize: 1000,
        clickable: false,
        addRemoveLinks: true,
        dictRemoveFile: "",
        dictCancelUpload: "",
        dictDefaultMessage: "Glisser les fichiers ici",

        success: function (response) {
            var x = JSON.parse(response.xhr.responseText);
            $(".uploadform2 > .dz-message").css('display','none');
            var increment_images = $('[name=fichiers]', '#ajouterForm').val();
            $('[name=fichiers]', '#ajouterForm').val(increment_images+x.fichier+",");

            var element_courant = response.previewElement;
            $(element_courant).children('.dz-progress').css('display',"none");
            //$(".uploadform2").css("opacity",1);
        },   
    
        removedfile: function(file) {

            var x = JSON.parse(file.xhr.responseText);
             if ($(".uploadform2 > .dz-preview").length <= 1) {
                //$(".uploadform2").css("opacity","");
                $(".uploadform2 > .dz-message").css('display','block'); 
                $('[name=fichiers]', '#ajouterForm').val('');
            } else {
               // $(".uploadform2").css("opacity","1");
                var chaine_courante = $('[name=fichiers]', '#ajouterForm').val();
                chaine_courante = chaine_courante.split(",");
                chaine_courante.splice(chaine_courante.indexOf(x.fichier), 1);
                $('[name=fichiers]', '#ajouterForm').val(chaine_courante);
            }
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },

        error: function(file) {
            if ($(".uploadform2 > .dz-preview").length <= 1) $(".uploadform2 > .dz-message").css('display','block');
            alert('Une erreur est survenue. Merci de réessayer l\'envoi');
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;  
        }
    });
});

</script>






