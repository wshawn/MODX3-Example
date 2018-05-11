<?php
/**
 * Created by PhpStorm.
 * User: W. Shawn Wilkerson
 * Date: 5/10/2018
 * Time: 13:57
 */
namespace SanityLLC\Example\Test;

use xPDO\xPDO;

trait Utils
{
    /**
     * Converts a packed in_addr representation representation into a a human readable IP address.
     * @link http://php.net/manual/en/function.inet-pton.php
     *
     * @return string The in_addr representation of the given address.
     */
    public function decodeIp($ipaddress = '')
    {
        return inet_ntop($ipaddress);
    }

    /**
     * Converts a human readable IP address to its packed in_addr representation
     * @link http://php.net/manual/en/function.inet-pton.php
     *
     * @param string $ipaddress Human readable IP address.
     * @return string
     */
    public function encodeIp(string $ipaddress = '')
    {
        $this->getClientIpAddress();
        return inet_pton($ipaddress);
    }

    /**
     * Retrieves the current user's IP Address in IPv4 or IPv6
     * @todo return the filter to FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE when site is live.
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
                if (filter_var(trim($value), FILTER_VALIDATE_IP)) {
                    $out = $value;
                    break;
                }
            }
        }
        return $out;
    }
}
