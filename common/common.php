<?php
require_once("config.php");
require_once("bootstrap.php");

function getUserIP()
{
    $ipaddress = '';
    if (filter_has_var(INPUT_SERVER, 'HTTP_CLIENT_IP')) {
        $ipaddress = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
    } else if (filter_has_var(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR')) {
        $ipaddress = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
    } else if (filter_has_var(INPUT_SERVER, 'HTTP_X_FORWARDED')) {
        $ipaddress = filter_input_var(INPUT_SERVER, 'HTTP_X_FORWARDED');
    } else if (filter_has_var(INPUT_SERVER, 'HTTP_FORWARDED_FOR')) {
        $ipaddress = filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR');
    } else if (filter_has_var(INPUT_SERVER, 'HTTP_FORWARDED')) {
        $ipaddress = filter_input(INPUT_SERVER, 'HTTP_FORWARDED');
    } else if (filter_has_var(INPUT_SERVER, 'REMOTE_ADDR')) {
        $ipaddress = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
    } else {
        $ipaddress = 'UNKNOWN';
    }

    /* In case it's behind a proxy, get the first record from XFF */
    $realip = explode(",", $ipaddress);

    return $realip[0];
}
