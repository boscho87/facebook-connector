<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 05.11.2017
 * Time: 14:01
 */

namespace itscoding\facebookconnector\services;

/**
 * Class EntryParser
 * @package itscoding\facebookconnector\services
 */
class EntryParser
{


    private $youtubeLinkTypeStrPos = ['youtu.be'];

    /**
     * @param $entry
     * @param $parsed
     * @param $key
     * @return mixed
     */
    public function parseEntry($entry, $attachment)
    {
        $entry->type = $attachment->type ?? '';
        if (!$entry->title && !$entry->type) {
            $entry->type = 'notitle';
        }
        $media = $attachment->media ?? null;
        $entry->image = $media->image ?? null;
        $entry->target = $attachment->target->url ?? null;
        $entry->url = $attachment->url ?? '';
        $entry->internal = $this->isInternalLink($entry->target);
        $this->checkIfIsLink($entry);
        return $entry;
    }

    /**
     * checks if the entry only is a link, if this is the case,
     * set the type
     * @param $entry
     */
    private function checkIfIsLink($entry)
    {
        $url = parse_url(trim($entry->content));
        if (isset($url['host'])) {
            if ($this->ifYoutubeLink($entry->content)) {
                $entry->type = 'youtube';
            //    $entry->video = $url['path'];
                return;
            }
            $entry->type = 'link';
        }
    }

    /**
     * @param $url
     * @return bool
     */
    private function ifYoutubeLink($url)
    {
        foreach ($this->youtubeLinkTypeStrPos as $strPos) {
            if (strpos($url, $strPos)) {
                return true;
            }
        }
        return false;
    }

    private function isInternalLink($url)
    {
        //$actual_link = $_SERVER['HTTP_HOST'];
        $actual_link = 'ddd';
        return strpos($url, $actual_link) > 0;
    }

}
