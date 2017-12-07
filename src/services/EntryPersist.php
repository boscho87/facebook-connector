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
            $fbEntry->content = isset($entry->message) ? json_encode($entry->message) : '';
            $fbEntry->created = strtotime($entry->created_time);
            $fbEntry->has_detail = false;
            $fbEntry->save();
            return true;
        }
        return false;
    }

    /**
     * get the attachment and save it to the entry
     */
    public function loadEntryDetail()
    {
        $count = 0;
        $entryParser = new EntryParser();
        $entries = FacebookEntry::findAll(['has_detail' => false]);
        foreach ($entries as $entry) {
            $count++;
            $attachment = FacebookConnector::$plugin->entryFetcher->getEntryAttachments($entry->fbId);
            $entry->title = $entry->title ?? $attachment->title ?? '';
            $entry = $entryParser->parseEntry($entry, $attachment);
            //Todo this have to be after entry parse, because type is set after
            if ($entry->type == 'event') {
                //Todo extract this
                $eventId = preg_replace('/\d+_{1}/', '', $entry->fbId);
                $event = FacebookConnector::$plugin->entryFetcher->getEventDetails($eventId);
                $entry->event_cover_offset_y = $event->cover->offset_y;
                $entry->event_cover_offset_x = $event->cover->offset_x;
                $entry->event_cover_source = $event->cover->source;
                $entry->start_time = strtotime($event->start_time);
                $entry->end_time = strtotime($event->end_time);
            }
            $entry->has_detail = true;
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
