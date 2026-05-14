<?php
$host = 'ebioservernew.esslsecurity.com';
$port = 99;
$timeout = 10;

$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);

if ($fp) {
    echo "SUCCESS: Connection to $host on port $port is OPEN.";
    fclose($fp);
} else {
    echo "ERROR: Connection failed. Reason: $errstr ($errno). <br>";
    echo "This confirms your hosting provider is blocking Port 99.";
}
