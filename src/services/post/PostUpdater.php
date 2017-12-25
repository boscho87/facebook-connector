<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 09.10.2017
 * Time: 20:54
 */

namespace itscoding\facebookconnector\services\post;

use itscoding\facebookconnector\records\PostMemorize;

class PostUpdater extends AbstractPostHandler
{

    /**
     * @var array values allowed to update
     */
    private $updateFields = ['message', 'privacy', 'tag'];


    /**
     * @param int $entryId
     * @return string
     * @codeCoverageIgnore
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
     * @inheritdoc
     * @codeCoverageIgnore
     */
    protected function savePostReference(string $checkSum, int $entryId, $facebookId): bool
    {
        $postMemorize = PostMemorize::findOne(['entryId' => $entryId]);
        $postMemorize->entryId = $entryId;
        $postMemorize->facebookId = $facebookId;
        $postMemorize->checksum = $checkSum;
        return $postMemorize->update();
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function submitPost($postData, $token, $entryId)
    {
        if (($postId = $this->entryChanged($entryId, $this->getCheckSum($postData)))) {
            $postData = array_intersect_key($postData, array_flip($this->updateFields));
            if ($this->sendRequest($postId, $postData, $token)) {
                return $postId;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    protected function sendRequest(string $postId, array $postData, string $token)
    {
        try {
            $this->facebook->post(
                $postId,
                $postData,
                $token
            );
            return true;
        } catch (\Exception $e) {
            //Todo handle the case if update does not work
            return false;
        }
    }
}
