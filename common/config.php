<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$dbfilename = "iplog.db";
$geoloc     = false;
$threshold  = 3;
if(file_exists($file = "$path/common/whitelist.ini"))
{
    $wl_ini = parse_ini_file($file);
    $whitelist = explode(",", trim($wl_ini['whitelist']));
}