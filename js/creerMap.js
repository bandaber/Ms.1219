
var carte;
var controlDessin;
var objetsCarte = new L.FeatureGroup();
var largeurColonne = 500;
var decalageScroll = 25;
var fondDocument;
var endroitsAffiches = [];
var bezierRemarques = [];
var cochesActives = ['rien'];
var couleurEteint = 'rgb(180,180,180)';
var editionTerminee = true;
var rolloverCarte = true;
var trierAffiche = false;
var mode = "sujet";
var marqueurClick = "";
var remarqueCourante = "";
var marqueursCaches = false;
var pageAffichee = false;
var pages;
var pagesGroupe;

var polyBase = {"color": "#ffffff", "weight":2, "opacity": 1, "fillOpacity":0.3};
var polySelect = {"color": "#ffffff", "weight":2, "opacity": 1, "fillOpacity":0};
var marqueurBase = L.divIcon({iconSize:[26,26], iconAnchor:[13, 13],  className:'marqeurBase', html:''});

$(document).ready(function() {

	$("#carte").css("width", $(window).width()-largeurColonne);

	carte = new L.map('carte', {zoomControl:false, minZoom:2, maxBounds:[[78.5, -135],[-78.5, 135]]});
	fondDocument = new L.tileLayer('MS1219_tuiles/{z}/{x}/{y}.jpg', {minZoom:1, maxZoom:8, bounds:[[77, -130],[-77, 130]], continuousWorld:true});
	controlDessin = new L.Control.Draw({
		position: 'topright',
		draw: {
			polyline: false, circle: false, rectangle: false,
			marker: true,
			polygon: { 
				allowIntersection: false,
				drawError: {color: '#e1e100', message: 'La zone doit être continue'},
				shapeOptions: polyBase
			},
		},
		edit: {
			featureGroup: objetsCarte,
			remove: true,
			selectedPathOptions: {
                        color: '#bada55', 
                        opacity: 1,
                        dashArray: '5,5',
                        fill: true,
                        fillColor: '#bada55', 
                        fillOpacity: 0.1
                }
    		}
		});


	pages = L.geoJson(pagesData,{
		pointToLayer: function (feature, latlng) {
   			return L.marker(latlng, {
   				icon: L.divIcon({
   					iconSize:[40,40],
   					iconAnchor:[20,18],
   					className:'numeroPage',
   					html:feature.properties.page})
   			});
   		}
   	});
	//pages.addTo(carte);


	carte.fitBounds([[100, -140],[-100, 140]]);
	//carte.fitBounds([[80, -120],[-80, 120]]);
	carte.addLayer(fondDocument);
	carte.addLayer(objetsCarte);
	carte.addControl(controlDessin);


	$(window).resize(function() {
		$("#carte").css("width",$(window).width()-largeurColonne);
	});
});