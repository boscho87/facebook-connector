<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 05.11.2017
 * Time: 10:39
 */

namespace itscoding\facebookconnector\console\controllers;

use itscoding\facebookconnector\FacebookConnector;
use itscoding\facebookconnector\migrations\Install;
use yii\console\Controller;

/**
 * Class EntryFetchController
 * @package itscoding\facebookconnector\console\controllers
 */
class EntryFetchController extends Controller
{

    /**
     * @return string
     * Handle facebook-connector/fetch-list console command
     */
    public function actionFetchList(string $date = null)
    {
        $count = 0;
        $loadUntil = $date ?: time();
        echo 'Loading data from facebook' . PHP_EOL;
        $entries = FacebookConnector::$plugin->entryFetcher->fetchAll($loadUntil);
        foreach ($entries as $entry) {
            $saved = FacebookConnector::$plugin->entryPersist->persistEntry($entry);
            if ($saved) {
                $count++;
            }
        }
        echo 'added ' . $count . ' new entries ' . PHP_EOL;
        return 0;
    }

    /**
     * Handle facebook-connector/fetch-detail console commands.
     */
    public function actionFetchDetail()
    {
        echo 'Loading data from facebook' . PHP_EOL;
        $count = FacebookConnector::$plugin->entryPersist->persistEntryDetails();
        echo 'loaded details for ' . $count . ' entries' . PHP_EOL;
    }


    /**
     * Handle facebook-connector/up console command
     */
    public function actionUp()
    {
        $migration = new Install();
        $migration->safeUp();
    }

    /**
     * Handle facebook-connector/down console command
     */
    public function actionDown()
    {
        $migration = new Install();
        $migration->safeDown();
    }
}
