<?php
define('APP_TIME', microtime(true));
register_shutdown_function(function () {
    echo '<div style="position:fixed; padding:1rem; font-family:arial; background:black; color:white; bottom:0; right:0;">Time: ' . number_format((microtime(true) - APP_TIME), 4) . ' Seconds</div>';
}, APP_TIME);

function pr($arr)
{
    echo '<pre>', print_r($arr, true), '</pre>';
}

require 'vendor/autoload.php';

$parser = new \Pheonix\Parser;

$template = <<<EOT
Hello, {{ name }}
I'm {{ age }} years old!
EOT;

// He is {{ student.name }} from {{ student.class }}

// {{ abc }}
//     Cout - {{ a }}
// {{ /abc }}

echo "OUTPUT: <br>" . $parser->parse($template, [
    'name' => 'Javed',
    'age' => '20',
    'student' => [
        'name' => 'Ali',
        'class' => 'BSCS-3B',
    ],
    'abc' => [
        ['a' => 1],
        ['a' => 2],
        ['a' => 3],
    ],
]);
