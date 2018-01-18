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
}
