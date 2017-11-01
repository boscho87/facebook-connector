<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * Connect a Website to an Facbook Page
 *
 * @link      https://www.itscoding.ch
 * @copyright Copyright (c) 2017 Simon Müller itsCoding
 */

namespace itscoding\facebookconnector\services;

use Facebook\Facebook;
use Facebook\FacebookRequest;
use Facebook\GraphNodes\GraphEdge;
use itscoding\facebookconnector\FacebookConnector;

use craft\base\Component;

/**
 * EventFetcher Service
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class EntryFetcher extends Component
{

    /**
     * @var Facebook
     */
    private $fb;

    public function getEntry()
    {
        session_start();
        $entries = $this->loadAllEntries();
    }

    /**
     * get all pages (with the next link)
     * @return array
     */
    public function loadAllEntries()
    {
        $token = FacebookConnector::$plugin->tokenLoader->loadValidToken();
        $this->fb = FacebookConnector::$plugin->tokenLoader->getFacebookInstance();
        $nextLink = null;
        $entries = [];
        do {
            if ($nextLink) {
                $nextLink = str_replace($this->getApiUrl(), '', $nextLink);
                $response = $this->fb->get($nextLink, $token);
            } else {
                $response = $this->fb->get(FacebookConnector::getInstance()->getSettings()->pageId . '/posts', $token);
            }
            $nextLink = json_decode($response->getBody())->paging->next ?? null;
            $entries[] = json_decode($response->getBody())->data ?? [];
        } while (isset($nextLink));
        return array_merge(...$entries);
    }


    /**
     * @param int $timestamp
     */
    public function loadEntriesUntilDate(int $timestamp)
    {
        $date = strtotime('Y-m-d H:i:s', $timestamp);
        //Todo implement method

    }

    /**
     * @return string
     */
    private function getApiUrl()
    {
        return $this->fb->getClient()->getBaseGraphUrl() . '/' . $this->fb->getDefaultGraphVersion();
    }

}
