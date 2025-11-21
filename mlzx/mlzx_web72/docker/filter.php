<?php
$c = $_POST['c'] ?? '';

if (preg_match('/\bhighlight_file\b/i', $c)) {
    echo "Prohibited 01HF";
    die();
}

if (preg_match('/\bshow_source\b/i', $c)) {
    echo "Prohibited 02SS";
    die();
}

$env_ban = [
    'getenv', '$_ENV', '$_SERVER', 'putenv', 'apache_getenv', 'get_cfg_var'
];
foreach ($env_ban as $func) {
    if (stripos($c, $func) !== false) {
        echo "Warning 01EN";
        die();
    }
}

if (stripos($c, 'GZCTF_FLAG') !== false) {
    echo "Warning 02GF";
    die();
}

?>