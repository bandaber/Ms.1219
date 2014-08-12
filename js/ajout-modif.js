$(document).ready(function(){
    

    $(document).on('change', 'select', function() {
        if ($(this).val() == 'invite') {
            var invite = prompt("Merci de saisir le nom de l'invité", "");

            if (invite != "" && invite != " " && invite != null) {
                $('select option[value="invite"]').text("Invité : "+invite);
            } else {
                $('select').val('0');
            }
        }
    });


    $(document).on('submit', '#ajouterForm', function() {
        var message = "";
        if ($("select[name$='sujet']").val() == null && $("input[name$='sujet']").attr('type') != "hidden") message += "le sujet, ";
        if ($("select[name$='auteur']").val() == null) message += "l'auteur, ";
        if ($("textarea[name$='texte']").val() == '') message += "le texte, ";
        var valeursImages = '';
        if ($("input[class$='legende']").length > 0) {
            $("input[class$='legende']").each(function() {
                valeursImages += '{"legende":"'+$(this).val()+'", "source":"'+$(this).attr("data-img-src")+'"},';
            });
        }
        $("input[name$='images']").val('['+valeursImages.slice(0, -1)+']');
        $('select option[value="invite"]').val($('select option[value="invite"]').text());

        if (message == "") {
            $("input[name$='position']").val($.toJSON(objetsCarte.toGeoJSON()));
                $.post("./actions/ajouter.php", $(this).serialize())
                .done(function(data) {
                    console.log(data);
                    window.location.hash = "#index";
                });
        } else {
            alert("Il semble que certains champs ne soient pas renseignés.\r\nMerci de saisir "+message.substring(0,message.length-2));
        }
        return false;
    });


    $(document).on('submit', '#modifierForm', function() {
        var message = "";
        if ($("select[name$='sujet']").val() == null && $("input[name$='sujet']").attr('type') != "hidden") message += "le sujet, ";
        if ($("select[name$='auteur']").val() == null) message += "l'auteur, ";
        if ($("textarea[name$='texte']").val() == '') message += "le texte, ";
        var valeursImages = '';
        if ($("input[class$='legende']").length > 0) {
            $("input[class$='legende']").each(function() {
                valeursImages += '{"legende":"'+$(this).val()+'", "source":"'+$(this).attr("data-img-src")+'"},';
            });
        }

        $('select option[value="invite"]').val($('select option[value="invite"]').text());
        $("input[name$='images']").val('['+valeursImages.slice(0, -1)+']');
        if (message == "") {
            $("input[name$='position']").val($.toJSON(objetsCarte.toGeoJSON()));
            $.post("./actions/modifier.php", $(this).serialize())
                .done(function(data) {
                    console.log(data);
                    window.location.hash = "#index";
                }); 
        } else {
            alert("Il semble que certains champs ne soient pas renseignés.\r\nMerci de saisir "+message.substring(0,message.length-2));
        }
        return false;

    });


    $(document).on('submit', '#ajouterSujetForm', function() {
        if (confirm('Attention, vous êtes sur le point de modifier les sujets généraux. Si vous avez supprimé un ou plusieurs sujets, les commentaires qui y sont rapportés seront également supprimés. Êtes-vous sûr de vouloir enregistrer les modifications des sujets ?')) {
            var sujetsTableau = new Array();
            var idsTableau = new Array();
            $(".inputSujet").each(function(index) {
                if ($(this).val() != "") {
                    sujetsTableau.push($(this).val());
    
                    if ($(this).attr('data-id') != undefined) {
                        idsTableau.push($(this).attr('data-id'));
                    } else {
                        var idDernier = parseInt(idsTableau[idsTableau.length-1])+1;
                        idsTableau.push(idDernier);
                    }                
                }           
            });
    
            $("input[name$='sujets']").val(sujetsTableau.join('*'));
            $("input[name$='ids']").val(idsTableau.join('*'));
    
    
            $.post("./actions/sujets.php", $(this).serialize())
                .done(function(data) {
                    console.log(data);
                    window.location.hash = "#index";
            });
                
            return false;
        } else {
            return false;
        }
    });

});