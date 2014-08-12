<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
	<title>MS1219</title>
	<meta charset="UTF-8">
	<script src="libs/leaflet-src.js"></script>
	<link rel="stylesheet" href="libs/leaflet.css" />

	<script src="libs/Leaflet.draw-src.js"></script>
	<link rel="stylesheet" href="libs/leaflet.draw.css" />

	<script src='libs/jquery-1.9.1.min.js'></script>
	<script src='libs/jquery.json-2.4.min.js'></script>	

	<link rel="stylesheet" href="style.css" />
</head>	
<body>
	<div id="carte"></div>

	<div id="commentaires">
		<div id='afficher' class='bouton'>Afficher</div>
		<div id='ajouter' class='bouton'>Ajouter</div>
		<div id='remarques'></div>
	</div>

<script type="text/javascript">
$(document).ready(function() {

	//MESSAGES
	L.drawLocal.draw.toolbar.buttons.polygon = 'Déterminer une zone';
	L.drawLocal.draw.toolbar.buttons.marker = 'Déterminer un lieu';
	L.drawLocal.draw.toolbar.buttons.edit = 'Modifier';

	//VARIABLES
	var largeurColonne = 600;


	// CREATION CARTE
	//$("#carte").css("width",$(window).width()-largeurColonne);
	var carte = new L.map('carte', {zoomControl:false, maxBounds:[[100, -140],[-100, 140]]})
	carte.fitBounds([[80, -120],[-80, 120]]);
	var layer1 = new L.tileLayer('TILES_OK/{z}/{x}/{y}.jpg', {
		minZoom:2,
		maxZoom:8,
		bounds:[[80, -120],[-80, 120]],
		continuousWorld:true
	}).addTo(carte);

/*
	//AFFICHAGE OBJETS
	var objetsCarte = new L.FeatureGroup();
	carte.addLayer(objetsCarte);

	//AFFICHAGE BARRE D'OUTILS
	var controlDessin = new L.Control.Draw({
		position: 'topright',
		draw: {
        	polyline: false,
        	circle: false, 
        	rectangle: false,
        	marker: true,
        	polygon: {
        	    allowIntersection: false,
        	    drawError: { // Si intersection
        	        color: '#e1e100',
        	        message: 'La zone doit être continue'
        	    },
        	    shapeOptions: {
        	        color: '#bada55'
        	    }
        	},
    	},
		edit: {
			featureGroup: objetsCarte,
			remove: false
		}
	});
	carte.addControl(controlDessin);




	//EVENEMENTS MODIFICATION BARRE D'OUTILS
	carte.on('draw:created', function (e) {
		var type = e.layerType,
			layer = e.layer;
		objetsCarte.addLayer(layer);
		$("input[name$='position']").val($.toJSON(objetsCarte.toGeoJSON()));
		console.log(objetsCarte.toGeoJSON());
	});

	carte.on('draw:edited', function (e) {
		var layers = e.layers;
		var nombreCalquesEdites = 0;
		layers.eachLayer(function(layer) {
			nombreCalquesEdites++;
		});
		console.log("Edited " + nombreCalquesEdites + " layers");
	});

				/*if (type === 'marker') {
			layer.bindPopup('A popup!');
		}
		*/

		/*L.DomUtil.get('changeColor').onclick = function () {
			drawControl.setDrawingOptions({ rectangle: { shapeOptions: { color: '#004a80' } } });
		};
	$(window).resize(function() {
		$("#carte").css("width",$(window).width()-largeurColonne);
	});*/
});
</script>
</body>
</html>