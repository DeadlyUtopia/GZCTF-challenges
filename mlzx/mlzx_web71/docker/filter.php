<?php

$c = $_POST['c'] ?? '';
/*
if (stripos($c, 'include') !== false && stripos($c, '/') !== false && stripos($c, '.txt') !== false) {
    die();
}
*/
if (stripos($c, 'GZCTF_FLAG') !== false) {
    die();
}
/*
ob_start();

register_shutdown_function(function() {
    $output = ob_get_contents();
    ob_end_clean();
    
    if (stripos($output, 'flag') !== false) {
        die();
    }
    
    echo $output;
});
*/
?>