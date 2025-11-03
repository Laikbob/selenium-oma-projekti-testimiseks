<?php
$kasutaja = "d133850_andreileb";
$parool = "musthaus862686";
$andmebaas = "d133850_phpbaas";
$servername = "d133850.mysql.zonevs.eu";


$yhendus = new mysqli($servername, $kasutaja, $parool, $andmebaas);
$yhendus->set_charset("utf8");

