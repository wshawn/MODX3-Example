<?php
/**
 * This file is part of MODX3 Example and is used to manipulate database data.
 *
 * Copyright (c)2017 Sanity, LLC. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SanityLLC\Example\Test;

use xPDO\xPDO;
use xPDO\Om\xPDOObject;

/* Force timezone to UTC */
date_default_timezone_set('UTC');

/**
 * Class Log
 *
 * This file handles all of the logging functions for the project.
 *
 * @property string $class Name of the Class performing action
 * @property string $action Name of the function performing action
 * @property boolean $status Whether or not the action was successful.
 * @property string $comment Message from action.
 * @property integer $userId Primary Key of modUser performing action.
 * @property integer $externalId Primary Key of item action was performed on.
 * @property string $timestamp The UTC timestamp of when the action was performed.
 * @property string $ipaddress IP Address of user performing action.
 *
 * @package SanityLLC\SexiPhd\Research
 */
class Log extends \xPDO\Om\xPDOSimpleObject
{
    /** @var array $backtrace A placeholder for the xPDO::getBacktrace */
    public $backtrace = array();

    /* Import Trait */
    use Utils;

    /**
     * Override the parent save() function.
     * @param boolean|integer $cacheFlag Indicates if the saved object(s) should be cached and optionally, by specifying
     * an integer value, for how many seconds before expiring.
     *
     * @return boolean Returns true on success, false on failure.
     * @uses xPDOObject::save();
     * @return bool|void
     */
    public function save($cacheFlag = null)
    {
        $this->setBacktrace();
        $this->set('class', $this->backtrace[3]['class']);
        $this->set('action', $this->backtrace[3]['function']);
        $this->set('ipaddress', $this->encodeIp($this->getClientIpAddress())); // Found in Utils
        $this->set('timestamp', time());
        return parent::save($cacheFlag);
    }

    /**
     * Sets $backtrace.
     *
     * @uses xpdo::getDebugBacktrace();
     */
    private function setBacktrace()
    {
        $this->backtrace = $this->xpdo->getDebugBacktrace();
    }
}
