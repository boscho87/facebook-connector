<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 05.11.2017
 * Time: 10:39
 */

namespace itscoding\facebookconnector\console\controllers;

use itscoding\facebookconnector\FacebookConnector;
use yii\console\Controller;

class EntryFetchController extends Controller
{

    /**
     * @return string
     * php craft facebook-connector/entry-fetch/list
     */
    public function actionFetchList(string $date = null)
    {
        $count = 0;
        $loadUntil = $date ?: time();
        $entries = FacebookConnector::$plugin->entryFetcher->fetchAll($loadUntil);
        foreach ($entries as $entry) {
            $count++;
            FacebookConnector::$plugin->entryPersist->persist($entry);
        }
        echo 'Added ' . $count . ' new entries ' . PHP_EOL;
        return 0;
    }

    public function actionProvideAttachments()
    {
        $count = FacebookConnector::$plugin->entryPersist->provideAttachments();
        echo 'get attachments for ' . $count . ' entries' . PHP_EOL;
    }

}
