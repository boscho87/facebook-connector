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

use itscoding\facebookconnector\FacebookConnector;
use itscoding\facebookconnector\records\PostMemorize;
use craft\base\Component;
use craft\elements\Entry;
use itscoding\facebookconnector\services\post\AbstractPostHandler;
use itscoding\facebookconnector\services\post\PostHandlerFactory;

/**
 * EntryPoster Service
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class EntryPoster extends Component
{

    /**
     * @var ConfigFileLoader
     */
    private $configFileLoader;

    /**
     * @var PostHandlerFactory
     */
    private $postHandlerFactory;


    /**
     * initialize function by CraftCMS
     * @codeCoverageIgnore
     */
    public function init()
    {
        if (FacebookConnector::$plugin) {
            $this->configFileLoader = FacebookConnector::$plugin->configFileLoader;
            $this->postHandlerFactory = new PostHandlerFactory();
        }
    }

    /**
     * @param ConfigFileLoader $configFileLoader
     */
    public function setConfigFileLoader(ConfigFileLoader $configFileLoader): void
    {
        $this->configFileLoader = $configFileLoader;
    }

    /**
     * @param PostHandlerFactory $postHandlerFactory
     */
    public function setPostHandlerFactory(PostHandlerFactory $postHandlerFactory): void
    {
        $this->postHandlerFactory = $postHandlerFactory;
    }

    /**
     * @param Entry $entry
     * @return array
     */
    public function getPostData(Entry $entry)
    {
        $config = $this->configFileLoader->getConfigFile();
        $default = [
            'post_on_facebook' => $entry->getFieldValue('post_on_facebook') ?? true,
            'link' => $entry->getUrl(),
            'entry_id' => $entry->id,
            'message' => $entry->getFieldValue('message') ?? '',
            'picture' => '',
            'caption' => '',
            'description' => ''
        ];
        return array_merge($default, $config($entry));
    }

    /**
     * called from the event-listener
     * FacebookConnector::$plugin->entryPoster->post()
     * @param Entry $entryType
     * @codeCoverageIgnore
     */
    public function post(Entry $entry)
    {
        $token = FacebookConnector::$plugin->tokenLoader->loadValidToken();
        if (!$token) {
            return $this->handleInvalidToken($token);
        }
        $postData = $this->getPostData($entry);
        if ($postData['post_on_facebook']) {
            $postHandler = $this->loadPostHandler($entry->getId());
            return $postHandler->post($postData, $token, $entry->getId());
        }
        return true;
    }

    /**
     * @param string $entryId
     * @return AbstractPostHandler
     */
    public function loadPostHandler(string $entryId, bool $forceUpdater = false): AbstractPostHandler
    {
        if ((FacebookConnector::$plugin && (bool)PostMemorize::findOne(['entryId' => $entryId])) || $forceUpdater) {
            return $this->postHandlerFactory->getPostUpdater();
        }
        return $this->postHandlerFactory->getPostCreator();
    }

    /**
     * @param $token
     * @return bool
     */
    public function handleInvalidToken($token)
    {
        //Todo do not post and send a message to the user what he clould to to fix this
        //maybe set a flash message
        var_dump($token, 'no token found');
        return false;
    }
}
