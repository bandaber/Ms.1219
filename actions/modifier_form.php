<?php
try {
	include 'connexion_db.php';

    $query_a = "SELECT nom FROM utilisateurs ORDER BY id ASC";
    $stmt_a = $con -> prepare($query_a);
    $stmt_a -> execute();
    $num_a = $stmt_a -> rowCount();
    $auteurs = array();
    
    while ($row_a = $stmt_a->fetch(PDO::FETCH_ASSOC)){
        array_push($auteurs, $row_a['nom']);
    }

    $query_s = "SELECT * FROM sujets ORDER BY id ASC";
    $stmt_s = $con -> prepare($query_s);
    $stmt_s -> execute();
    $num_s = $stmt_s -> rowCount();
    $sujets = array();
    $IDsujets = array();
    
    while ($row_s = $stmt_s->fetch(PDO::FETCH_ASSOC)){
        array_push($sujets, $row_s['sujet']);
        array_push($IDsujets, $row_s['id']);
    }

	
	$query = "SELECT * FROM remarques WHERE id = ? LIMIT 0,1";
	$stmt = $con->prepare( $query );

    
	$stmt->bindParam(1, $_REQUEST['remarque_id']);

	if ($stmt->execute()) {
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$id = $row['id'];
		$reponse = $row['reponse'];
		$position = $row['position'];
		$auteur = $row['auteur'];
        $sujet = $row['sujet'];
		$texte = $row['texte'];
        $notes = $row['notes'];
		$images = $row['images'];
		$fichiers = $row['fichiers'];
		
	} else {
		echo "Impossible d'accéder à la remarque.";
	}
}

catch (PDOException $exception){
	echo "Erreur : " . $exception->getMessage();
}
$notes = str_replace("<ol>\r\n<li", "<ol><li", $notes); // remove carriage returns

?>

<div class='filetHaut'></div>
<div class='editionCarteGauche'>
<div class='btmarqueur boutonIconeCont'><img src="images/lieu2.gif" /><div class="boutonIcone">Ajouter un point</div></div>
<div class='btpolygone boutonIconeCont'><img src="images/zone.gif" /><div class="boutonIcone">Ajouter une zone</div></div>
</div>

<div class='editionCarteDroite'>
<div class='btsupr boutonIconeCont' <?php

if (strpos($position, 'Point') == false && strpos($position, 'Polygon') == false) {
    echo "style='display:none;'";
} else {
    echo "style='display:block;'";
}
?>
><img src="images/icone-supprimer2.gif" /><div class="boutonIcone">Supprimer</div></div>
</div>

<form id='modifierForm' action='#' method='post' border='0'>
    <fieldset>
        <input type='hidden' name='position' value='<?php echo $position;  ?>' />
        <input type='hidden' name='fichiers' value='<?php echo $fichiers;  ?>' />
        <input type='hidden' name='images' value='' />
        <input type='hidden' name='id' value='<?php echo $id ?>' />
        <input type='hidden' name='reponse' value='<?php echo $reponse; ?>' />
        <?php
            if ($reponse == '0') {
                echo "<select class='select' name='sujet' form='modifierForm'>";
                echo '<option value="vide" disabled selected>Choisir un sujet</option>';
                for ($i=0; $i<$num_s; $i++) {
                    echo '<option ';
                    if ($sujet == $IDsujets[$i]) {
                        echo 'selected="selected" ';
                    } 
                    echo 'value="'.$IDsujets[$i].'">'.$sujets[$i].'</option>';
                }
                echo "</select>";
            } else {
                echo "<input type='hidden' name='sujet' value='".$sujet."' />";
            }
        ?>

        <select class="select" name="auteur" form="modifierForm" <?php if ($reponse == '0') { echo 'style="float:right;"'; } else { echo 'style="float:left;"'; } ?>>
            <option value="" disabled selected>Choisir un auteur</option>
            <?php

            for ($i=0; $i<$num_a; $i++) {
                echo '<option ';
                if (is_numeric($auteur)) {
                    if ($auteur == $i) {
                        echo 'selected="selected" ';
                    }
                }
                echo 'value="'.$i.'">'.$auteurs[$i].'</option>';
            }

            if (is_numeric($auteur)) {
                echo '<option value="invite">Invité</option>';
            } else {
                echo '<option value="invite" selected="selected">'.$auteur.'</option>';
            }

    
            ?>
            
        </select>
  
        <textarea id="redactor_content" name="texte" placeholder="(Texte)"><?php echo $texte;  ?></textarea>
        <textarea id="redactor_notes" name="notes"><?php echo $notes;  ?></textarea>

    </fieldset>
