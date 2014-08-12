<?php
include 'actions/sujets.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>MS1219</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<script src='libs/jquery-1.9.1.min.js'></script>
	<script src='libs/jquery.json-2.4.min.js'></script>

	<script src="libs/leaflet-src.js"></script>
	<link rel="stylesheet" href="libs/leaflet.css" />

	<script src="libs/leaflet.draw-src.js"></script>
	<link rel="stylesheet" href="libs/leaflet.draw.css" />
	<script src="libs/leaflet-dvf.js"></script>

	<script src="fichiers/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
	<script src="fichiers/assets/plugins/dropzone/dropzone.js" type="text/javascript"></script>
	<link href="fichiers/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="fichiers/assets/plugins/dropzone/css/dropzone.css" rel="stylesheet" type="text/css"/>

	<link rel="stylesheet" href="redactor/redactor.css" />
	<script src="redactor/superscript.js"></script>
	<script src="redactor/redactor.js"></script>
	<script src="redactor/fr.js"></script>

	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css">
	<link rel="stylesheet" href="style.css" />

</head>	
<body>
	<div id="carte"></div>
	<div id="commandes">
		<div id='masquerMarqueurs' style='opacity:1;'><img src='images/icone-marqueurs.gif' alt='Afficher/masquer les références sur le document' /></div>
		<div id='afficherPages'><img src='images/icone-livre.gif' alt='Afficher/masquer les pages du document' /></div>
	</div>
	<div class='etiquette'></div>
	<div id="commentaires">
		<div id='titre_general'>Ms.1219</div>
		<div id='sous_titre'>
			Livre d'heures de la famille Pontbriand, <i>circa</i> 1490<br /> convservé à la bibliothèque de Rennes &mdash; Les Champs Libres			
			<div id='ajouter' class='bouton_haut'><img style='margin-top:-4px;padding-right:8px;'src='images/plus.gif' />Ajouter un commentaire</div>
			<div id='editerSujets' class='bouton_haut'><img style='margin-top:-4px;padding-right:8px;'src='images/sujets.gif' />Éditer les sujets</div>
			<div id='afficher' class='bouton_haut' style='display:none;'><img style='margin-top:-4px;padding-right:8px;'src='images/retour.gif' />Retour</div>
		</div>

		<div id='sujets'></div>
	</div>
<script type="text/javascript" src='js/pages.php'></script>
<script type="text/javascript" src='js/creerMap.js'></script>
<script type="text/javascript" src='js/actions.js'></script>
<script type="text/javascript" src="js/ajout-modif.js" ></script> 
<script type="text/javascript">

$(document).ready(function(){
	function msieversion() {
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        	alert("Pour accéder à cette application dans les meilleures conditions, merci d'utiliser un navigateur respecteux des standards comme Google Chrome ou Mozilla Firefox.");
        	window.history.back();
        }
           
   		return false;
	}
	msieversion();
});
</script>

</body>
</html>