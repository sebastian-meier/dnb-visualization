<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<title>DNB</title>
		<script src="js/d3.v3.min.js" type="text/javascript" charset="utf-8"></script>
		<style type="text/css">
			html, body{
				margin:0;
				padding:0;
				border:0;
			}

			#city1, #city2, #city3, #bg, #overlay{
				position: absolute;
				top:150px;
				left: 0;
			}

			#city1{
				left: 50px;
			}

			#city2{
				left:400px;
			}

			#city3{
				left:750px;
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

			#controlBar ul, #controlBar ul li{
				list-style: none;
				margin:0;
				padding:0;
			}

			#controlBar{
				margin-left:10px;
			}

			#controlBar ul li{
				padding-left:6px;
				padding-top:17px;
				float:left;
				display: block;
				width:344px;
			}

			#controlBar ul li:last-child{
				width:320px;
			}

			select{
				background:none;
				border:1px solid #dddddd;
				color:#aaaaaa;
			}

			select:focus { 
				outline: 1px solid #555555; 
			}

			label{
				font-size: 10px;
				font-family: Arial;
				color:#aaaaaa;
			}

			#selectjob{
				width:200px;
				margin-left:10px;
			}

			#loader1, #loader2, #loader3{
				position: absolute;
				top:170px;
				width:50px;
				height:50px;
				display: block;
			}

			#loader1{
				left:100px;
			}

			#loader2{
				left:450px;	
			}

			#loader3{
				left:800px;
			}

		</style>
	</head>
	<body>
		<div id="header"><img src="images/logo.gif" /><h1>/ Data Explorer</h1></div>
		<div id="controlBar">
			<ul>
				<li>
					<select id="select1" data-id="1">
						<option>Please Choose</option>
					</select>
				</li>
				<li>
					<select id="select2" data-id="2">
						<option>Please Choose</option>
					</select>
				</li>
				<li>
					<select id="select3" data-id="3">
						<option>Please Choose</option>
					</select>
				</li>
				<li>
					<input type="checkbox" id="relativeOpt" value="1" /><label>Relative/Absolute</label>
					<select id="selectjob" data-id="job">
						<option>Filter by Job</option>
					</select>
				</li>
			</ul>
		</div>
		<svg id="bg" xmlns="http://www.w3.org/2000/svg" version="1.1" width="2000" height="6042"></svg>
		<svg id="city1" xmlns="http://www.w3.org/2000/svg" version="1.1" height="6042" width="300"></svg>
		<svg id="city2" xmlns="http://www.w3.org/2000/svg" version="1.1" height="6042" width="300"></svg>
		<svg id="city3" xmlns="http://www.w3.org/2000/svg" version="1.1" height="6042" width="300"></svg>
		<svg id="overlay" xmlns="http://www.w3.org/2000/svg" version="1.1" width="2000" height="6042"></svg>
		<div id="loader1"></div><div id="loader2"></div><div id="loader3"></div>
		<script>var firstCity = <?php if(isset($_GET["request_id"])){echo '"'.$_GET["request_id"].'"';}else{ echo 'false';} ?>;</script>
		<script type="text/javascript">

			var cities;
			var jobs;
			var currentJob = false;
			var inits = [false, false, false, false];
			var overlay = false;

			function loader(config) {
			  return function() {
			    var radius = Math.min(config.width, config.height) / 2;
			    var tau = 2 * Math.PI;

			    var arc = d3.svg.arc()
			            .innerRadius(radius*0.5)
			            .outerRadius(radius*0.9)
			            .startAngle(0);

			    var svg = d3.select(config.container).append("svg")
			        .attr("id", config.id)
			        .attr("width", config.width)
			        .attr("height", config.height)
			      .append("g")
			        .attr("transform", "translate(" + config.width / 2 + "," + config.height / 2 + ")")

			    var background = svg.append("path")
			            .datum({endAngle: 0.33*tau})
			            .style("fill", "#4D4D4D")
			            .attr("d", arc)
			            .call(spin, 1500)

			    function spin(selection, duration) {
			        selection.transition()
			            .ease("linear")
			            .duration(duration)
			            .attrTween("transform", function() {
			                return d3.interpolateString("rotate(0)", "rotate(360)");
			            });

			        setTimeout(function() { spin(selection, duration); }, duration);
			    }

			    function transitionFunction(path) {
			        path.transition()
			            .duration(7500)
			            .attrTween("stroke-dasharray", tweenDash)
			            .each("end", function() { d3.select(this).call(transition); });
			    }

			  };
			}


			var myLoader1 = loader({width: 50, height: 50, container: "#loader1", id: "loader_1"}); myLoader1();
			var myLoader2 = loader({width: 50, height: 50, container: "#loader2", id: "loader_2"}); myLoader2();
			var myLoader3 = loader({width: 50, height: 50, container: "#loader3", id: "loader_3"}); myLoader3();

			d3.select("#loader1").style("display", "none");
			d3.select("#loader2").style("display", "none");
			d3.select("#loader3").style("display", "none");

			d3.json("city_list.json.php", function(error, json) {
				if (error) return console.warn(error);
				cities = json;
				for(var i = 1; i<4; i++){
					d3.select("#select"+i).selectAll("option").data(json).enter().append("option")
						.attr("value", function(d){ return d[1]; })
						.attr("label", function(d){ if(d[2]>0){ return d[0]+' ('+d[2]+')'; }else{ return d[0]; } });

					d3.select("#select"+i).on("change", function(){
						if(d3.select(this).property('selectedIndex')==0){
							removeCity(d3.select(this).attr("data-id"));
						}else{
							drawCity("city.json.php?place_id="+cities[d3.select(this).property('selectedIndex')][1], d3.select(this).attr("data-id"));	
						}
					});
				}

				if(firstCity){
					drawCity("city.json.php?place_id="+firstCity, 1);
					d3.select('#select1').property('value', firstCity);
				}
			});

			d3.json("job.json.php", function(error, json) {
				if (error) return console.warn(error);
				jobs = json;
				d3.select("#selectjob").selectAll("option").data(json).enter().append("option")
					.attr("value", function(d){ return d[1]; })
					.attr("label", function(d){ if(d[2]>0){ return d[0]+' ('+d[2]+')'; }else{ return d[0]; } });

				d3.select("#selectjob").on("change", function(){
					if(d3.select(this).property('selectedIndex')==0){
						resetJob();
					}else{
						setJob(jobs[d3.select(this).property('selectedIndex')][1]);
					}
				});
			});

			function resetJob(){
				for(var i = 1; i<4; i++){
					years[i] = data[i].years;
				}
				updateHistograms();
			}

			function setJob(job_id){
				currentJob = job_id;
				for(var i = 1; i<4; i++){
					if(inits[i]){
						var t_years = [];
						for(var y = 0; y<2015; y++){
							var t_year = [];
							for(var p = 0; p<data[i].years[y].length; p++){
								if(
									data[i].persons[data[i].years[y][p]][1] != null && 
									data[i].persons[data[i].years[y][p]][1].indexOf(job_id)>-1
								){
									t_year.push(data[i].years[y][p]);
								}
							}
							t_years.push(t_year);
						}
						years[i] = t_years;
					}
				}
				updateHistograms();
			}

			function updateHistograms(){
				for(var i = 1; i<4; i++){
					if(inits[i]){
						updateHistogram(i, 2500);
					}
				}
			}

			d3.select("#relativeOpt").on("change", function(){
				use_s_max = d3.select(this).property("checked");
				updateHistograms();		
			});

			var data = [];
			var years = [];
			var maxs = [0,0,0,0];
			var top_offset = 20;
			var s_max = 0;
			var use_s_max = false;

			var labels = [2014];
			for(var i = 2000; i>0; i-=50){
				labels.push(i);
			}

			var lineFunction = d3.svg.line()
				.x(function(d) { return d.x; })
				.y(function(d) { return d.y; })
				.interpolate("linear");

			d3.select("#bg").selectAll("path.bglines").data(labels)
				.enter().append("path")
				.attr("class", "bglines")
				.attr("d", function(d){
					var lineData = [
						{
							"x":0,
							"y":(2015-d)*3 + top_offset
						},{
							"x":2000,
							"y":(2015-d)*3 + top_offset
						}
					];

					return lineFunction(lineData);
				})
				.style("stroke", "#dddddd")
				.attr("width", 2000)
				.attr("stroke-width", 1)
				.attr("fill", "none");

			var activeLine = d3.select("#bg").selectAll("path.activeLine").data([150])
				.enter().append("path")
				.attr("class", "activeLine")
				.attr("d", function(d){
					var lineData = [
						{
							"x":0,
							"y":d
						},{
							"x":2000,
							"y":d
						}
					];

					return lineFunction(lineData);
				})
				.style("stroke", "#ffdddd")
				.attr("width", 2000)
				.attr("stroke-width", 2)
				.attr("fill", "none");

			var activeYear = d3.select("#bg").selectAll("rect.yearbg").data([150])
				.enter().append("rect")
				.attr("class", "yearbg")
				.style("fill", "#ffdddd")
				.attr("width", 30)
				.attr("height", 15)
				.attr("x", 1100)
				.attr("y", function(d){ return d-7; });

			var activeLabel = d3.select("#bg").selectAll("text.yearlabel").data([150])
				.enter().append("text")
				.attr("class", "yearlabel")
				.style("fill", "rgba(0,0,0,0.4)")
				.style("font-family", "Arial")
				.style("font-size", "10px")
				.attr("x", 1103)
				.attr("y", function(d){ return d-7; });

			var cityLabelBg = d3.select("#overlay").selectAll("rect.citybg").data([[1,-150],[2,-150],[3,-150]])
				.enter().append("rect")
				.attr("class", "citybg")
				.style("fill", "#ff0000")
				.attr("width", 30)
				.attr("height", 15)
				.attr("x", function(d){ return ((d[0]-1)*350+50); })
				.attr("y", function(d){ return ((Math.floor(d[1]/3.0)*3.0)-7); });			

			var cityLabel = d3.select("#overlay").selectAll("text.citylabel").data([[1,-150],[2,-150],[3,-150]])
				.enter().append("text")
				.attr("class", "citylabel")
				.style("fill", "#000000")
				.style("font-family", "Arial")
				.style("font-size", "10px")
				.attr("x", function(d){ return ((d[0]-1)*350+54); })
				.attr("y", function(d){ return ((Math.floor(d[1]/3.0)*3.0)-7); });

			var overlaybg = d3.select("#overlay").selectAll("rect.bg").data([1])
				.enter().append("rect")
				.attr("class", "bg")
				.attr("width", 2000)
				.attr("height", 6042)
				.style("fill", "rgba(255,255,255,0.0)")
				.on("click", function(){
					if(overlay){
						overlay = false;
					}else if((inits[1]||inits[2]||inits[3])){
						overlay = true;
					}
				});

			var overlaylabels = d3.select("#overlay").selectAll("text.headcol").data([[1,"",20],[2,"",370],[3,"",720]]).enter().append("text")
				.attr("class", "headcol")
				.text(function(d){ return d[1]; })
				.attr("x", function(d){ return d[2]; })
				.attr("y", 20)
				.style("font-size", 10)
				.style("font-family", "Arial")
				.style("font-weight", "bold");

			d3.select(window).on("click", function() {
				var y = d3.event.pageY;
				if(y>top_offset+150){
					var year = (2071-Math.floor(y/3.0));
					if(overlay && (inits[1]||inits[2]||inits[3])){
						overlaybg.style("fill", "rgba(255,255,255,0.9)");
						overlaylabels.data([[1,year,20],[2,year,370],[3,year,720]]).text(function(d){ return d[1]; });
						var personData = [];
						for(var i = 1; i<4; i++){
							if(inits[i]){
								var t_year = years[i][2014-year];
								for(var j = 0; j<t_year.length; j++){
									personData.push(new Array( i, j, data[i].persons[t_year[j]][0], t_year[j] ));
								}
							}
						}
						d3.select("#overlay").selectAll("text.list").data(personData).enter().append("text")
							.attr("x", function(d){ return 49+(d[0]-1)*350; })
							.attr("class", "list")
							.style("font-size", 10)
							.style("font-family", "Arial")
							.attr("y", function(d){ return 36+(d[1]-1)*16; })
							.text(function(d){return d[2];})
							.on("click", function(d){
								window.location = "person.php?request_id="+d[3];
							});

					}else{
						overlaylabels.data([[1,"",20],[2,"",370],[3,"",720]]).text(function(d){ return d[1]; });
						d3.select("#overlay").selectAll("text.list").data([]).exit().remove();
						overlaybg.style("fill", "rgba(255,255,255,0.0)");
					}
					updateMouse(0);
				}
			});

			d3.select(window).on("mousemove", function() {
				var y = d3.event.pageY;
				updateMouse(y);
			});

			function updateMouse(y){
				if(y>top_offset+150 && !overlay){
					activeLine.data([y-150]).attr("d", function(d){
						var lineData = [
							{
								"x":0,
								"y":Math.floor(d/3.0)*3.0+1
							},{
								"x":2000,
								"y":Math.floor(d/3.0)*3.0+1
							}
						];

						return lineFunction(lineData);
					});

					activeYear.data([y-150]).attr("y", function(d){ return ((Math.floor(d/3.0)*3.0)-7); });
					activeLabel.data([y-150]).text(function(d){return (2021-Math.floor(d/3.0));}).attr("y", function(d){ return ((Math.floor(d/3.0)*3.0)+4); });

					cityLabelBg.data([[1, y-150], [2, y-150], [3, y-150]])
						.attr("width", function(d){ 
							var r = 0;
							if(inits[d[0]]){
								var l = years[d[0]][(2014 - (2021-Math.floor(d[1]/3.0)))].length;

								if(l>9999){
									r = 34;
								}else if(l>999){
									r = 30;
								}else if(l>99){
									r = 26;
								}else if(l>9){
									r = 22;
								}else if(l<1){
									r = 0;
								}else{
									r = 18;
								}
							}
							return r;
						})
						.attr("y", function(d){ 
							var r = -150;
							if(inits[d[0]]){
								r = ((Math.floor(d[1]/3.0)*3.0)-6)
							}
							return r; 
						});
					cityLabel.data([[1, y-150], [2, y-150], [3, y-150]]).text(function(d){
						var r = "";
						if(inits[d[0]]){
							r = years[d[0]][(2014 - (2021-Math.floor(d[1]/3.0)))].length;
							if(r<1){r = "";}
						}
						return r;
					}).attr("y", function(d){ 
						var r = -150;
						if(inits[d[0]]){
							r = ((Math.floor(d[1]/3.0)*3.0)+6)
						}
						return r; 
					});
				
					var ny = Math.floor((y-top_offset-148)/3);
					for(var i =1; i<4; i++){
						if(inits[i]){
							//if(i==3){ny++;}
							d3.select('#city'+i).selectAll("rect").style('fill', 'black').filter(':nth-child('+ny+')').style('fill', 'red');
						}
					}

				}else{
					activeLine.data([-150]).attr("d", function(d){
						var lineData = [
							{
								"x":0,
								"y":Math.floor(d/3.0)*3.0+1
							},{
								"x":2000,
								"y":Math.floor(d/3.0)*3.0+1
							}
						];

						return lineFunction(lineData);
					});

					d3.select('#city'+i).selectAll("rect").style('fill', 'black');

					activeYear.data([-150]).attr("y", function(d){ return ((Math.floor(d/3.0)*3.0)-7); });
					activeLabel.data([-150]).attr("y", function(d){ return ((Math.floor(d/3.0)*3.0)+4); });
					cityLabel.data([[1,-150],[2,-150],[3,-150]]).attr("y", function(d){ return ((Math.floor(d[1]/3.0)*3.0)+4); });
					cityLabelBg.data([[1,-150],[2,-150],[3,-150]]).attr("y", function(d){ return ((Math.floor(d[1]/3.0)*3.0)+4); });
				}
			}

			/*

			Add click event to show names
			Filter by Jobs

			*/

			function drawLabels(className, offset){
				d3.select("#bg").selectAll("text."+className).data(labels)
					.enter().append("text")
					.attr("class", className)
					.attr("x", offset)
					.text(function(d){return d;})
					.attr("y", function(d){ return (2015-d)*3 + top_offset - 3; })
					.style("fill", "#aaaaaa")
					.style("font-family", "Arial")
					.style("font-size", 10);
			}

			drawLabels("col1", 20);
			drawLabels("col2", 370);
			drawLabels("col3", 720);
			drawLabels("col4", 1070);
			
			function drawCity(source, target){
				d3.select("#loader"+target).style("display", "block");
				d3.json(source, function(error, json) {
					if (error) return console.warn(error);
					json.years.reverse();
					data[target] = json;
					maxs[target] = data[target].max;
					years[target] = json.years;
					var omax = s_max;

					s_max = 0;
					for(var i = 1; i<4; i++){
						if(maxs[i]>s_max){
							s_max = maxs[i];
						}
					}

					d3.select("#loader"+target).style("display", "none");

					if(inits[target]){
						updateHistogram(target, 2500);	
					}else{
						inits[target] = true;
						drawHistogram(target, 0);	
					}
				});
			}

			function removeCity(target){
				inits[target] = false;
				d3.select("#city"+target).selectAll("rect").remove();
			}

			function drawHistogram(target, durationTime){
				d3.select("#city"+target).selectAll("rect").data(years[target])
					.enter().append("rect")
	      			.attr("x", 0)
	      			.attr("id", function(d,i){ return "year_"+(2014-i);})
	      			.attr("title", function(d,i){ return (2014-i);})
	      			.attr("y", function(d,i){return (3*i+1 + top_offset);})
	      			.attr("height", 2)
	      			.attr("width", function(d){
	      				var m = data[target].max;
	      				if(use_s_max){m = s_max;}
	      				var r = (d.length/m)*300;
	      				if(r<1 && r>0){r=1;}
	      				return r; 
	      			})
	      			.style("fill", "black");
			}

			function updateHistogram(target, durationTime){
				d3.select("#city"+target).selectAll("rect").data(years[target]).transition().duration(durationTime).attr("width", function(d){
	      				var m = data[target].max;
	      				if(use_s_max){m = s_max;}
	      				var r = (d.length/m)*300;
	      				if(r<1 && r>0){r=1;}
	      				return r; 
	      			});
			}

		</script>
	</body>
</html>