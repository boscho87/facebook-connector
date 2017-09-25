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
use Facebook\Authentication\AccessToken;


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
     * @var array values allowed to update
     */
    private $updateFields = ['message', 'privacy', 'tag'];

    /**
     * @param Entry $entry
     * @return array
     */
    private function getPostData(Entry $entry)
    {
        try {
            $config = include \Craft::$app->vendorPath . '/../' . 'fieldconfig.php';
        } catch (\Exception $e) {
            $config = function () {
                return [];
            };
        }
        $default = [
            'post_on_facebook' => $entry->post_on_facebook ?? true,
            'link' => $entry->getUrl(),
            'entry_id' => $entry->id,
            'message' => $entry->subTitle ?? '',
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
     */
    public function post(Entry $entry)
    {
        $token = FacebookConnector::$plugin->tokenLoader->loadValidToken();
        if (!$token) {
            return $this->handleInvalidToken($token);
        }
        $postData = $this->getPostData($entry);
        if ($postData['post_on_facebook']) {
            $checkSum = md5(serialize($postData));
            if ($this->isNewEntry($entry->getId())) {
                $postId = $this->postNew($postData, $token);
                $this->saveCheckSum($checkSum, $entry->getId(), $postId);
            } elseif (($postId = $this->entryChanged($entry->getId(), $checkSum))) {
                $updated = $this->updatePost($postId, $postData, $token);
                if (!$updated) {
                    //Todo translate
                    \Craft::$app->session->setError(
                        'Post to update not found, deleted reference and set post_on_facebook to false'
                    );
                    //Todo set the post_on_facebook and save the entry
                    return false;
                }
                $this->saveCheckSum($checkSum, $entry->getId(), $postId);
            }
        }
        return true;
    }

    /**
     * post a new entry to facebook
     * @param array $postData
     * @param AccessToken $token
     */
    private function postNew(array $postData, AccessToken $token)
    {
        $pageId = FacebookConnector::getInstance()->getSettings()->pageId;
        return $this->sendRequest($pageId . '/feed', $postData, $token);
    }

    /**
     * update a entry on facebook
     * @param array $postData
     * @param AccessToken $token
     */
    private function updatePost($postId, array $postData, AccessToken $token)
    {
        $postData = array_intersect_key($postData, array_flip($this->updateFields));
        return $this->sendRequest($postId, $postData, $token);
    }

    /**
     * @param string $endPoint
     * @param array $postData
     * @param AccessToken $token
     * @return mixed string|null
     */
    private function sendRequest(string $endPoint, array $postData, AccessToken $token)
    {
        try {
            $fb = FacebookConnector::$plugin->tokenLoader->getFacebookInstance();
            $response = $fb->post(
                $endPoint,
                $postData,
                FacebookConnector::$plugin->tokenLoader->exchangePageToken($token)
            );
            //if its a new post return the id of the created post
            return $response->getDecodedBody()['id'] ?? true;
        } catch (\Exception $e) {
            //the endpoint is the facebook if (on update)
            $this->removeCheckSum($endPoint);
            return false;
        }
    }

    /**
     * @param array $postData
     * @param string $entryId
     * @param $facebookId
     */
    private function saveCheckSum(string $checkSum, int $entryId, $facebookId)
    {
        $postMemorize = PostMemorize::findOne(['entryId' => $entryId]);
        if (!$postMemorize) {
            $postMemorize = new PostMemorize();
        }
        $postMemorize->entryId = $entryId;
        $postMemorize->facebookId = $facebookId;
        $postMemorize->checksum = $checkSum;
        if ($postMemorize->isNewRecord) {
            $postMemorize->save();
            return;
        }
        $postMemorize->update();
    }

    /**
     * @param int $entryId
     */
    private function removeCheckSum(string $facebookId)
    {
        $postMemorize = PostMemorize::findOne(['facebookId' => $facebookId]);
        if ($postMemorize) {
            return $postMemorize->delete();
        }
        return false;
    }

    /**
     * @param string $entryId
     * @return bool
     */
    private function isNewEntry(string $entryId)
    {
        return !(bool)PostMemorize::findOne(['entryId' => $entryId]);
    }

    /**
     * @param int $entryId
     * @return string
     */
    private function entryChanged(int $entryId, string $checkSum)
    {
        $postMemorize = PostMemorize::findOne(['entryId' => $entryId]);
        /** @var $postMemorize \stdClass */
        if ($postMemorize && $postMemorize->checksum != $checkSum) {
            return $postMemorize->facebookId;
        }
        return false;
    }

    /**
     * @param $token
     * @return bool
     */
    private function handleInvalidToken($token)
    {
        //Todo do not post and send a message to the user what he clould to to fix this
        //maybe set a flash message
        var_dump($token, 'no token found');
        return false;
    }

}
