<?php
/**
 * This file is part of Example and is used to demonstrate the use of namespaces in MODX3 without using deprecated
 * MODx functions. As MODX3 is developed this example may become obsolete.
 *
 * Copyright (c)2018 Sanity, LLC. Use at your own risk.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SanityLLC\Example;

use SanityLLC\Example\Test\Log;
use xPDO\Om\xPDOObject;
use xPDO\xPDO;
use \Exception;
use xPDO\xPDOException;


define('MIN_PHP_VERSION', '7.0.0');
define('MIN_MODX_VERSION', '3.0.0');

/**
 * Modx3Example xPDO Version
 *
 * This is the main file to include in your scripts to use SexiPhd xPDO Version.
 *
 * @package Example
 * User: W. Shawn Wilkerson *
 * Date: 3/17/2018
 * Time: 9:40
 * @version 1.0.0-alpha
 * @author W. Shawn Wilkerson <shawn@sanityllc.com>
 * @copyright Copyright 2018 by Sanity LLC
 * @link http://www.sanityllc.com
 * @license Use at your own risk.
 */
class M3
{
    /** @var bool DEBUG Whether or not the class is in debug mode. */
    const DEBUG = false;
    /** @var array $_config The runtime configuration. */
    public $config = array();
    /** @var \modX $modx A reference to the modX object. */
    public $modx = null;
    /** @var string $package The name of the package by (also of the directory the schema will be parsed into). */
    private $package = 'Test';
    /** @var string The namespace to prefix the schema objects. */
    private $packageNamespace = 'SanityLLC\\Example\\Test\\';
    /** @var string #var The actual filename of the schema. */
    private $schemaFileName = 'SanityLLC.Example';
    /** @var string $prefix The database table prefix for the package. */
    private $prefix = 'xx_';
    /** @var \modUser $user A reference to the current user object. */
    public $user = null;

    /**
     * Main constructor for the Class
     * @throws \Exception If MODX is not installed.
     * @param \modX $modx A reference to the current modx Object.
     */
    public function __construct(\modX &$modx)
    {
        try {
            /* Check PHP version - must be at least version 7.0.0*/
            if (version_compare(MIN_PHP_VERSION, phpversion(), '>')) {
                throw new Exception('PHP version ' . MIN_PHP_VERSION . " or higher required. System running:" . phpversion());
            }
            /* Validate MODX */
            if (!is_object($modx) || (!$modx instanceof \modX)) {
                throw new Exception("MODX CMS required and was not found. Please install version 3 or higher.");
            } else {
                /* Check MODX version */
                $modx->getVersionData();
                if (version_compare(MIN_MODX_VERSION, $modx->version['full_version'], '>')) {
                    $modx->sendError('unavailable',
                        array('error_message' => 'MODX version ' . MIN_MODX_VERSION . ' or higher required. Version: ' . $modx->version['full_version']));
                } else {
                    /** The MODX object. */
                    $this->modx = &$modx;

                    /** The current MODX Revolution User */
                    $this->user = $this->modx->user;

                    /* Initialize the configuration */
                    $this->setConfig();

                    /* Set the package */
                    if (!$this->modx->setPackage($this->package, $this->config['modelPath'], $this->prefix)) {
                        $this->modx->sendError('unavailable',
                            ['error_message' => 'Package ' . $this->package . ' not found at:' . $this->config['modelPath']]);
                    }
                }
            }
        } catch (\Exception $e) {
            /* Handle exceptions when MODX is not installed or available. */
            echo 'Message: ' . $e->getMessage();
        }
    }

    /**
     * @param array $config An array of runtime settings with cleaned up windows paths.
     */
    public function setConfig(array $config = array())
    {
        $assetsPath = $this->modx->getOption('assets_url') . 'components' . DIRECTORY_SEPARATOR . __CLASS__ . DIRECTORY_SEPARATOR;
        $assetsUrl = str_replace('\\', '/', $assetsPath);
        $herePath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $corePath = str_replace('\\', '/', $herePath);

        $this->config = array_merge($config, array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector/',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'snippetsPath' => $corePath . 'elements/snippets',
        ));

