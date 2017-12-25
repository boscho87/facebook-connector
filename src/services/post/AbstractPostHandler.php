<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 09.10.2017
 * Time: 21:01
 */

namespace itscoding\facebookconnector\services\post;

use Facebook\Authentication\AccessToken;
use itscoding\facebookconnector\FacebookConnector;
use itscoding\facebookconnector\records\PostMemorize;

/**
 * Class AbstractPostHandler
 * @package itscoding\facebookconnector\services\post
 * @codeCoverageIgnore
 */
abstract class AbstractPostHandler
{

    /**
     * @var \Facebook\Facebook
     */
    protected $facebook;

    /**
     * AbstractPostHandler constructor.
     */
    public function __construct()
    {
        if (FacebookConnector::$plugin) {
            $this->facebook = FacebookConnector::$plugin->tokenLoader->getFacebookInstance();
        }
    }

    /**
     * execute the post method from the facebook sdk
     * @param string $endPoint
     * @param array $postData
     * @param string $token
     * @return bool
     */
    abstract protected function sendRequest(string $endPoint, array $postData, string $token);

    /**
     * @param array $postData
     * @param AccessToken $token
     * @param int $entryId
     * @return bool
     */
    public function post(array $postData, AccessToken $token, int $entryId)
    {
        $token = FacebookConnector::$plugin->tokenLoader->exchangePageToken($token);
        $postId = $this->submitPost($postData, $token, $entryId);
        if ($postId) {
            return $this->savePostReference($this->getCheckSum($postData), $entryId, $postId);
        }
        return false;
    }

    /**
     * @param $postData
     * @return string checksum of the postData
     */
    protected function getCheckSum($postData)
    {
        return md5(serialize($postData));
    }

    /**
     * submit the post to facebook
     * @param $postData
     * @param $token
     * @param $entryId
     * @return mixed
     */
    abstract protected function submitPost($postData, $token, $entryId);

    /**
     * update the post reference to the local database
     * @param string $checkSum
     * @param int $entryId
     * @param $facebookId
     * @return bool
     */
    abstract protected function savePostReference(string $checkSum, int $entryId, $facebookId): bool;

    /**
     * @param int $entryId
     */
    protected function removePostReference(string $facebookId)
    {
        $postMemorize = PostMemorize::findOne(['facebookId' => $facebookId]);
        if ($postMemorize) {
            return $postMemorize->delete();
        }
        return false;
    }
}
