<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../src/mysqli.php';


$DBServer = 'localhost'; // e.g 'localhost' or '192.168.1.100'
$DBUser   = 'root';
$DBPass   = 'admin';
$DBName   = 'mysqli_ex';

$conn = new mysqli_db($DBServer, $DBUser, $DBPass, $DBName);


$arrParams = array("i",1);
$obj = $conn->query("SELECT * FROM sample WHERE type=?", $arrParams);

echo ("<br /><h1> Rows Count </h1> <br />");
echo $obj->num_rows;

echo ("<h1> Rows  </h1> <br />");
print_r($obj->rows->data);

echo ("<h1> Rows With no Field Keys </h1> <br />");
print_r($obj->rows->ndata);


echo ("<br /><h1> fields </h1> <br />");

print_r($obj->fields->all);


echo ("<br /><h1> fields Names Array </h1> <br />");

print_r($obj->fields->names);



echo ("<br /><h1> fields Original Names Array </h1> <br />");

print_r($obj->fields->orgnames);


echo ("<br /><h1> Table Primary Keys fields Names Array </h1> <br />");

print_r($obj->fields->primary);





$obj = $conn->query("INSERT INTO sample2 (number) VALUES (".rand(0, 100000).")");
echo ("<br /><h1> ID </h1> <br />");
echo $obj->insert_id;



$obj = $conn->query("UPDATE sample2 SET number=".rand(0, 100000)." WHERE ID < 10");
echo ("<br /><h1> Affected </h1> <br />");
echo $obj->affected_rows;



$data = Array (  "name" => "soso", "age" => 40, "country" => "egypt", "type" => 1 ) ;
echo ("<br /><h1> Test Insert Function </h1> <br />");
$obj = $conn->insert("sample", $data);
echo $obj->insert_id;