        unset ($assetsPath, $assetsUrl, $herePath, $corePath);
    }

    /**
     * Final destructor.
     */
    function __destruct()
    {
    }

    function __toString()
    {
        return __CLASS__;
    }

    /**
     * Creates a new schema object and saves it to the database table.
     * @param string $name The name of the object to create, which must be predefined.
     * @param array $parameters The parameters sent to xPDOObject::FromArray
     * @uses getSchemaObjectNames()
     * @see  xPDOObject::fromArray()
     * @return null|\xPDOObject
     */
    private function createNewSchemaObject(string $name = '', array $parameters = array()): ?xPDOObject
    {
        $obj = null;

        try {
            if (!in_array($name, $this->getSchemaObjectNames())) {
                throw new xPDOException($name . ' not permitted');
            } else {
                $obj = $this->modx->newObject($this->packageNamespace . $name, $parameters);
                if (!is_object($obj) || !$obj instanceof xPDOObject) {
                    throw new xPDOException('Object ' . $name . ' not created.');
                }
            }
        } catch (xPDOException $xe) {
            $this->modx->sendError('unavailable', array('error_message' => $xe->getMessage()));
        }

        return $obj;
    }

    /**
     * Shortcut version to create the platform class files that keeps files from being overwritten and destroyed.
     *
     * @uses parsePackageSchema()
     * @return bool Whether or not the schema was refreshed.
     */
    public function createPackageSchema(): bool
    {
        $schemaObjects = $this->getSchemaObjectNames();
        try {
            if (!count($schemaObjects) > 0) {
                throw new xPDOException('Schema Objects have not been defined.');
            } else {
                $testObjName = array_pop($schemaObjects);
                $testObj = $this->modx->getObject($this->packageNamespace . $testObjName);
                if (is_object($testObj) && $testObj instanceof \xPDOObject) {
                    throw new xPDOException('Schema has already been parsed.');
                } else {
                    $success = $this->parsePackageSchema(false, 0, 0);
                }
            }
        } catch (xPDOException $xe) {
            $this->modx->sendError('unavailable', array('error_message' => $xe->getMessage()));
        }
        return isset($success) ?: false;
    }

    /**
     * Creates the database tables associated with the package.
     *
     * @return bool
     */
    public function createPackageTables(): bool
    {
        $out = false;
        $this->modx->setLogTarget(php_sapi_name() === 'cli' ? 'ECHO' : 'HTML');
        $this->modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
        $schemaObjects = $this->getSchemaObjectNames();
        if (count($schemaObjects) > 0) {
            $manager = $this->modx->getManager();
            $i = 0;
            foreach ($schemaObjects as $className) {
                $i += ($manager->createObjectContainer($this->packageNamespace . $className) ? 1 : 0);
            }
            $out = (count($schemaObjects) == $i) ?: false;
        }
        return $out;
    }

    /**
     * Retrieves the current user's IP Address in IPv4 or IPv6
     *
     * @return string An IP Address or an empty string
     */
    public function getClientIpAddress(): string
    {
        $serverVariables = array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_X_COMING_FROM',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_COMING_FROM',
            'HTTP_CLIENT_IP',
            'HTTP_FROM',
            'HTTP_VIA',
            'REMOTE_ADDR'
        );
        $out = '';
        foreach ($serverVariables as $serverVariable) {
            $value = '';
            if (isset($_SERVER[$serverVariable])) {
                $value = $_SERVER[$serverVariable];
            } elseif (getenv($serverVariable)) {
                $value = getenv($serverVariable);
            }

            if (!empty($value)) {
                if (filter_var(trim($value), FILTER_VALIDATE_IP,
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $out = $value;
                    break;
                }
            }
        }
        return $out;
    }

    /**
     * Retrieves an array of the object names defined in the schema, without namespace..
     * Note: this list must be manually updated and match schema definitions for
     * all subsequent usage to function properly.
     * @return array An Array of object names established in the schema file.
     */
    private function getSchemaObjectNames(): array
    {
        return array(
            'Log',
            'Organization',
            'OrganizationExec',
            'OrganizationExecPosition',
        );
    }

    /**
     * Creates an Event log.
     *
     * @uses \SanityLLC\SexiPhd\Research\Log
     * @param bool $status
     *            true on success || false on failure
     * @param string $comment
     *            Text related to the action
     *
     * @return boolean true on success || false on failure
     */
    public function logEvent(bool $status = false, string $comment = ''): bool
    {
        $parameters = array(
            'status' => (bool)$status,
            'comment' => (string)$comment,
            'userId' => (int)$this->user->getPrimaryKey(),
        );
        $obj = new \SanityLLC\SexiPhd\Research\Log($this->modx);
        $obj->fromArray($parameters);
        return $obj->save();
    }

    /**
     * Parses the schema and generates ORM files and the database structure, but will not overwrite preexisting files such as IonCube Encoded Versions or those previously generated.
     *
     * Schema file must be stored at /core/components/[package_name]/model/schema/[package_name].mysql.schema.xml.
     * Schema file must be named [package_name].mysql.schema.xml.
     *
     * @param bool $compile Compile multiple packages into a single file for quicker loading. Defaults to false as we only have a single package.
     * @param int $regenerate Indicates if existing class files should be regenerated; 0=no [default], 1=regenerate platform classes, 2=regenerate all classes.
     * @param int $update Indicates if existing class files should be updated; 0=no, 1=update platform classes, 2=update all classes [default].
     *
     * @see \xPDO\Om\xPDOGenerator::parseSchema()
     * @see \xPDO\Om\xPDOGenerator::outputClasses()
     * @throws \xPDO\xPDOException
     * @return bool Whether or not the schema was parsed.
     */
    private function parsePackageSchema($compile = false, $regenerate = 0, $update = 2): bool
    {
        $success = false;
        $this->modx->setLogTarget(php_sapi_name() === 'cli' ? 'ECHO' : 'HTML');
        $this->modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
        $manager = $this->modx->getManager();
        $manager->getGenerator();
        $generator = $manager->generator;

        try {
            $schemaFile = $this->config['modelPath'] . 'schema' . DIRECTORY_SEPARATOR . $this->schemaFileName . '.' . $this->modx->config['dbtype'] . '.schema.xml';

            /* Make sure the file exists and is readable */
            if (!is_readable($schemaFile)) {
                throw new xPDOException('File ' . $schemaFile . ' not found.');
            }
            /* Make sure we are actually reading an xml file */
            if (!mime_content_type($schemaFile) === 'application/xml') {
                throw new xPDOException($schemaFile . ' does not appear to be xml.');
            }

            $options = array(
                'compile' => $compile,
                'namespacePrefix' => __NAMESPACE__,
                'outputDir' => $this->config['modelPath'] . $this->package,
                'regenerate' => $regenerate,
                'update' => $update,
                'withNamespace' => 1
            );

            $success = !$generator->parseSchema($schemaFile, $this->config['modelPath'], $options);

        } catch (xPDOException $xe) {
            $this->modx->sendError('unavailable', array('error_message' => $xe->getMessage()));
        }

        /* Parse Schema */
        return $success;
    }

    /**
     * Shortcut version to refresh the platform class files.
     *
     * @uses parsePackageSchema()
     * @throws xPDOException
     * @return bool Whether or not the schema was refreshed.
     */
    public function refreshPackageSchema(): bool
    {
        return $this->parsePackageSchema(false, 1, 1);
    }

    /**
     * Removed the database tables associated with the package.
     *
     * @return bool Whether or not database tables were created for all the schema objects.
     */
    public function removePackageTables(): bool
    {
        $out = false;
        try {
            $this->modx->setLogTarget(php_sapi_name() === 'cli' ? 'ECHO' : 'HTML');
            $this->modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
            $schemaObjects = $this->getSchemaObjectNames();
            if (!count($schemaObjects) > 0) {
                throw new xPDOException('Schema Objects have not been defined.');
            } else {
                $manager = $this->modx->getManager();
                $i = 0;
                foreach ($schemaObjects as $className) {
                    $i += ($manager->removeObjectContainer($this->packageNamespace . $className) ? 1 : 0);
                }
                $out = (count($schemaObjects) == $i) ?: false;
            }
        } catch (xPDOException $xe) {
            $this->modx->sendError('unavailable', array('error_message' => $xe->getMessage()));
        }
        return $out;
    }
}
