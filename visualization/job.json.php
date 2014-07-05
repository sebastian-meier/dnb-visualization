<?php
	
	include("config_local.php");
	header("Content-Type: text/html; charset=utf-8");

	$list = array(array("Filter by Job",0,0));

	$sql = 'SELECT name, id, hits FROM dnb_job WHERE length(name)>2 AND hits > 100 ORDER BY name ASC';
	$result = query_mysql($sql, $link);
	if ($result) {
	    while ($row = mysql_fetch_array($result)) {
	    	if(strlen($row[0])>20){
	    		$name = substr($row[0], 0, 20)."...";
	    	}else{
	    		$name = $row[0];
	    	}
	    	array_push($list, array($name,$row[1],$row[2]));
	    }
	}
	mysql_free_result($result);

	echo json_encode($list);
?>