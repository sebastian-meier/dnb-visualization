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


$sql = 'SELECT value FROM `'.$db_max_table.'` WHERE `zoom` = '.$z0i.' AND `key` = "maxh"';
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

$dots = array();

$sql = 'SELECT latitude, longitude, name, id FROM `'.$db_table.'` WHERE `validconversion` = 1 AND x0 > '.$_GET['min_x'].' AND x0 < '.$_GET['max_x'].' AND y0 > '.$_GET['min_y'].' AND y0 < '.$_GET['max_y'];
$result = query_mysql($sql, $link);
if ($result) {
	while ($row = mysql_fetch_array($result)) {
		array_push($dots, array($row[0], $row[1], $row[2], $row[3]));
		//array_push($dots, array($row[0], $row[1]));
		//array_push($dots, array($row[0], $row[1]));
		//array_push($dots, array($row[0], $row[1]));
		//array_push($dots, array($row[0], $row[1]));
	}
}
mysql_free_result($result);

echo json_encode(array($dots));

?>