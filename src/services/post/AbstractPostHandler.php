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
        $this->facebook = FacebookConnector::$plugin->tokenLoader->getFacebookInstance();
    }

    /**
     * execute the post method from the facebook sdk
     * @param string $endPoint
     * @param array $postData
     * @param AccessToken $token
     * @return bool
     */
    protected function sendRequest(string $endPoint, array $postData, AccessToken $token)
    {
        try {
            $response = $this->facebook->post(
                $endPoint,
                $postData,
                FacebookConnector::$plugin->tokenLoader->exchangePageToken($token)
            );
            //if its a new post return the id of the created post, else return just true
            return $response->getDecodedBody()['id'] ?? true;
        } catch (\Exception $e) {
            //if the post is not working
            // $this->removePostReference($endPoint);
            return false;
        }
    }

    /**
     * template method to post the entries
     * @param $postData
     * @param $token
     * @param $entryId
     * @return bool
     */
    public function post($postData, $token, $entryId)
    {
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
    protected abstract function submitPost($postData, $token, $entryId);

    /**
     * update the post reference to the local database
     * @param string $checkSum
     * @param int $entryId
     * @param $facebookId
     * @return bool
     */
    protected abstract function savePostReference(string $checkSum, int $entryId, $facebookId): bool;

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
