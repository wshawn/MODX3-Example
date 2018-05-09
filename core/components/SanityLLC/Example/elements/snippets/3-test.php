<?php
/**
 * Created by PhpStorm.
 * User: W. Shawn Wilkerson
 * Date: 5/9/2018
 * Time: 9:46
 */
namespace SanityLLC\Example;

require_once MODX_CORE_PATH . 'components/SanityLLC/Example/M3.php';

$Test = new M3($modx);

if (is_object($Test) && $Test instanceof M3) {
    $Test->logEventNoModx(false, 'Without using modx::newObject and with namespace');
    $Test->logEvent(true, 'MODx3 did this.');
} else {
    echo 'no';
}
