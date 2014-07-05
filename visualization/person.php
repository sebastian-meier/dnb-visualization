<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>DNB</title>
	<script src="js/d3.v3.min.js" type="text/javascript" charset="utf-8"></script>
	<style>

html, body{
        margin:0;
        padding:0;
        border:0;
      }

      #header{
        width:100%;
        background-image:url('images/header.gif');
        display: block;
        height:105px;
      }

      #header img{
        margin-left:10px;
        float:left;
      }

      #header h1{
        float:left;
        margin:58px 0 0 9px;
        font-family:Helvetica;
        font-weight:bolder;
        font-size:25px;
      }


      #main, #overlay{
			position: absolute;
			top:200px;
			left: 0;
			width:100%;
		}

	#relativeOptHolder{
		float:right;
		margin-top:-39px;
		margin-right: 10px;
	}

	h2{
		width:100%;
		text-align:center;
		font-size:30px;
		margin:20px 0 0 0;
		padding:0;
	}

	h3{
		width:100%;
		margin:5px 0 0 0;
		padding:0;
		color:#555555;
		text-align:center;
		font-size:12px;
	}

	#overlay{
		display: none;
	}

</style>
</head>
<body>
    <div id="header"><img src="images/logo.gif" /><h1>/ Data Explorer</h1></div>
    <div id="relativeOptHolder"><input type="checkbox" id="relativeOpt" value="1" /><label>All / Similar Job</label></div>
<?php

	include("config_local.php");

	/*

	@TODO

	Timeline for one person
		-> Filter for persons who have the same job...

	Apply HeatMap technology
		-> Lowes Zoomlevel get Markers

	*/

	$request = array(
		"franz" => 	'100006132',
		"werfel" => '100006132',
		"test" => 	'118815873',
		"vinci" => '118640445'
		//119448009
		//13418999X
		//134597869
		//142328308
	);

	if(isset($_GET["request_id"])){
		$request_id = $_GET["request_id"];
	}else{
		$request_id = $request["vinci"];	
	}
	
	$sql = 'SELECT name, job_ids FROM dnb_persons WHERE id = "'.$request_id.'"';
	$result = query_mysql($sql, $link);
	if ($result) {
	    while ($row = mysql_fetch_array($result)) {
	    	echo '<h2>'.$row[0].'</h2>';
	    	echo '<h3>'.$request_id.'</h3>';
	    	$jobs = json_decode($row[1]);
	    }
	}

?>

	<svg id="main" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="6042px"></svg>
	<svg id="overlay" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="6042px"></svg>

<?php
	$cities = array();
	$jobcities = array();
	$persons = array();
	$names = array();

	$sql = 'SELECT place_id, start, end FROM dnb_person_place WHERE person_id = "'.$request_id.'"';
	$result = query_mysql($sql, $link);
	if ($result) {
	    while ($row = mysql_fetch_array($result)) {
				$city = array();
				$exist = false;
				foreach ($cities as $tcity) {
					if($tcity[0] == $row[0] && $tcity[1] == $row[1] && $tcity[2] == $row[2]){
						$exist = true;
					}
				}
				if(!$exist){

			    	$sql = 'SELECT person_id, start, end FROM dnb_person_place WHERE place_id = "'.$row[0].'" AND ((start <= '.$row[1].' AND ( end >= '.$row[1].' AND end <= '.$row[2].')) OR (start <= '.$row[1].' AND end >= '.$row[2].') OR ((start >= '.$row[1].' AND start <= '.$row[2].') AND ( end <= '.$row[2].' AND end >= '.$row[1].')) OR (end >= '.$row[2].' AND ( start >= '.$row[1].' AND start <= '.$row[2].')))';
			    	$result1 = query_mysql($sql, $link);
					if ($result1) {
					    while ($row1 = mysql_fetch_array($result1)) {
					    	if($row1[0] != $request_id){
						    	$found = false;
						    	foreach ($city as $p) {
						    		if($p[0] == $row1[0]){
						    			$found = true;
						    		}
						    	}
						    	if(!$found){
						    		array_push($city, $row1);
						    	}

						    	$found = false;
						    	foreach ($persons as $p) {
						    		if($p == $row1[0]){
						    			$found = true;
						    		}
						    	}
						    	if(!$found){
						    		array_push($persons, $row1[0]);
						    	}
						    }
					    }
					}
					mysql_free_result($result1);

					$cname = "NA";
					$sql = 'SELECT name FROM dnb_places WHERE id = "'.$row[0].'"';
			    	$result1 = query_mysql($sql, $link);
					if ($result1) {
						while ($row1 = mysql_fetch_array($result1)) {
							$cname = $row1[0];
						}
					}
					mysql_free_result($result1);

			    	array_push($cities, array($row[0], $row[1], $row[2], $cname, $city));
			    }
			
	    }
	}
	mysql_free_result($result);

	if(count($persons)>=1){
		$sql = 'SELECT id, name, job_ids FROM dnb_persons WHERE ';
		$first = true;
		foreach ($persons as $person) {
			if(!$first){
				$sql .= ' OR ';
			}
			$sql .= 'id="'.$person.'"';
			$first = false;
		}
		$result = query_mysql($sql, $link);
		if ($result) {
		    while ($row = mysql_fetch_array($result)) {
		    	$names[$row[0]] = array($row[1], json_decode($row[2]));
		    }
		}
		mysql_free_result($result);
	}

	foreach ($cities as $city) {
		$tcity = array(
				$city[0],
				$city[1],
				$city[2],
				$city[3],
				array()
			);

		foreach ($city[4] as $person) {
			$match = false;

			foreach ($names[$person[0]][1] as $job) {
				foreach ($jobs as $pjob) {
					if($job == $pjob){
						$match = true;
					}
				}
			}

			if($match){
				array_push($tcity[4], $person);
			}
		}

		array_push($jobcities, $tcity);
	}

	echo '<script> var cities = '.json_encode($cities).';</script>';
	echo '<script> var jobcities = '.json_encode($jobcities).';</script>';
	echo '<script> var names = '.json_encode($names).';</script>';

