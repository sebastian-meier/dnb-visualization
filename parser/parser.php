<?php

include("../visualization/config.php");
ini_set('default_socket_timeout', 900);
mysql_query("SET NAMES 'utf8'");

header("Content-Type: text/html; charset=utf-8");

//Create the XML Parser
$parser = xml_parser_create("UTF-8");
xml_set_object($parser);
xml_set_element_handler($parser, "startTag", "endTag");
xml_set_character_data_handler($parser, "contentTag");

//Which fields are we trying to extract from the xml
$fields = array(
    array("024", "url"),
    array("551", "wirkungsorte"),
    array("100", "name"),
    array("550", "beruf"),
    array("548", "lebenszeit")

    /*
    548 Lebenszeit XXXX-XXXX
    024 URL /ID
    100 Name
    400 Alternative Name
    700 Name-1
    913 Name-2
    550 Beruf
    551 Wirkungsorte
    548 Lebenszeit
    */
);

$currentPerson = array();
$currentField = false;

session_start();

$_SESSION["data"] = array();
$_SESSION["field"] = false;
$_SESSION["code"] = false;
$_SESSION["count"] = 0;
$_SESSION["infield"] = false;

//Check if this person was already added to the database
function dbExists($db_name, $db_id){
    global $link;

    $r = false;
    $sql = 'SELECT `id` FROM `'.$db_name.'` WHERE `id` = "'.$db_id.'"';
    $result = query_mysql($sql, $link);
    if ($result) {
        $num = mysql_num_rows($result);
        if($num>=1){
            $r = true;
        }
    }
    mysql_free_result($result);
    return $r;
}

//The next functions are the xml-parsers there is nothing special about it
//They are actually collecting all data in an array until the record-tag
//is closed and then the data is stored in the database
function contentTag($parser, $content){
    if($_SESSION["field"] && (strlen(trim($content))>=1)){
        if($_SESSION["infield"]){
            $_SESSION["data"][$_SESSION["field"]][(count($_SESSION["data"][$_SESSION["field"]])-1)][$_SESSION["code"]][count($_SESSION["data"][$_SESSION["field"]][(count($_SESSION["data"][$_SESSION["field"]])-1)][$_SESSION["code"]])-1] .= $content;
        }else{
            $_SESSION["infield"] = true;
            array_push($_SESSION["data"][$_SESSION["field"]][(count($_SESSION["data"][$_SESSION["field"]])-1)][$_SESSION["code"]], $content);
        }
    }
}

function startTag($parser, $name, $attribs){
    global $fields;

    switch(strtolower($name)){
        case "record":
            $_SESSION["data"] = array();
            foreach ($fields as $field) {
                $_SESSION["data"][$field[1]] = array();
            }
            $_SESSION["field"] = false;
        break;
        case "datafield":
            $found = false;
            foreach ($fields as $field) {
                if($field[0] == $attribs["TAG"]){
                    $found = true;
                    $_SESSION["field"] = $field[1];
                    array_push($_SESSION["data"][$_SESSION["field"]], 
                        array(
                            "i"=>false,
                            "a"=>false,
                            "9"=>false,
                            "0"=>false,
                            "2"=>false,
                            "5"=>false,
                            "d"=>false,
                            "c"=>false,
                            "b"=>false,
                            "z"=>false,
                            "x"=>false,
                            "w"=>false
                        )
                    );
                }
            }
            if(!$found){
                $_SESSION["field"] = false;
            }
        break;
        case "subfield":
            if($_SESSION["field"]){
                if(!is_array($_SESSION["data"][$_SESSION["field"]][(count($_SESSION["data"][$_SESSION["field"]])-1)][$attribs["CODE"]])){
                    $_SESSION["data"][$_SESSION["field"]][(count($_SESSION["data"][$_SESSION["field"]])-1)][$attribs["CODE"]] = array();
                    $_SESSION["code"] = $attribs["CODE"];
                }
            }
        break;
    }

}

