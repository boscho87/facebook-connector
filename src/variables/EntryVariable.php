<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 05.11.2017
 * Time: 12:13
 */

namespace itscoding\facebookconnector\variables;

use itscoding\facebookconnector\FacebookConnector;

class EntryVariable
{


    public function getEntries()
    {
        return FacebookConnector::$plugin->entryPersist->getEntries();
    }
}
