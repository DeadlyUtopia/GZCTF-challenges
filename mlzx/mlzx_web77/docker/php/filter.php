<?php
// Return 404 if accessed directly (but not if auto_prepended)
if (basename($_SERVER['PHP_SELF']) === 'filter.php' && 
    strpos($_SERVER['REQUEST_URI'], 'filter.php') !== false) {
    http_response_code(404);
    die('404 Not Found');
}

// Get the code to be evaluated (only when processing POST requests)
$c = $_POST['c'] ?? '';

// Only apply filters if there's POST data (meaning it's not just a direct access)
if (!empty($c)) {
    // Filter checks before eval
    if (preg_match('/\bhighlight_file\b/i', $c)) {
        echo "Prohibited 01HF";
        die();
    }

    // Environment variable access restrictions
    $env_ban = [
        'getenv', '$_ENV', '$_SERVER', 'putenv', 'apache_getenv', 'get_cfg_var'
    ];
    foreach ($env_ban as $func) {
        if (stripos($c, $func) !== false) {
            echo "Warning 01EN";
            die();
        }
    }

    // Prevent accessing GZCTF_FLAG constant directly
    if (stripos($c, 'GZCTF_FLAG') !== false) {
        echo "Warning 02GF";
        die();
    }
}

?>