function endTag($parser, $name){
    global $fields, $link, $char, $stepsize;
    $_SESSION["infield"] = false;
    if(strtolower($name) == "record" && count($_SESSION["data"])>1){
        $data = $_SESSION["data"];

        if(count($data["url"])>=1){
            //ID aus URL extrahieren
            $url_e = explode("/", $data["url"][0]["a"][0]);
            $data["id"] = $url_e[(count($url_e)-1)];

            if(!dbExists("dnb_persons", $data["id"])){
                /*
                    1 > No Dates
                    2 > Birth or Dead only
                    3 > Birth and Dead
                    4 > Location Times
                */

                $valid = 1;

                $match_s = $match_e = array();

                if(count($data["lebenszeit"])>=1 && count($data["lebenszeit"][0]["a"])>=1){
                    $se = explode("-", $data["lebenszeit"][0]["a"][0]);
                    preg_match("/(\\d{4})/ui", $se[0], $match_s);
                    if(count($se)>1){
                        preg_match("/(\\d{4})/ui", $se[1], $match_e);
                    }else{
                        $match_e = array();
                    }
                }

                if(count($match_s)>=1){ $start = (int)$match_s[0]; }else{ $start = 0; }
                if(count($match_e)>=1){ $end = (int)$match_e[0]; }else{ $end = 0; }

                if($start>0 && $end>0){
                    $valid = 3;
                }else if($start>0 || $end>0){
                    $valid = 2;
                }else{
                    $valid = 1;
                }

                $sql = 'INSERT INTO `dnb_persons` (`id`, `name`, `life_start`, `life_end`, `valid`)VALUES("'.$data["id"].'", "'.str_replace('"', '', $data["name"][0]["a"][0]).'", '.$start.', '.$end.', '.$valid.')';
                $result = query_mysql($sql, $link);
                $_SESSION["count"]++;

                $jobs = array();

                foreach($data["beruf"] as $beruf) {
                    if(strpos($beruf["0"][0], ")")){
                        $bid_e = explode(")", $beruf["0"][0]);
                        $bid = $bid_e[1];
                    }else{
                        $bid = $beruf["0"][0];
                    }
                    if(strlen($bid)>1){
                        if(!dbExists("dnb_job", $bid)){
                            $sql = 'INSERT INTO `dnb_job` (`id`, `name`)VALUES("'.$bid.'", "'.$beruf["a"][0].'")';
                            $result = query_mysql($sql, $link);
                        }
                        $sql = 'INSERT INTO `dnb_person_job` (`person_id`, `job_id`)VALUES("'.$data["id"].'", "'.$bid.'")';
                        array_push($jobs, $bid);
                        $result = query_mysql($sql, $link);
                    }
                }

                if(count($jobs)>=1){
                    $sql = "UPDATE dnb_persons SET job_ids = '".json_encode($jobs)."' WHERE id = '".$data["id"]."'";
                    $result = query_mysql($sql, $link);
                }

                foreach($data["wirkungsorte"] as $ort) {
                    $oid_e = explode(")", $ort["0"][0]);
                    if(count($oid_e)>1){
                        $oid = $oid_e[1];
                    }else{
                        $oid = $oid_e[0];
                    }
                    if(strlen($oid)>1){
                        if(!dbExists("dnb_places", $oid)){
                            $sql = 'INSERT INTO `dnb_places` (`id`, `name`)VALUES("'.$oid.'", "'.$ort["a"][0].'")';
                            $result = query_mysql($sql, $link);
                        }

                        if($ort["i"][0]=="Sterbeort"){
                            $o_start = $o_end = $end;
                        }else if($ort["i"][0]=="Geburtsort"){
                            $o_start = $o_end = $start;
                        }else{

                            if((count($ort["9"])>1) && (substr($ort["9"][1], 0, 2) == "Z:")){
                                $o_time = explode("-", substr($ort["9"][1],2));
                                if(count($o_time)>1){
                                    preg_match("/(\\d{4})/ui", $o_time[0], $match_s);
                                    preg_match("/(\\d{4})/ui", $o_time[1], $match_e);
                                    $o_start = (int)$match_s[0];
                                    $o_end = (int)$match_e[0];
                                }else{
                                    preg_match("/(\\d{4})/ui", $se[0], $match_s);
                                    $o_end = $o_start = (int)$match_s[0];
                                }
                            }else{
                                $o_start = $start;
                                $o_end = $end;
                            }

                        }

                        $sql = 'INSERT INTO `dnb_person_place` (`person_id`, `place_id`, `start`, `end`)VALUES("'.$data["id"].'", "'.$oid.'", '.$o_start.', '.$o_end.')';
                        $result = query_mysql($sql, $link);
                    }
                }
            }else{
                
            }
            $newPosition = $char - $stepsize;
            $sql = 'UPDATE `dnb_parser` SET `character` = '.$newPosition.' WHERE `id` = 1';
            $result = query_mysql($sql, $link);
        }
    }
}

//The position where the parser stopped working is stored in the database for the next run
$currentPosition = 0;
$sql = 'SELECT `character` FROM `dnb_parser` WHERE `id` = 1';
$result = query_mysql($sql, $link);
if ($result) {
    while ($row = mysql_fetch_array($result)) {
        $currentPosition = $row[0];
    }
}
mysql_free_result($result);

$newPosition = $char = $currentPosition;

$stepsize = 4096;

//We cannot parse the whole 10GBs at once
//We are using the stream context functionallity
//To aquire one a substring of the file
function largeSeek($currentPosition){
    global $stepsize;
    $start=floatval($currentPosition);
    $len=floatval($stepsize);
    $opts = array(
        'http'=>array(
            'method'=>'GET',
            'header'=>array(
                "Content-Type: text/xml; charset=utf-8",
                "Range: bytes=$start-".($start+$len-1)
            )
        )
    );
    $context = stream_context_create($opts);
    $result = file_get_contents($xmlurl."/Tpgesamt1402gndmrc.xml", false, $context);
    return $result;
}

$init = false;

//As we are selecting a subset of the whole file
//We will receive a corrupted xml
//Therefore we are deleting everying before the 
//first record tag and adding and xml header before that
while (($char-$currentPosition)<1000000) {
    $data = largeSeek($char);
    if($currentPosition>0 && !$init){
        if(strpos($data, "<record")){
            //Add xml head
            $data = '<?xml version="1.0" encoding="UTF-8"?><collection xmlns="http://www.loc.gov/MARC21/slim">'.substr($data, strpos($data, "<record"));
            $init = true;
            xml_parse($parser, $data);
        }
    }else{
        xml_parse($parser, $data);
    }
    $char+=$stepsize;
}

//Let's output some performance info for the cron-job output
$d = new DateTime();
echo $d->format('Y-m-d H:i:s').' char_pos: '.($char - $stepsize)." (".$_SESSION["count"].")"."\n";

?>