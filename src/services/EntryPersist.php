<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 05.11.2017
 * Time: 11:10
 */

namespace itscoding\facebookconnector\services;

use craft\base\Component;
use itscoding\facebookconnector\FacebookConnector;
use itscoding\facebookconnector\records\FacebookEntry;

/**
 * TokenLoader Service
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.9.0
 */
class EntryPersist extends Component
{

    /**
     * @param \stdClass $entry
     */
    public function persist(\stdClass $entry)
    {
        if (!FacebookEntry::findOne(['fbId' => $entry->id])) {
            $fbEntry = new FacebookEntry();
            $fbEntry->fbId = $entry->id;
            $fbEntry->content = $entry->message ?? '';
            $fbEntry->created = strtotime($entry->created_time);
            $fbEntry->save();
        }
    }

    /**
     * get the attachment and save it to the entry
     */
    public function loadEntryDetail()
    {
        $count = 0;
        $parser = new EntryParser();
        $entries = FacebookEntry::findAll(['attachment' => null]);
        foreach ($entries as $entry) {
            $count++;
            $attachment = FacebookConnector::$plugin->entryFetcher->getEntryAttachments($entry->fbId);
            $entry->title = $entry->title ?? $attachment->title ?? '';
            $entry = $parser->parseEntry($entry, $attachment);
            $entry->attachment = 'loaded';
            $entry->update();
        }
        return $count;
    }

    /**
     * @return array|mixed
     */
    public function getEntries()
    {
        return FacebookEntry::find();
    }
}