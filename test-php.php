<?php
header('Content-Type: text/plain');
echo 'PHP works! Date: ' . date('Y-m-d H:i:s');
echo "\nPHP version: " . phpversion();
echo "\nABSPATH: " . (defined('ABSPATH') ? ABSPATH : 'not defined');
