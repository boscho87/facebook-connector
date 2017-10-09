<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 09.10.2017
 * Time: 20:54
 */

namespace itscoding\facebookconnector\services\post;


use itscoding\facebookconnector\FacebookConnector;
use itscoding\facebookconnector\records\PostMemorize;

class PostCreator extends AbstractPostHandler
{

    /**
     * @inheritdoc
     */
    public function savePostReference(string $checkSum, int $entryId, $facebookId): bool
    {
        $postMemorize = new PostMemorize();
        $postMemorize->entryId = $entryId;
        $postMemorize->facebookId = $facebookId;
        $postMemorize->checksum = $checkSum;
        return $postMemorize->save();
    }

    /**
     * @inheritdoc
     */
    protected function submitPost($postData, $token, $entryId)
    {
        $pageId = FacebookConnector::getInstance()->getSettings()->pageId;
        return $this->sendRequest($pageId . '/feed', $postData, $token);
    }
}
