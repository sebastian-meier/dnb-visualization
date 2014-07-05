<?php
	
	include("config_local.php");
	header("Content-Type: text/html; charset=utf-8");

	$max = 0;
	$persons = array();
	$years = array();
	$n_years = array();
	for($i=0; $i<2015; $i++){$years[intval($i)] = array();}

	$cities = explode(",", $_GET["place_id"]);
	$request = "";

	for($i=0; $i<count($cities); $i++){
		if($i>0){
			$request .= ' OR ';
		}
		$request .= 'place_id = "'.$cities[$i].'"';
	}

	$sql = 'SELECT person_id, start, end FROM dnb_person_place WHERE '.$request;
	$result = query_mysql($sql, $link);
	if ($result) {
	    while ($row = mysql_fetch_array($result)) {
	    	if(!array_key_exists($row[0], $persons)){
	        	$persons[$row[0]] = array();
	        }

	        if($row[1] == 0 && $row[2] == 0){
	        	//ignore
	        }else if(($row[1] == 0)||($row[1]==$row[2])){
	        	if(!array_key_exists($row[0], $years[$row[2]])){
	        		$years[$row[2]][$row[0]] = 0;
					if(count($years[$row[2]])>$max){$max = count($years[$row[2]]);}
	        	}
	        }else if($row[2] == 0){
	        	if(!array_key_exists($row[0], $years[$row[1]])){
	        		$years[$row[1]][$row[0]] = 0;
	        		if(count($years[$row[1]])>$max){$max = count($years[$row[1]]);}
	        	}
	        }else if($row[1] != $row[2]){
	        	for($start = $row[1]; $start <= $row[2]; $start++){
	        		if(!array_key_exists($row[0], $years[$start])){
	        			$years[$start][$row[0]] = 0;
	        			if(count($years[$start])>$max){$max = count($years[$start]);}
	        		}
	        	}
	        }
	    }
	}
	mysql_free_result($result);

	$sql = 'SELECT id, name, job_ids FROM dnb_persons WHERE ';
	$first = true;
	foreach ($persons as $person => $value) {
		if(!$first){
			$sql .= ' OR ';
		}
		$sql .= 'id="'.$person.'"';
		$first = false;
	}

	$result = query_mysql($sql, $link);
	if ($result) {
	    while ($row = mysql_fetch_array($result)) {
	    	$persons[$row[0]] = array($row[1], json_decode($row[2]));
	    }
	}
	mysql_free_result($result);

	foreach ($years as $year) {
		$t_year = array();
		foreach ($year as $person => $zero) {
			array_push($t_year, $person);
		}
		array_push($n_years, $t_year);
	}

	$json = array(
		"max" => $max,
		"years" => $n_years,
		"persons" => $persons
	);

	echo json_encode($json);
?>