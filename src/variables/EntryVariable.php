<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 05.11.2017
 * Time: 12:13
 */

namespace itscoding\facebookconnector\variables;

use itscoding\facebookconnector\FacebookConnector;

/**
 * Class EntryVariable
 * @package itscoding\facebookconnector\variables
 */
class EntryVariable
{

    /**
     * @return mixed
     * @codeCoverageIgnore
     */
    public function getEntries()
    {
        return FacebookConnector::$plugin->entryPersist->getEntries();
    }

    /**
     * @param $data
     * @return mixed
     * @codeCoverageIgnore
     */
    public function decode($data)
    {
        return json_decode($data);
    }

    /**
     * @param $data
     * @param int $end
     * @param int $start
     * @return bool|string
     */
    public function subDecode($data, $end = 100, $start = 0): string
    {
        $data = json_decode($data);
        return $this->sub($data, $end, $start);
    }

    /**
     * @param string $string
     * @param int $end
     * @param int $start
     * @return string
     */
    public function sub(string $string, int $end = 100, int $start = 0): string
    {
        $string = strrev(substr($string, $start, $end));
        $lastSpace = strpos($string, ' ');
        return strrev(substr($string, $lastSpace));
    }
}
