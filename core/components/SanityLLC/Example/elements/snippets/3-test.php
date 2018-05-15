<?php
/**
 * Created by PhpStorm.
 * User: W. Shawn Wilkerson
 * Date: 5/9/2018
 * Time: 9:46
 */

namespace SanityLLC\Example;

use SanityLLC\SexiPhd\Phd;

require_once MODX_CORE_PATH . 'components/SanityLLC/Example/M3.php';

$Test = new M3($modx);
$modx = $Test->modx;
$modx->setLogTarget(php_sapi_name() === 'cli' ? 'ECHO' : 'HTML');
$modx->setLogLevel($modx::LOG_LEVEL_INFO);
if (is_object($Test) && $Test instanceof M3) {
//   $Test->createPackageSchema();
//   $Test->createPackageTables();
    //    $Test->refreshPackageSchemaPlatformFiles();
    /*
        //$orgObj = $Test->getSchemaObject('Organization', array(['id'=>1]));
        $orgObj = $Test->createNewSchemaObject('Organization');
        if ($orgObj) {
            print_r($orgObj->toArray());
            echo '<br><br>';
            var_dump($orgObj->getFKClass('id'));
            echo '<br><br>';
            var_dump($orgObj->getFKDefinition('Execs'));
            echo '<br><br>';
            var_dump($orgObj->_aggregates);
            echo '<br><br>';
            var_dump($orgObj->_composites);

} else {
    echo 'no org';
}
    */
$orgExecPositionArray = array('CEO', 'CIO', 'COO', 'CFO', 'CIO');

foreach ($orgExecPositionArray as $position) {
    $positionObj = $Test->createNewSchemaObject('OrganizationExecPosition', array('name' => $position));
    $positionObj->save();
}
$orgArray = array(
    array(
        'name' => 'Org1',
        'rank' => 9999,
        'Execs' => array(
            array('name' => 'John Doe', 'positionId' => 1),
            array('name' => 'Johnny Doe', 'positionId' => 3),
            array('name' => 'Jon Doe', 'positionId' => 4)
        )
    )
);

foreach ($orgArray as $org) {
    if (!empty($org)) {
        $saveObj = $Test->createNewSchemaObject('Organization', $org);
        $newExecs = array();
        foreach ($org['Execs'] as $exec) {
            $newExecs[] = $Test->createNewSchemaObject('OrganizationExec', $exec);
        }
        $saveObj->addMany($newExecs);
        $saveObj->save();
    }
}

} else {
    echo 'no';
}
