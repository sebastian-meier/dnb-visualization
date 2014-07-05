<?php

//This feature is really important
//If the data later on added to your database
//seams to be not precise enough, it might be
//due to the lag of this feature
ini_set("precision", 20);

//Database Connection
$db_server = "SERVER";
$db_user = "USERNAME";
$db_pass = "PASSWORD";
$db_database = "DATABASE";

//If you want to parse the Database yourself
//You need to download the Tpgesamt1402gndmrc.xml
//And store it somewhere for retrieval
$xmlurl = "URL TO XML";

/*------------------- MYSQL -------------------*/

if(!$link = mysql_connect($db_server, $db_user, $db_pass)){
    echo 'Problem connecting to the MySql Server';
    exit;
}

if(!mysql_select_db($db_database, $link)){
    echo 'Unable to connect to database-table ';
    exit;
}

function query_mysql($sql, $link){
    $result = mysql_query($sql, $link);
    if (!$result) {
        echo "DB Error, could not execute request\n";
        echo 'MySQL Error: ' . mysql_error();
        exit;
    }else{
        return $result;
    }
}

ini_set('default_socket_timeout', 900);
mysql_query("SET NAMES 'utf8'");

?>