<?php
$path = dirname(__DIR__);
require_once "$path/common/common.php";

echo "Please run: iptables -N honeypot;iptables -I INPUT -j honeypot -p tcp -m multiport --dport 80; before running this or it will NOT work".PHP_EOL;
echo "Although it might not be secure, run this script as root in order to add the rules to your firewall, set it as a */1 cron for example".PHP_EOL;
$apply = false;
if (isset($argv[1]) && $argv[1] === "apply") {
    if (posix_getuid() !== 0) {
        echo "Sicne you are not running this as root, all execs will eventually fail.".PHP_EOL;
    }
    $apply = true;
}

$iptpath = trim(shell_exec("which iptables"));
/* get flagged addresses */
$query   = "select IPADDR from IPLOG where FLAGGED=1";
$ret     = $sqli->query($query);
$cmd     = "$iptpath -F honeypot".PHP_EOL;
if ($apply) {
    shell_exec($cmd);
}

while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
    $cmd = "$iptpath -A honeypot -s ".$row["IPADDR"]."/32 -j REJECT --reject-with icmp-port-unreachable".PHP_EOL;
    /* Debug */
    if ($row["IPADDR"] === "127.0.0.1") {
        echo "This command will never run as it will break local connections".PHP_EOL;
    } elseif ($apply) {
        shell_exec($cmd);
    }
    echo $cmd;
}
$sqli->close();