</form>
<form action class="uploadform dropzone no-margin dz-clickable"> </form>
<form action class="uploadform2 dropzone no-margin dz-clickable"> </form>
<input type='submit'  value='Modifier' class='bouton' form="modifierForm" />    



<script type="text/javascript" src="js/editeur.js" ></script> 
<script type="text/javascript" src="js/modif-carte.js" ></script> 


<script type="text/javascript">

$(document).ready(function(){
    var _ref;
    Dropzone.autoDiscover = false;
    
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
           // console.log(response);
            var x = JSON.parse(response.xhr.responseText);
            var increment_images = $('[name=images]', '#ajouterForm').val();
            $(".uploadform > .dz-message").css('display','none');
            var element_courant = response.previewElement;
            $(element_courant).children('.dz-progress').css('display',"none");
            //$(".uploadform").css("opacity",1);
            $(element_courant).append("<input type='text' data-img-src="+x.img+" class='legende' placeholder='(Légende de l&rsquo;image)' />");
        },   
    
        removedfile: function(file) {
            if (file.recuperation) {
                var x = JSON.parse(file.recuperation);
            } else {
                var x = JSON.parse(file.xhr.responseText);
            }

            if ($(".uploadform > .dz-preview").length <= 1) {
                $(".uploadform > .dz-message").css('display','block'); 
                 //$(".uploadform").css("opacity","");
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
        },

        init: function() {
            var images_recuperees = '<?php echo $images;  ?>';
            if (images_recuperees.length > 0) {
                var tableauImages = jQuery.parseJSON(images_recuperees);
            
                for (var i=0; i<tableauImages.length;i++) {
                        var fichier = {
                            name: tableauImages[i].source,
                            size: 0,
                            status: 'success',
                            recuperation: '{"img":"'+tableauImages[i].source+'"}'
                        };
                    


                    this.emit("addedfile", fichier);
                    this.emit("thumbnail", fichier, tableauImages[i].source);
                    this.files.push(fichier);
                   // console.log(this.files);
                   // var containerPreview = this.files[this.files.length-1].previewElement;
                    //console.log(this.files[this.files.length-1]);
                   //containerPreview.innerHTML = "test"+containerPreview.innerHTML;
                   $(".dz-preview:last").append("<input type='text' data-img-src="+tableauImages[i].source+" class='legende' placeholder='(Légende de l&rsquo;image)' value='"+tableauImages[i].legende+"'/>");
                    //containerPreview.innerHTML = "<input type='text' data-img-src="+tableauImages[i].source+" class='legende' placeholder='(Légende de l&rsquo;image)' value='"+tableauImages[i].legende+"'/>"+containerPreview.innerHTML ;                    
                   // $(".uploadform").css("opacity",1);
                }
            }
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
            var increment_images = $('[name=fichiers]', '#modifierForm').val();
            $('[name=fichiers]', '#modifierForm').val(increment_images+x.fichier+",");

            var element_courant = response.previewElement;
            $(element_courant).children('.dz-progress').css('display',"none");
            //$(".uploadform2").css("opacity","1");
        },   
    
        removedfile: function(file) {
            if (file.recuperation) {
                var x = JSON.parse(file.recuperation);
            } else {
                var x = JSON.parse(file.xhr.responseText);
            }


             if ($(".uploadform2 > .dz-preview").length <= 1) {
                $(".uploadform2 > .dz-message").css('display','block'); 
                $('[name=fichiers]', '#modifierForm').val('');
                //$(".uploadform2").css("opacity","");
            } else {
                //$(".uploadform2").css("opacity","1");
                var chaine_courante = $('[name=fichiers]', '#modifierForm').val();
                chaine_courante = chaine_courante.split(",");
                chaine_courante.splice(chaine_courante.indexOf(x.fichier), 1);
                $('[name=fichiers]', '#modifierForm').val(chaine_courante);
            }

            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },

        error: function(file) {
            if ($(".uploadform2 > .dz-preview").length <= 1) $(".uploadform2 > .dz-message").css('display','block');
            alert('Une erreur est survenue. Merci de réessayer l\'envoi');
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;  
        },

        init: function() {
            var fichiers_recuperees = "<?php echo $fichiers;  ?>";
            fichiers_recuperees = fichiers_recuperees.split(",");
            for (var i=0; i<fichiers_recuperees.length-1;i++) {
                    var fichier = {
                        name: fichiers_recuperees[i],
                        size: 0,
                        status: 'success',
                        recuperation: '{"fichier":"'+fichiers_recuperees[i]+'"}'
                    };
                
                this.emit("addedfile", fichier);
                this.files.push(fichier);
                //console.log(this.files);
               // $(".uploadform2").css("opacity",1);
            }
        }

    });
});

</script>







