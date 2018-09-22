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
     * @var int
     */
    private $pageLimit = 15;

    /**
     * @var string|null
     */
    private $nextLink;

    /**
     * @var  string
     */
    private $token;

    /**
     * @var Facebook
     */
    private $facebook;
    /**
     * @var array
     */
    private $eventFields = ['start_time', 'end_time', 'cover', 'interested_count'];


    public function __construct(
        TokenLoader $tokenLoader = null
    )
    {
        $this->facebookPageId = FacebookConnector::getInstance()->getSettings()->pageId;
        parent::__construct();
    }


    /**
     * @codeCoverageIgnore
     */
    public function init()
    {
        parent::init();
        $this->token = FacebookConnector::$plugin->tokenLoader->loadValidToken();
        $this->facebook = FacebookConnector::$plugin->tokenLoader->getFacebookInstance();
    }

    /**
     * get all pages (with the next link)
     * @param int|null $latestDate if this is set,
     * next page only loads when it has newer entries than this date
     * @return array
     * @codeCoverageIgnore
     *
     */
    public function fetchAll(int $latestDate = 0)
    {
        $limit = $this->pageLimit;
        do {
            $entries[] = $this->getPageEntries();
            $limit--;
            if (!$this->checkIfDateInRange($entries, $latestDate)) {
                break;
            }
        } while (isset($this->nextLink) && $limit > 0);
        return array_merge(...$entries);
    }

    /**
     * @param string $entryId
     * @return string
     * @codeCoverageIgnore
     */
    public function getEntryAttachments(string $entryId)
    {
        try {
            $response = $this->facebook->get($entryId . '/attachments', $this->token);
            return json_decode($response->getBody())->data[0];
        } catch (\Exception $exception) {
            return '';
        }
    }

    /**
     * @param string $eventId
     * @return mixed
     * @codeCoverageIgnore
     */
    public function getEventDetails(string $eventId): object
    {
        $response = $this->facebook->get(
            '/' . $eventId . '?fields=' . implode(',', $this->eventFields),
            $this->token
        );
        return json_decode($response->getBody());
    }

    /**
     * @param $entries
     * @param $limit
     * @return array
     * @codeCoverageIgnore
     */
    private function getPageEntries(): array
    {
        if ($this->nextLink) {
            $this->nextLink = str_replace($this->getApiUrl(), '', $this->nextLink);
            $response = $this->facebook->get($this->nextLink, $this->token);
        } else {
            $response = $this->facebook->get(FacebookConnector::getInstance()
                    ->getSettings()->pageId . '/feed', $this->token);
        }
        $this->nextLink = json_decode($response->getBody())->paging->next ?? null;
        return json_decode($response->getBody())->data ?? [];
    }

    /**
     * @param $entries
     * @param $latestDate
     * @return bool
     * @codeCoverageIgnore
     */
    private function checkIfDateInRange($entries, $latestDate): bool
    {
        $created = end($entries[count($entries) - 1])->created_time;
        return $latestDate < strtotime($created);
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    private function getApiUrl(): string
    {
        return $this->facebook->getClient()->getBaseGraphUrl() . '/' . $this->facebook->getDefaultGraphVersion();
    }
}