?>
<script>
	var min = 99999999;
	var max = 0;
	for(var i = 0; i<cities.length; i++){
		if(cities[i][4].length>max){
			max = cities[i][4].length;
		}
		if(cities[i][4].length<min){
			min = cities[i][4].length;
		}
	}

	var r_max = 250;
	var r_min = 100;

	if(max == 0){max = 1;}

	var main = d3.select("#main");
    var width = parseInt(main.style("width"));

	var circles = d3.select("#main").selectAll("circle").data(cities)
		.enter().append("circle")
		.style("fill", "rgba(0,0,0,0.2)")
		.attr("r", function(d){
			return (d[4].length/max)*(r_max-r_min) + r_min;
		})
		.attr("cx", function(d, i) { 
			return width/2;
		})
		.attr("cy", function(d, i) { 
			var y = 50;
			for(var c = 0; c<i; c++){
				y += ((cities[c][4].length/max)*(r_max-r_min) + r_min)*2 + 40;
			}
			y += ((cities[i][4].length/max)*(r_max-r_min) + r_min);

			return y;
		});
		
		circles.on("click", function(d){
			updateOverlay(d);
		});

		d3.select("#overlay").selectAll("rect").data([true])
			.enter().append("rect")
			.attr("width", width)
			.attr("height", 6042)
			.style("fill", "rgba(255,255,255,0.9)")
			.on("click", function(){
				d3.select("#overlay").style("display", "none");				
			});


	function updateOverlay(data){
		d3.select("#overlay").selectAll("text.headline").data([]).exit().remove();

		d3.select("#overlay").selectAll("text.headline").data([data])
			.enter().append("text")
			.attr("class", "headline")
			.text(function(d){return d[3];})
			.attr("text-anchor", "middle")
			.attr("x", width/2)
			.attr("y", 30)
			.attr("width", width)
			.style("fill", "#000000")
			.style("font-family", "Arial")
			.style("font-size", "20px")
			.on("click", function(d){
				window.location = "city.php?request_id="+d[0];
			});

		d3.select("#overlay").selectAll("text.people").data([]).exit().remove();

		d3.select("#overlay").selectAll("text.people").data(data[4])
			.enter().append("text")
			.attr("class", "people")
			.text(function(d){return names[d[0]][0];})
			.attr("text-anchor", "middle")
			.attr("x", width/2)
			.attr("y", function(d, i){
				return 50 + i*17;
			})
			.attr("width", width)
			.style("fill", "#000000")
			.style("font-family", "Arial")
			.style("font-size", "12px")
			.on("click", function(d){
				window.location = "person.php?request_id="+d[0];
			});


		d3.select("#overlay").style("display", "block");
	}

	var labels1 = d3.select("#main").selectAll("text.city").data(cities)
		.enter().append("text")
		.attr("class", "city")
		.style("fill", "#000000")
		.style("font-family", "Arial")
		.style("font-size", "20px")
		.style("width", function(d){
			return (d[4].length/max)*(r_max-r_min) + r_min;
		})
		.text(function(d){
			return d[3];
		})
		.style("text-align", "center")
		.attr("text-anchor", "middle")
		.attr("x", function(d){ return width/2; })
		.attr("y", function(d, i){
			var y = 50;
			for(var c = 0; c<i; c++){
				y += ((cities[c][4].length/max)*(r_max-r_min) + r_min)*2 + 40;
			}
			y += ((cities[i][4].length/max)*(r_max-r_min) + r_min);

			return y-10;
		});

	var labels2 = d3.select("#main").selectAll("text.people").data(cities)
		.enter().append("text")
		.attr("class", "people")
		.style("fill", "#000000")
		.style("font-family", "Arial")
		.style("font-size", "15px")
		.style("width", function(d){
			return (d[4].length/max)*(r_max-r_min) + r_min;
		})
		.text(function(d){
			var t = d[4].length + " other people";
			if(d[4].length<1){
				t = "no one else";
			}
			return t;
		})
		.style("text-align", "center")
		.attr("text-anchor", "middle")
		.attr("x", function(d){ return width/2; })
		.attr("y", function(d, i){
			var y = 50;
			for(var c = 0; c<i; c++){
				y += ((cities[c][4].length/max)*(r_max-r_min) + r_min)*2 + 40;
			}
			y += ((cities[i][4].length/max)*(r_max-r_min) + r_min);

			return y+30;
		});

	var labels3 = d3.select("#main").selectAll("text.time").data(cities)
		.enter().append("text")
		.attr("class", "time")
		.style("fill", "#000000")
		.style("font-family", "Arial")
		.style("font-size", "15px")
		.style("width", function(d){
			return (d[4].length/max)*(r_max-r_min) + r_min;
		})
		.text(function(d){
			return d[1] + " - " + d[2];
		})
		.style("text-align", "center")
		.attr("text-anchor", "middle")
		.attr("x", function(d){ return width/2; })
		.attr("y", function(d, i){
			var y = 50;
			for(var c = 0; c<i; c++){
				y += ((cities[c][4].length/max)*(r_max-r_min) + r_min)*2 + 40;
			}
			y += ((cities[i][4].length/max)*(r_max-r_min) + r_min);

			return y+10;
		});

