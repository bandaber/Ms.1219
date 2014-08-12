$(document).ready(function(){

    var decalageEtiquetteX = 10;
    var decalageEtiquetteY = 20;
    var modeSupprimer = false;

    var toolbar;
    for (var toolbarId in controlDessin._toolbars) {
        toolbar = controlDessin._toolbars[toolbarId];
        if (toolbar instanceof L.EditToolbar) {
            toolbar._modes.edit.handler._selectedPathOptions = polyBase;
            toolbar._modes.edit.handler.enable();
            break;
        }
    }

    function afficherEtiquette(texte) {
        $(".etiquette").html(texte);
        $(".etiquette").css('display','block');
    }

    $(window).mousemove(function (e) {
        $(".etiquette").css("top",e.pageY+decalageEtiquetteY);
        $(".etiquette").css("left",e.pageX+decalageEtiquetteX);
    });

    $(window).mouseout(function (e) {
        if (e.toElement == null && e.relatedTarget == null) {
            $(".etiquette").css("display","none");
        }
    });

    $(window).mouseover(function (e) {
        if (!editionTerminee) {
            $(".etiquette").css("display","block");
        }
    });

//////////////
//////////////

    $(".btmarqueur").click(function () {
        var marqueurCourant = new L.Draw.Marker(carte, controlDessin.options.marker);
        marqueurCourant.options.icon = marqueurBase;
        //console.log(marqueurCourant);
        marqueurCourant.enable();
        afficherEtiquette("Cliquer sur le document pour ajouter un point");
        editionTerminee = false;
        modeSupprimer = false;
        $(".btsupr").css("opacity","");
        $(this).css("opacity","1");
    });

    $(".btpolygone").click(function () {
        var polygoneCourant = new L.Draw.Polygon(carte, controlDessin.options.marker);
        polygoneCourant.options.shapeOptions = polyBase;
        polygoneCourant.enable();
        afficherEtiquette("Cliquer sur le document pour tracer une zone");
        editionTerminee = false;
        modeSupprimer = false;
        $(".btsupr").css("opacity","");
        $(this).css("opacity","1");
    });


    $(".btsupr").click(function () {
        afficherEtiquette("Cliquer sur un point ou une zone pour la supprimer");
        editionTerminee = false;
        modeSupprimer = true;
        $(this).css("opacity","1");
    });

//////////////
// EVENEMENTS
//////////////

    carte.on('draw:created', function (e) {
        layer = e.layer;
        objetsCarte.addLayer(layer);
        $("input[name$='position']").val($.toJSON(objetsCarte.toGeoJSON()));
        $(".etiquette").css('display','none');
        $(".boutonIconeCont").css('opacity','');
        $(".btsupr").css('display','block');
        editionTerminee = true;
    });

    objetsCarte.on('click', function (e) {
        if (modeSupprimer) {
            var layer = e.layer;
            objetsCarte.removeLayer(layer);
            $("input[name$='position']").val($.toJSON(objetsCarte.toGeoJSON()));
            $(".etiquette").css('display','none');
            $(".boutonIconeCont").css("opacity","");
            editionTerminee = true;
            modeSupprimer = false;

            var calques = objetsCarte.getLayers();
            if (calques.length == 0) {
                $(".btsupr").css("display","none");
            };
        } 
   });
});



/*

    carte.on('draw:created', function (e) {
        layer = e.layer;
        objetsCarte.addLayer(layer);
        $("input[name$='position']").val($.toJSON(objetsCarte.toGeoJSON()));
        $(".etiquette").css('display','none');
        $(".boutonIconeCont").css('opacity','');
        //$(".btediter").css('display','block');
        $(".btsupr").css('display','block');
        editionTerminee = true;
    });

    carte.on('draw:edited', function (e) {
        $("input[name$='position']").val($.toJSON(objetsCarte.toGeoJSON()));
        $(".etiquette").css('display','none');
        $(".boutonIconeCont").css('opacity','');
        editionTerminee = true;
    });

    carte.on('draw:deleted', function (e) {
        $("input[name$='position']").val($.toJSON(objetsCarte.toGeoJSON()));
        $(".etiquette").css('display','none');
        $(".boutonIconeCont").css('opacity','');
        editionTerminee = true;
        modeEdit();
    });
});
*/


