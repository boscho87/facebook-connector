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
     * @codeCoverageIgnore
     */
    public function parseEntry($entry, $attachment)
    {
        $entry->type = $attachment->type ?? '';
        if (!$entry->title && !$entry->type) {
            $entry->type = 'notitle';
        }
        $media = $attachment->media ?? null;
        $image = isset($media->image) ? $media->image : null;
        $entry->image_src = $image->src ?? '';
        $entry->image_height = $image->height ?? '';
        $entry->image_width = $image->width ?? '';
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
     * @codeCoverageIgnore
     */
    private function checkIfIsLink($entry)
    {
        $url = parse_url(trim($entry->content));
        if (isset($url['host'])) {
            if ($this->ifYoutubeLink($entry->content)) {
                $entry->type = 'youtube';
                $entry->video = $url['path'];
                return;
            }
            $entry->type = 'link';
        }
    }

    /**
     * @param $url
     * @return bool
     * @codeCoverageIgnore
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

    /**
     * checks if a link has de host of an site registered in craft
     * in it (the sites are stored in the DBTable sections_sites
     * @param $url
     * @return bool
     * @codeCoverageIgnore
     */
    private function isInternalLink($url)
    {
        $sites = \Craft::$app->getSites();
        foreach ($sites as $site) {
            $host = parse_url(urldecode($site->baseUrl))['host'] ?? '';
            if (strpos($url, $host)) {
                return true;
            }
        }
        return false;
    }
}