var cdata;

d3.select("#relativeOpt").on("change", function(){
	var durationTime = 2500;

  if(d3.select(this).property("checked")){
  	cdata = jobcities;
  }else{
  	cdata = cities;
  }

	circles.data(cdata).transition().duration(durationTime).attr("r", function(d){
		return (d[4].length/max)*(r_max-r_min) + r_min;
	})
	.attr("cx", function(d, i) { 
		return width/2;
	})
	.attr("cy", function(d, i) { 
		var y = 50;
		for(var c = 0; c<i; c++){
			y += ((cdata[c][4].length/max)*(r_max-r_min) + r_min)*2 + 40;
		}
		y += ((cdata[i][4].length/max)*(r_max-r_min) + r_min);

		return y;
	});

	circles.on("click", function(d){
			updateOverlay(d);
		});

labels3.data(cdata).transition().duration(durationTime).style("width", function(d){
		return (d[4].length/max)*(r_max-r_min) + r_min;
	})
	.text(function(d){
		return d[1] + " - " + d[2];
	})
	.attr("y", function(d, i){
		var y = 50;
		for(var c = 0; c<i; c++){
			y += ((cdata[c][4].length/max)*(r_max-r_min) + r_min)*2 + 40;
		}
		y += ((cdata[i][4].length/max)*(r_max-r_min) + r_min);

		return y+10;
	});
labels2.data(cdata).transition().duration(durationTime).style("width", function(d){
		return (d[4].length/max)*(r_max-r_min) + r_min;
	})
	.text(function(d){
		var t = d[4].length + " other people";
		if(d[4].length<1){
			t = "no one else";
		}
		return t;
	})
	.attr("y", function(d, i){
		var y = 50;
		for(var c = 0; c<i; c++){
			y += ((cdata[c][4].length/max)*(r_max-r_min) + r_min)*2 + 40;
		}
		y += ((cdata[i][4].length/max)*(r_max-r_min) + r_min);

		return y+30;
	});
labels1.data(cdata).transition().duration(durationTime).style("width", function(d){
		return (d[4].length/max)*(r_max-r_min) + r_min;
	})
	.attr("y", function(d, i){
		var y = 50;
		for(var c = 0; c<i; c++){
			y += ((cdata[c][4].length/max)*(r_max-r_min) + r_min)*2 + 40;
		}
		y += ((cdata[i][4].length/max)*(r_max-r_min) + r_min);

		return y-10;
	});

});
</script>
</body>
</html>