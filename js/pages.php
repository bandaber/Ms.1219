<?php

echo 'var pagesData = {
	"type":"FeatureCollection",
	"features":
		[';

$decalageXpage = 14;
$decalageXdouble = 19;
$decalageY = -15;

$index = -2;
$recto = false;

$incrementX = -121.8;
$incrementY = 76;

$hauteurs = array(75.05,66.93,54.37,36.6,13.58,-12.21,-35.75,-53.75,-66.09,-74.78);
$impression = "";

for ($y=0; $y<10; $y++) {
	for ($x=0; $x<16; $x++) {
		if ($index >= 76) {
			break;
		}

		if ($recto) {
			
			switch ($index) {
				case "-1":
				$affichage = 'C<br>1';
				break;

				case "75":
				$affichage = 'C<br>3';
				break;

				default:
				$affichage = $index.'<br>r';
			}

			$impression .= '{
				"type":"Feature",
				"properties":{"page":"'.$affichage.'"},
				"geometry":	{"type":"Point","coordinates":['.$incrementX.','.$hauteurs[$y].']}
			},';
			$recto = false;
			$incrementX += $decalageXdouble;
		} else {

			switch ($index) {
				case "-1":
				$affichage = 'C<br>2';
				break;

				case "75":
				$affichage = 'C<br>4';
				break;

				default:
				$affichage = $index.'<br>v';
			}

			if ($index != -2) {
				$impression .= '{
					"type":"Feature",
					"properties":{"page":"'.$affichage.'"},
					"geometry":	{"type":"Point","coordinates":['.$incrementX.','.$hauteurs[$y].']}
				},';
			} 
				
				$recto = true;
				$incrementX += $decalageXpage;
				$index++;
			
		}
		
	}
	$incrementX = -121.8;
}
echo substr($impression, 0, -1);
echo ']
};';


/*
{"type":"FeatureCollection","features":[{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-107.9296875,75.05035357407698]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-114.2578125,66.93006025862448]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-114.9609375,54.367758524068385]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-115.31249999999999,36.5978891330702]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-114.9609375,13.581920900545844]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-114.2578125,-12.211180191503985]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-114.60937499999999,-35.7465122599185]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-115.31249999999999,-53.748710796898976]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-114.9609375,-66.08936427047087]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":
[-114.9609375,-74.77584300649234]}}]}*/
?>
















