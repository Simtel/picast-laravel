<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';
$t = [
    ['22', '02'],
    ['09', '14'],
    ['18', '22'],
    ['10', '19'],
    ['09', '18'],
    ['14', '18'],
    ['14', '22'],
    ['02', '07'],
];

usort($t, static function ($a, $b) {
    if ($a[1] == $b[1]) {
        return $a[0] < $b[0] ? -1 : 1;
    }
    return $a[1] < $b[1] ? -1 : 1;
});

foreach ($t as $v) {
    dump($v[0].'-'.$v[1]);
}
