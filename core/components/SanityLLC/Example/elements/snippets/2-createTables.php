<?php
/**
 * Created by PhpStorm.
 * User: W. Shawn Wilkerson
 * Date: 5/9/2018
 * Time: 9:45
 */
namespace SanityLLC\Example;

require_once MODX_CORE_PATH . 'components/SanityLLC/Example/M3.php';

$MT = new M3($modx);
if (is_object($MT) && $MT instanceof M3) {
    $MT->createPackageTables();
} else {
    echo 'no';
}