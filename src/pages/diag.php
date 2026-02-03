<?php
header('Content-Type: text/plain; charset=utf-8');
echo "PHP: " . PHP_VERSION . PHP_EOL;
echo "mysqli loaded: " . (extension_loaded('mysqli') ? 'YES' : 'NO') . PHP_EOL;
echo "mysqli_report exists: " . (function_exists('mysqli_report') ? 'YES' : 'NO') . PHP_EOL;
