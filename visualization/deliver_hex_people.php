<?php

/*
deliver_square.php
Needs zoom + min_lat / max_lat & min_lng / max_lng and returns an geojson for the heatmap within that area

*/

require_once("heat_config.php");

$tiles = array();
$z0 = $_GET["zoom"]-2;
$z0i = $_GET["zoom"]-1;


$dist_x = ($_GET["max_x"] - $_GET["min_x"])*0.5;
$_GET["max_x"] += $dist_x;
$_GET["min_x"] -= $dist_x;
if($_GET["max_x"]>$max*2){$_GET["max_x"]=$max*2;}
if($_GET["min_x"]<0){$_GET["max_x"]=0;}

$dist_y = ($_GET["max_y"] - $_GET["min_y"])*0.5;
$_GET["max_y"] += $dist_y;
$_GET["min_y"] -= $dist_y;
if($_GET["max_y"]>$max*2){$_GET["max_y"]=$max*2;}
if($_GET["min_y"]<0){$_GET["max_y"]=0;}


$sql = 'SELECT value FROM `'.$db_max_table.'` WHERE `zoom` = '.$z0i.' AND `key` = "maxh_people"';
$result = query_mysql($sql, $link);
if ($result) {
	while ($row = mysql_fetch_array($result)) {
		$tmax = $row[0];
	}
}
mysql_free_result($result);

$size = $tmax / 9;
$stepps = array();
for($i=0; $i<10; $i++){
	$stepps[$i] = array();
}

$step_max = 19;

if($_GET["zoom"]<4){
	$sql = 'SELECT sum(hits), zh'.$z0i.' FROM `'.$db_table.'` WHERE `validconversion` = 1 GROUP BY zh'.$z0i;	
}else{
	$sql = 'SELECT sum(hits), zh'.$z0i.' FROM `'.$db_table.'` WHERE `validconversion` = 1 AND x0 > '.$_GET['min_x'].' AND x0 < '.$_GET['max_x'].' AND y0 > '.$_GET['min_y'].' AND y0 < '.$_GET['max_y'].' GROUP BY zh'.$z0i;	
}

$result = query_mysql($sql, $link);
if ($result) {
	while ($row = mysql_fetch_array($result)) {
		$y = floor($row[1]/$steps[$step_max-$z0]);
		$x = $row[1] - ($y*$steps[$step_max-$z0])-1;
		if($y%2){
			$x += 0.5; 
		}
		$tarray = array();

		$mStep = $step_size[$step_max-$z0]/14;

		$cornerStep = 2.5;
		$topStep = 2.5;

		array_push($tarray, array($x*$step_size[$step_max-$z0]-$max, $y*$step_size[$step_max-$z0]-$max+$mStep*$cornerStep));
		//Top of the hex
		array_push($tarray, array($x*$step_size[$step_max-$z0]-$max+$step_size[$step_max-$z0]/2, $y*$step_size[$step_max-$z0]-$max-$mStep*$topStep));
		array_push($tarray, array($x*$step_size[$step_max-$z0]-$max+$step_size[$step_max-$z0], $y*$step_size[$step_max-$z0]-$max+$mStep*$cornerStep));
		array_push($tarray, array($x*$step_size[$step_max-$z0]-$max+$step_size[$step_max-$z0], $y*$step_size[$step_max-$z0]-$max+$step_size[$step_max-$z0]-$mStep*$cornerStep));
		//Bottom of the hex
		array_push($tarray, array($x*$step_size[$step_max-$z0]-$max+$step_size[$step_max-$z0]-$step_size[$step_max-$z0]/2, $y*$step_size[$step_max-$z0]-$max+$step_size[$step_max-$z0]+$mStep*$topStep));
		array_push($tarray, array($x*$step_size[$step_max-$z0]-$max, $y*$step_size[$step_max-$z0]-$max+$step_size[$step_max-$z0]-$mStep*$cornerStep));

		$istep = round(($row[0])/$size);
		if($istep>9){
			$istep = 9;
		}

		array_push($stepps[$istep], $tarray);
	}
}
mysql_free_result($result);

echo '[';
$first = true;
foreach ($stepps as $key => $step) {
if($step == null){$step = array(array());}
if(!$first){ echo ','; }else{ $first = false; }
?>{"type": "Feature", "properties":{"style": {"stroke": "false", "weight":"0", "color":"#000", "fillColor":"#ff0000", "opacity":"0", "fillOpacity":"<?php echo ($key+1)/10; ?>"}}, "geometry": {"type": "MultiPolygon","coordinates": <?php echo json_encode(array($step)); ?>},"crs": {"type": "name","properties": {"name": "urn:ogc:def:crs:SR-ORG::7483"}}}<?php
}
echo ']';?>