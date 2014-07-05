<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="js/leaflet.css" />
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

</style>
<style>

svg {
  font: 10px sans-serif;
}

.area {
  fill: #999999;
  clip-path: url(#clip);
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.brush .extent {
  stroke: #fff;
  fill-opacity: .125;
  shape-rendering: crispEdges;
}

#map{
  width:100%;
  height:500px;
  margin: 15px 0;
}

#relativeOptHolder{
  float:right;
  margin-top:-39px;
  margin-right: 10px;
}

</style>
<script src="js/leaflet.js"></script>
<script src="js/jquery-2.1.0.min.js"></script>
<script src="js/proj4js-compressed.js"></script>
<script src="js/proj4leaflet.js"></script>
<script src="js/fn.js"></script>
</head>
  <body>
    <div id="header"><img src="images/logo.gif" /><h1>/ Data Explorer</h1></div>
    <div id="relativeOptHolder"><input type="checkbox" id="relativeOpt" value="1" /><label>Places / Persons</label></div>
    <div id="map"></div>
<script src="js/d3.v3.min.js"></script>
<script>
      var map, layer, layer_webmer, request, start_o, end_o, start_y, end_y;
      var people = false;
      var init = false;
      start_y = start_o = 0;
      end_y = end_o = 2014;

      $(document).ready(function() {
        layer_webmer = L.layerGroup();
        map = L.map('map',{layers: [layer_webmer]}).setView([50.126055, 8.652357], 5);
        layer = L.layerGroup();
        layer.addTo(map);

        map.on('zoomend', function(e){ setHeatmap(); });
        map.on('dragend', function(e){ setHeatmap(); });
        map.on('moveend', function(e){ setHeatmap(); });
        
        var osmAttr = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>';
        var mqTilesAttr = 'Tiles &copy; <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" />';

        L.tileLayer('http://{s}.tile.stamen.com/toner-lite/{z}/{x}/{y}.png', {attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',subdomains: 'abcd',minZoom: 3,maxZoom: 21}).addTo(map);
        //L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png',{subdomains: '1234',attribution: osmAttr + ', ' + mqTilesAttr}).addTo(map);

        Proj4js.defs["SR-ORG:7483"] = "+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs";
        init = true;
        setHeatmap();
      });

      function setHeatmap(){
        if(map.getZoom()>3){
          var bounds = map.getBounds();
          var ne = ToWebMercator(bounds.getNorthEast().lng, bounds.getNorthEast().lat);
          var sw = ToWebMercator(bounds.getSouthWest().lng, bounds.getSouthWest().lat);
          ne[0]+=20037508;ne[1]+=20037508;sw[0]+=20037508;sw[1]+=20037508;
          if(ne[0]>sw[0]){max_x = ne[0];min_x = sw[0];}else{max_x = sw[0];min_x = ne[0];}
          if(ne[1]>sw[1]){max_y = ne[1];min_y = sw[1];}else{max_y = sw[1];min_y = ne[1];}
        }else{
          min_x = max_y = max_x = min_y = 0;
        }

        if(map.getZoom()>12){
          if(start_y != start_o || end_y != end_o){
            var trequest = "deliver_locations_dynmaic.php?min_y="+min_y+"&max_y="+max_y+"&min_x="+min_x+"&max_x="+max_x+"&start="+start_y+"&end="+end_y;  
          }else{
            var trequest = "deliver_locations.php?min_y="+min_y+"&max_y="+max_y+"&min_x="+min_x+"&max_x="+max_x;
          }
          if(trequest!=request){
            request = trequest;
            $.getJSON(request, function( data ) {
              //Add marker and Popups with links to the city page
              layer.clearLayers();
              var markerIcon = L.icon({
                  iconUrl: 'images/marker.png',
                  iconRetinaUrl: 'images/marker2x.png',
                  iconSize: [27, 50],
                  iconAnchor: [13.5, 50],
                  popupAnchor: [0, 0]
                });
              for(var i = 0; i<data[0].length; i++){
                L.marker([data[0][i][0], data[0][i][1]], {icon: markerIcon}).bindPopup('<a href="city.php?request_id='+data[0][i][3]+'">'+data[0][i][2]+'</a>').addTo(layer);
              }
            });
          }
        }else{
          if(start_y != start_o || end_y != end_o){
            if(people){
              var trequest = "deliver_hex_dynamic_people.php?zoom="+map.getZoom()+"&min_y="+min_y+"&max_y="+max_y+"&min_x="+min_x+"&max_x="+max_x+"&start="+start_y+"&end="+end_y;  
            }else{
              var trequest = "deliver_hex_dynamic.php?zoom="+map.getZoom()+"&min_y="+min_y+"&max_y="+max_y+"&min_x="+min_x+"&max_x="+max_x+"&start="+start_y+"&end="+end_y;  
            }
          }else{
            if(people){
              var trequest = "deliver_hex_people.php?zoom="+map.getZoom()+"&min_y="+min_y+"&max_y="+max_y+"&min_x="+min_x+"&max_x="+max_x;  
            }else{
              var trequest = "deliver_hex.php?zoom="+map.getZoom()+"&min_y="+min_y+"&max_y="+max_y+"&min_x="+min_x+"&max_x="+max_x;  
            }
          }
          if(trequest!=request){
            request = trequest;
            $.getJSON(request, function( data ) {
              var geo;
              layer.clearLayers();
              for(var i = 0; i<data.length; i++){
                geo = L.Proj.geoJson(data[i], { style: function(feature){ return feature.properties && feature.properties.style; }}).on('dblclick', function(event){map.panTo(event.latlng);map.zoomIn();});
                geo.addTo(layer);
              }
            });
          }
        }
      }
    </script>
    <script>

d3.select("#relativeOpt").on("change", function(){
  people = d3.select(this).property("checked");
  setHeatmap();   
});

var svg_height = 200;
var svg_width = 1400;

var margin = {top: 10, right: 10, bottom: 10, left: 40},
    margin2 = {top: 10, right: 10, bottom: 20, left: 40},
    width = svg_width - margin.left - margin.right,
    height = 80,
    height2 = 80;

var parseDate = d3.time.format("%Y").parse;

var x = d3.time.scale().range([0, width]),
    x2 = d3.time.scale().range([0, width]),
    y = d3.scale.linear().range([height, 0]),
    y2 = d3.scale.linear().range([height2, 0]);

var xAxis = d3.svg.axis().scale(x).orient("bottom"),
    xAxis2 = d3.svg.axis().scale(x2).orient("bottom"),
    yAxis = d3.svg.axis().scale(y).orient("left");

var brush = d3.svg.brush()
    .x(x2)
    .on("brush", brushed);

var area2 = d3.svg.area()
    .interpolate("monotone")
    .x(function(d) { return x2(d.date); })
    .y0(height2)
    .y1(function(d) { return y2(d.amount); });

var svg = d3.select("body").append("svg")
    .attr("width", svg_width)
    .attr("height", svg_height);

svg.append("defs").append("clipPath")
    .attr("id", "clip")
  .append("rect")
    .attr("width", svg_width)
    .attr("height", svg_height);

var context = svg.append("g")
    .attr("class", "context")
    .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");

d3.csv("sp500sqrt.csv", type, function(error, data) {
  x.domain(d3.extent(data.map(function(d) { return d.date; })));
  y.domain([0, d3.max(data.map(function(d) { return d.amount; }))]);
  x2.domain(x.domain());
  y2.domain(y.domain());

  context.append("path")
      .datum(data)
      .attr("class", "area")
      .attr("d", area2);

  context.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height2 + ")")
      .call(xAxis2);

  context.append("g")
      .attr("class", "x brush")
      .call(brush)
    .selectAll("rect")
      .attr("y", -6)
      .attr("height", height2 + 7);
});

function brushed() {
  var selection = brush.extent();
  start_y = selection[0].getFullYear();
  end_y = selection[1].getFullYear();
  if(start_y == end_y){
    start_y = start_o;
    end_y = end_o;
  }
  //cancel request....!!!!!
  if(init = true){
    setHeatmap();
  }
}

function type(d) {
  d.date = parseDate(d.date);
  d.amount = +d.amount;
  return d;
}

</script>
</body>
</html>