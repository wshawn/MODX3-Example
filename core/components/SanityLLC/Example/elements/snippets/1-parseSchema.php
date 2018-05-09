<?php
/**
 * Created by PhpStorm.
 * User: W. Shawn Wilkerson
 * Date: 5/9/2018
 * Time: 9:44
 */
namespace SanityLLC\Example;

require_once MODX_CORE_PATH . 'components/SanityLLC/Example/M3.php';

$M = new M3($modx);
if (is_object($M) && $M instanceof M3) {
    $M->createPackageSchema();
} else {
    echo 'no';
}