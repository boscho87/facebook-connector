<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 05.11.2017
 * Time: 11:10
 */

namespace itscoding\facebookconnector\services;

use craft\base\Component;
use craft\db\ActiveRecord;
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
     * @var EntryParser
     */
    private $entryParser;


    /**
     * Init the Class
     */
    public function init()
    {
        $this->entryParser = new EntryParser();
        parent::init();
    }

    /**
     * @param \stdClass $entry
     * @codeCoverageIgnore
     */
    public function persistEntry(\stdClass $entry)
    {

        if (!FacebookEntry::findOne(['fbId' => $entry->id])) {
            $fbEntry = new FacebookEntry();
            $fbEntry->fbId = $entry->id;
            $fbEntry->content = $this->entryParser->parseContent($entry->message ?? '');
            $fbEntry->created = strtotime($entry->created_time);
            $fbEntry->image_src = $entry->full_picture;
            $fbEntry->has_detail = false;
            $fbEntry->save();
            return true;
        }
        return false;
    }

    /**
     * get the attachment and save it to the entry
     * @codeCoverageIgnore
     */
    public function persistEntryDetails()
    {
        $count = 0;
        //Todo entry persist should not make request to the api!
        $entries = FacebookEntry::findAll(['has_detail' => false]);
        foreach ($entries as $entry) {
            $count++;
            $attachment = FacebookConnector::$plugin->entryFetcher->getEntryAttachments($entry->fbId);
            $entry->title = $entry->title ?? '';
            $entry = $this->entryParser->parseEntry($entry, $attachment);
            if ($entry->type === 'event') {
                //Todo extract this
                $eventId = preg_replace('/\d+_{1}/', '', $entry->fbId);
                try {
                    $event = FacebookConnector::$plugin->entryFetcher->getEventDetails($eventId);
                    $entry->event_cover_offset_y = $event->cover->offset_y;
                    $entry->event_cover_offset_x = $event->cover->offset_x;
                    $entry->event_cover_source = $event->cover->source;
                    $entry->start_time = strtotime($event->start_time);
                    $entry->end_time = strtotime($event->end_time);
                } catch (\Exception $e) {
                    $entry->type = 'event_share';
                    echo 'event with id: ' . $eventId . ' could not be loaded (maybe its a shared object)';
                }
            }

            if ($entry->type === 'album') {
                if (isset($attachment)) {
                    $imgCount = 1;
                    //Todo cleanup this mess
                    foreach ($attachment->subattachments->data as $photo) {
                        $property = 'image_src_' . $imgCount;
                        $entry->$property = $photo->media->image->src;
                        $imgCount++;
                    }
                }
            }

            $entry->has_detail = true;
            $entry->update();
        }
        return $count;
    }

    /**
     * @return array|mixed
     * @codeCoverageIgnore
     */
    public function getEntries()
    {
        return FacebookEntry::find();
    }
}
