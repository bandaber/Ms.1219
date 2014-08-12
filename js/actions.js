
$(document).ready(function() {

	$(document).on('click', '#afficher', function() {window.location.hash = "#index";});
	$(document).on('click', '#ajouter', function() {window.location.hash = "#ajouter";});
	$(document).on('click', '#editerSujets', function() {window.location.hash = "#sujets";});
	$(document).on('click', '.repondre', function() {window.location.hash = "#ajouter_"+($(this).attr('data-id'));});
	$(document).on('click', '.modifier', function() {window.location.hash = "#modifier_"+($(this).attr('data-id'));});
	$(document).on('click', '.supprimer', function() {window.location.hash = "#supprimer_"+($(this).attr('data-id'));});


	$(window).on('hashchange', function(e) {
		switch (window.location.hash.substring(0, 6)) {

		case "#ajout": 
			$(".leaflet-draw").show(); $("#afficher").show(); $("#ajouter").hide(); $("#editerSujets").hide(); $("#filtrer").hide();
			$("#commentaires").scrollTop(0);
			mode = "ajout";
			if (window.location.hash.charAt(8) == '_') {
				var id_courant = parseInt(window.location.hash.substring(9, window.location.hash.length));
				objetsCarte.clearLayers();
				supprimerBezier();
				$('#sujets').load('./actions/ajouter_form.php?remarque_id='+id_courant, function() {
					resetVue();
				});
			} else {
				$('#sujets').load('./actions/ajouter_form.php', function() {
					objetsCarte.clearLayers();
					supprimerBezier();
					resetVue();
				});
			}
			break;


		case "#modif":
			$(".leaflet-draw").show();	$("#afficher").show();	$("#ajouter").hide(); $("#filtrer").hide(); $("#editerSujets").hide();
			var id_courant = parseInt(window.location.hash.substring(10, window.location.hash.length));
			mode = "modif";
			objetsCarte.eachLayer(function (layer) {
				if (layer.id_remarque != id_courant) objetsCarte.removeLayer(layer);
			});
			
			rolloverCarte = false;
			$("#commentaires").scrollTop(0);
			$('#sujets').load('./actions/modifier_form.php?remarque_id='+id_courant, function() {
				supprimerBezier();
				resetVue();
			});
			break;

		case "#suppr":
			if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
				var id_courant = parseInt(window.location.hash.substring(11, window.location.hash.length));
				$("#commentaires").scrollTop(0);
				supprimerBezier();
				$.post("./actions/supprimer.php", {id: id_courant})
					.done(function() {
						window.location.hash = "#index";
					});
			} else {
				rafraichirHash();
			}
		
			break;

		case "#sujet":
			$(".leaflet-draw").show(); $("#afficher").show(); $("#ajouter").hide(); $("#editerSujets").hide(); $("#filtrer").hide();
			$("#commentaires").scrollTop(0);
			$('#sujets').load('./actions/sujets_form.php', function() {
				objetsCarte.clearLayers();
				supprimerBezier();
				resetVue();
			});
	
			break;


		case "#index": 
			$(".leaflet-draw").hide(); $("#afficher").hide(); $("#ajouter").show(); $("#filtrer").show(); $("#editerSujets").show();
			$("#commentaires").scrollTop(0);
			rolloverCarte = true;
			$.post("./actions/afficher.php", {coches: cochesActives.toString()})
				.done(function(reponse) {
					resetVue();
					afficher(reponse);
				});
			break;
		}
	});
	



	$(document).on('click', '.sujet', function(event) {
		if ($(this).children('div.remarque').length >= 1) {
			if ($(event.target).hasClass('titre')) {
				fermerDetails();
			}
		} else {
			//console.log($(this).attr('data-id-sujet'));
			$.post("./actions/afficher_detail.php", {sujet: $(this).attr('data-id-sujet')})
			.done(function(reponse) {
				afficher_detail(reponse);
			});
		}
	});


	$(document).on('mouseover', '.sujet', function() {
		remarqueCourante = "";
		var id_sujet = $(this).attr("data-id-sujet");
		if (!marqueursCaches && !pageAffichee) {
			$(".referenceCarte").show();
			$(".referenceCarte:not(.sujet"+id_sujet+")").hide();
		}
		$(this).css('opacity','1');
	});



	$(document).on('mouseout', '.sujet', function() {
		if (mode == 'sujet') {
			$(this).css('opacity','');
			if (!marqueursCaches && !pageAffichee)  $(".referenceCarte").show();
		} else if (mode == 'remarque') {
			if (!marqueursCaches && !pageAffichee) {
				$(".referenceCarte").hide();
				var id_sujet = $(".sujet > .remarque").parent().attr("data-id-sujet");
				$(".sujet"+id_sujet).show();
			}
			if ($(this).children('div.remarque').length < 1) {
				$(this).css('opacity','');
			}
		}
		supprimerBezier();
	});

	$(document).on('mouseover', '.remarque', function(event) {
		remarqueCourante = "";
		supprimerBezier();
		if (!marqueursCaches && !pageAffichee) {
			tracerBezier($(this),"remarque");
		}
		$('.remarque').css('opacity','');
		$(this).css('opacity',1);
	});

	$(document).on('mouseout', '.remarque', function(event) {
		$(this).css('opacity','');
	});

	$(document).on('click', '.remarque', function(event) {
		var id_remarque = $(this).attr("data-id-remarque");
		if (!$(event.target).hasClass('icone') && !$(event.target).hasClass('lienImageAffichee') && !$(event.target).hasClass('fichierAffiche')) {
			deplacerFenetre(id_remarque);
		}
	});




	function afficher(requeteJSON) {
		//console.log(requeteJSON);
		mode = "sujet";
		var requete = $.parseJSON(requeteJSON);
		$('#sujets').html('');
		
		objetsCarte.clearLayers();
		validerEdit();
		if (requete[0].id) {
			$.each(requete, function(index, valeur) {
				$('#sujets').append(
					$('<div/>')
					.addClass("sujet")
					.attr("data-id-sujet", valeur.id)
					.append($('<div/>').addClass("chapeau gauche").html("<span class='titre'>"+valeur.sujet+"</span>"))
				);
				if (valeur.remarques[0]) {
					//console.log(valeur.remarques);
					$.each(valeur.remarques, function(indexR, valeurR) {
							ajouterElementCarte(valeurR.position, valeurR.id, valeur.id, false);
					});
				}
			});
		}
	}


	function afficher_detail(requeteJSON) {
		var requetes = $.parseJSON(requeteJSON);
		fermerDetails();
		mode = "remarque";
		$("div[data-id-sujet='"+requetes[0].sujet+"']").css({'opacity':'1', 'cursor':'auto'});

		$.each(requetes, function(index, requete) {
			function print_detail(requete, reponse) {
				$("div[data-id-sujet='"+requete.sujet+"']")
				.append($('<div/>')
					.attr('data-id-remarque', requete.id)
					.addClass("remarque contenu_detail")
				.append($('<div/>')
					.addClass("boutons contenu_detail")
					.attr("data-id-boutons",requete.id)
					.append($('<div/>').attr("data-id",requete.id).addClass("supprimer").html("<img class='icone' src='images/icone-supprimer3.gif' />"))
					.append($('<div/>').attr("data-id",requete.id).addClass("modifier").html("<img class='icone' src='images/icone-editer2.gif' />")))
				.append($('<div/>')
					.addClass("texte contenu_detail")
					.html(requete.auteur+", le "+requete.date+"<br />"+afficherReferences(requete.nombrePoints, requete.nombreZones)+"<br />-<br />"+requete.texte))
				);
				if (reponse) {
					$("div[data-id-remarque='"+requete.id+"']").addClass("reponse");
				} else {
					$("div[data-id-boutons='"+requete.id+"']").append($('<div/>').attr("data-id",requete.id).addClass("repondre").html("<img class='icone' src='images/icone-repondre.gif' />"));
				}
				afficherDocuments($("div[data-id-remarque='"+requete.id+"'] > .texte"), requete);
			}


			print_detail(requete, false);
			$.each(requete.reponses, function(indexR, reponse) {
				print_detail(reponse, true);
			});
		});
	
		if (remarqueCourante != "") {
			var sauverRemarqueCourante = remarqueCourante;
			$("div[data-id-remarque='"+remarqueCourante+"']").trigger("mouseover");
			remarqueCourante = sauverRemarqueCourante;
			$("#commentaires").scrollTop($("div[data-id-remarque='"+remarqueCourante+"']").offset().top + $("#commentaires").scrollTop() - decalageScroll);
		}	
	}

	function afficherDocuments(txt, requete) {
		if (requete.notes.length > 27) {
			txt.append("<br /><br />"+requete.notes);
		}
		if (requete.images != '') {
			var tableauImages = jQuery.parseJSON(requete.images);
		} else {
			var tableauImages = "";
		}

		if ((tableauImages.length+requete.fichiers.length) == 1) {
			txt.append("<br />-<br />Document associé :<br />");
		} else if ((tableauImages.length+requete.fichiers.length) > 1) {
			txt.append("<br />-<br />Documents associés :<br />");
		}			
		if (tableauImages.length > 0) {
			for (var i=0; i<tableauImages.length; i++) {
				var source = tableauImages[i].source;
				var miniature = source.replace("full", "miniatures");
				miniature = miniature.replace("img", "thumb-img");

				var impression = "<a class='lienImageAffichee' href='"+tableauImages[i].source+"' target='_blank'><img class='icone imageAffichee' src='"+miniature+"' />";
				if (tableauImages[i].legende != "") {
					impression += " &mdash; "+tableauImages[i].legende;
				}
				impression += "</a>";
				txt.append(impression);
			}
		}
		
		if (requete.fichiers.length > 1) {
			requete.fichiers =requete.fichiers.slice(0,-1); 
			var tableauFichiers = requete.fichiers.split(",");
			/*txt.append("<br />");*/
			for (var i=0; i<tableauFichiers.length; i++) {
				txt.append("<a class='fichierAffiche' href='fichiers/uploads_fichiers/"+tableauFichiers[i]+"' target='_blank'><img class='icone iconeFichier' src='images/fichier2.gif' />  &mdash; "+tableauFichiers[i]+"</a>");
			}
		}
	}

	function fermerDetails() {
		mode = 'sujet';
		$('.contenu_detail').remove();
		$('.reponse').remove();
		$('.repondre').remove();
		$(".sujet").css({'opacity':'', 'cursor':'pointer'});
		supprimerBezier();
		supprimerMarqueursReponses();
	}

	function supprimerMarqueursReponses() {
		objetsCarte.eachLayer(function(layer) {
			if (layer.reponse == true) {
				objetsCarte.removeLayer(layer);
			}
		});
	}

	function deplacerFenetre(id) {
		var limitesMarkers = [];
		objetsCarte.eachLayer(function(marker) {
			if (marker.id_remarque == id) {
				if (marker.feature.geometry.type == "Point") limitesMarkers.push(marker.getLatLng());
				else if (marker.feature.geometry.type == "Polygon")	limitesMarkers.push(marker.getBounds());
			}
		});
		carte.fitBounds(limitesMarkers, {maxZoom:6});
	}




	function tracerBezier(base, source) {
		if (source == "reference") {
			courbeBezier(base.target)
		} else if (source == "remarque") {
			objetsCarte.eachLayer(function(marker) {
				if (marker.id_remarque == base.attr('data-id-remarque')) {
					courbeBezier(marker);
				}
			});
		}

		function courbeBezier(cible) {
			var repetition = false;

			if (cible.feature.geometry.type == "Point") {
				var debut = cible.getLatLng();
				var decalageX = 0;
			} else if (cible.feature.geometry.type == "Polygon") {
				var lespoints = cible.getLatLngs();
				var debut = lespoints[max_lng(lespoints)];
				var decalageX = 0;
			}

			for (var i=bezierRemarques.length-1; i>=0; i--) {
				if (bezierRemarques[i].debut == debut) {
					repetition = true;
					break;
				}
			}

			if (!repetition) {
				if (mode == "sujet") {
					var finY = $("div[data-id-sujet='"+cible.id_sujet+"']").offset().top;
				} else if (mode == "remarque") {
					var finY = $("div[data-id-remarque='"+cible.id_remarque+"']").offset().top;
				}
	
				var fin = carte.containerPointToLatLng([$("#carte").width(),finY]);
				bezierRemarques.push(new L.ArcedPolyline([debut, fin], {color: 'white',weight:2, decalageX:decalageX}));
				var derniere = bezierRemarques[bezierRemarques.length-1];
				derniere.id_remarque = cible.id_remarque;
				derniere.id_sujet = cible.id_sujet;
				derniere.reponse = cible.reponse;
				derniere.debut = debut;
				derniere.addTo(carte);
			}
		}
	}
	
	function replacerBezier() {
		$.each(bezierRemarques, function(index, bezier) {
			var finX = $("#carte").width();

			if (mode == "sujet") {
				var finY = $("div[data-id-sujet='"+bezier.id_sujet+"']").offset().top;
			} else if (mode == "remarque") {
				var finY = $("div[data-id-remarque='"+bezier.id_remarque+"']").offset().top;
			}	
			var fin = carte.containerPointToLatLng([finX,finY]);
			bezier.decalageX = 0;
			bezier.setLatLngs([bezier.debut,fin]);
		});
	}


	function supprimerBezier() {
		for (var i=bezierRemarques.length-1; i>=0; i--) { // Boucle à l'envers pour ne pas affecter les index en supprimant des choses de l'array
			if (bezierRemarques[i].id_remarque != remarqueCourante) {
				carte.removeLayer(bezierRemarques[i]);
				bezierRemarques.splice(i, 1);
			}
		}
	}



	function ajouterElementCarte(position, id_remarque, id_sujet, reponse) {
		var layerGEO = L.geoJson();
		layerGEO.addData($.parseJSON(position));
		var calqueGEO = layerGEO.getLayers();

		for (var i=0; i<calqueGEO.length; i++) {
			calqueGEO[i].id_remarque = id_remarque;
			calqueGEO[i].id_sujet = id_sujet;
			calqueGEO[i].reponse = reponse;

			calqueGEO[i].on("click", function() {
				deplacerFenetre(id_remarque);
				remarqueCourante = id_remarque;
				if (mode == "sujet") {
					$("div[data-id-sujet='"+id_sujet+"']").trigger("click");
				} else if (mode == "remarque") {
					tracerBezier($("div[data-id-remarque='"+id_remarque+"']"),"remarque");
					supprimerBezier();
					$("#commentaires").scrollTop($("div[data-id-remarque='"+id_remarque+"']").offset().top + $("#commentaires").scrollTop() - decalageScroll);
				}
				
			});

			calqueGEO[i].on("mouseover", function(event) {
				if (mode == "remarque") {
					if (id_remarque != remarqueCourante) remarqueCourante = '';
					$('.remarque').css('opacity','');
					$("div[data-id-remarque='"+id_remarque+"']").css('opacity',1);
					supprimerBezier();
				}
				if (mode != "ajout" && mode != "modif") {
					tracerBezier(event,"reference");
					$("div[data-id-sujet='"+id_sujet+"']").css("opacity",1);
				}
			});

			calqueGEO[i].on("mouseout", function() {
				if (mode == "sujet") {
					$(".sujet").css("opacity",'');
				} else if (mode == "remarque") {
					$('.remarque').css('opacity','');
					if (id_remarque == remarqueCourante) $("div[data-id-remarque='"+id_remarque+"']").css("opacity",1);
				}
				supprimerBezier();
			});

			if (calqueGEO[i].feature.geometry.type == "Point") {
				calqueGEO[i].setIcon(L.divIcon({iconSize:[26,26], iconAnchor:[13, 13],  className:'marqeurBase referenceCarte sujet'+id_sujet, html:''}));
			} else if (calqueGEO[i].feature.geometry.type == "Polygon") {
				calqueGEO[i].setStyle({"color": "#ffffff", "weight":2, "opacity": 1, "fillOpacity":0.3, "className":"referenceCarte sujet"+id_sujet})
			}

			objetsCarte.addLayer(calqueGEO[i]);
		}
	}

	function afficherReferences(nombrePoints, nombreZones) {
		var phraseReferences;
		var totalReferences = nombrePoints+nombreZones;

		if (totalReferences == 1) {
			phraseReferences = "Référence sur le document : ";
		} else if (totalReferences > 1) {
			phraseReferences = "Références sur le document : ";
		} else {
			phraseReferences = "Aucune référence sur le document";
		}

		if (nombrePoints == 1) {
			phraseReferences += "un point";
		} else if (nombrePoints > 1) {
			phraseReferences += nombrePoints+ " points";
		}
		if (nombrePoints > 0 && nombreZones > 0) {
			phraseReferences += " et ";
		}
		if (nombreZones == 1) {
			phraseReferences += "une zone";
		} else if (nombreZones > 1) {
			phraseReferences += nombreZones+ " zones";
		}

		return phraseReferences;
	}

	carte.on('moveend', function() {
		if (window.location.hash.substring(0, 6) == "#index") {
			replacerBezier();
		}
	});


	carte.on('move', function() {
		replacerBezier();
	});

	$("#commentaires").scroll(function() {
		replacerBezier();
	});

	$("#titre_general").click(function() {
		rafraichirHash();
	});

	$(".btfiltrer").click(function() {
		if (!trierAffiche) {
			$("#filtrer").css('opacity','1');
			$("#trier").css('display','block');
			trierAffiche = true;
		} else {
			if ($(".typoFiltre").html() == 'Filtre par sujet désactivé') {
				$("#filtrer").css('opacity','');
			} else {
				$("#filtrer").css('opacity','1');
			}
			$("#trier").css('display','none');
			trierAffiche = false;
		}
	});


	function validerEdit() {
		var toolbar;
    	for (var toolbarId in controlDessin._toolbars) {
        	toolbar = controlDessin._toolbars[toolbarId];
        	if (toolbar instanceof L.EditToolbar) {
           		toolbar._modes.edit.handler.disable();
            break;
        	}
    	}
	}

	//$(".container .item").stick_in_parent();

	function rafraichirHash() {
		if (window.location.hash != "#index") {
			window.location.hash = "#index";
		} else {
			window.location.hash = "#index_";
		}
		supprimerBezier();
	}
	rafraichirHash();

	function max_lng(elements) {
	    var i = 1;
	    var mi = 0;
	    while (i < elements.length) {
	        if (!(elements[i].lng < elements[mi].lng))
	            mi = i;
	        i += 1;
	    }
	    return mi;
	}

		$("#masquerMarqueurs").click(function() {
		if (!marqueursCaches) {
			if (pageAffichee) {
				carte.removeLayer(pages);
				$("#afficherPages").css('opacity',0.5);
				pageAffichee = false;
				if (mode == "remarque") {
					var id_sujet = $(".sujet > .remarque").parent().attr("data-id-sujet");
					$(".sujet"+id_sujet).show();
				} else {
					$(".referenceCarte").show();
					$(".leaflet-editing-icon").show();
					$(".marqeurBase").show();
					$(".leaflet-overlay-pane").show();
				}
				$(this).css('opacity',1);
				marqueursCaches = false;
			} else {
				remarqueCourante = "";
				supprimerBezier();
				$(".referenceCarte").hide();
				$(".leaflet-editing-icon").hide();
				$(".marqeurBase").hide();
				$(".leaflet-overlay-pane").hide();
				marqueursCaches = true;
				$(this).css('opacity',0.5);
			}
		} else {
			if (mode == "remarque") {
				var id_sujet = $(".sujet > .remarque").parent().attr("data-id-sujet");
				$(".sujet"+id_sujet).show();
			} else {
				$(".referenceCarte").show();
				$(".marqeurBase").show();
			}
			$(".leaflet-overlay-pane").show();
			$(".leaflet-editing-icon").show();
			$(this).css('opacity',1);
			marqueursCaches = false;
		}
	});

	$("#afficherPages").click(function() {
		if (!pageAffichee) {
			if (marqueursCaches) {
				$("#masquerMarqueurs").trigger('click');
			}
			remarqueCourante = "";
			supprimerBezier();
			$(".referenceCarte").hide();
			$(".leaflet-editing-icon").hide();
			$(".marqeurBase").hide();
			$(".leaflet-overlay-pane").hide();
			
			pages.addTo(carte);
			pageAffichee = true;
			$("#masquerMarqueurs").css('opacity',0.5);
			$(this).css('opacity',1);
		} else {
			carte.removeLayer(pages);
			if (mode == "remarque") {
				var id_sujet = $(".sujet > .remarque").parent().attr("data-id-sujet");
				$(".sujet"+id_sujet).show();
			} else {
				$(".referenceCarte").show();
				$(".marqeurBase").show();
			}
			$(".leaflet-overlay-pane").show();
			$(".leaflet-editing-icon").show();
			$("#masquerMarqueurs").css('opacity',1);
			$(this).css('opacity',0.5);
			pageAffichee = false;
		}
	});

	function resetVue() {
		if (marqueursCaches) {
			$("#masquerMarqueurs").trigger('click');
		}
		if (pageAffichee) {
			$("#masquerMarqueurs").trigger('click');
		}
	}
		
		//function cacherMarqeursDehors() {
		/*endroitsAffiches = [];
		objetsCarte.eachLayer(function(marker) {
			var positionObjets;
			if (marker.feature.geometry.type == "Point") {
				positionObjets = marker.getLatLng();
			} else if (marker.feature.geometry.type == "Polygon") {
				positionObjets = marker.getBounds();
			}

			if (carte.getBounds().contains(positionObjets)) {
				endroitsAffiches.push(marker.id_remarque);
			}
		});

		// FERMER LES REMARQUES AFFICHÉES QUI SORTENT DE LA CARTE
		$(".sujet").each(function(index) {
			if ($(this).attr("data-lieux-remarque") == "1") {
				var id_courant = $(this).attr("data-id-remarque");
				if (endroitsAffiches.indexOf(parseInt(id_courant)) > -1) {
					$(this).show();
				} else {
					if ($(this).children('div.contenu_detail').length > 1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				}
			}
		});*/
	//}
/*
	function rolloverMarqueurs(id) {
		if (rolloverCarte) {
			var marqueurs = [];
			objetsCarte.eachLayer(function(marker) {
				/*if (mode == "SUJET") {
					if (marker.id_sujet == id) {
						if (marker.feature.geometry.type == "Polygon")	marker.setStyle(polySelect);
					}
				}*/
				
			/*});
		}
	}*/
/*
	function rolloutMarqueurs() {
		var marqueurs = [];
		objetsCarte.eachLayer(function(marker) {
			if (marker.feature.geometry.type == "Polygon")	marker.setStyle(polyBase);
		});
	}
*/

/*
	$(".coche_tri").change(function() {
		cochesActives = [];
		var nombreCoches;
  		$(".coche_tri").each(function(index) {
  			if ($(this).is(':checked')) {
  				cochesActives.push($(this).attr("value"));
  			}
  			nombreCoches = index;
  		});

  		if (cochesActives.length != nombreCoches+1) {
  			$(".typoFiltre").html('Filtre par sujet activé');
  		} else {
  			$(".typoFiltre").html('Filtre par sujet désactivé');
  		}
		rafraichirHash();
	});
*/
	
});
