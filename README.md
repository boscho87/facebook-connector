# FacebookConnector plugin for Craft CMS 3.x
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/e5bad32b62e145c29188de7645170778)](https://www.codacy.com/app/boscho87/facebook-connector?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=boscho87/facebook-connector&amp;utm_campaign=Badge_Grade)

Connect your Website with a Facebook Page

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Contribute to the Project

  - I will create a "CONTRIBUTE.md" file

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:
        cd /path/to/project

2. Then tell Composer to load the plugin:
        composer require boscho87/facebook-connector

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for FacebookConnector.

## FacebookConnector Overview
 ### Post your Craft entry on facebook in no Time!

 - the name is not 100% Okay, for now its only to post craft entries to facebook when the entry will be saved

## Configuring FacebookConnector

```php
add the file fieldconfig.php an return a callable that returns an array   
`/var/www/htdocs/craft/fieldconfig.php`

<?php
use itscoding\facebookconnector\FacebookConnector;
use craft\elements\Entry;

return function (Entry $entry) {
    return [
        //if true, the message is posted {default:true}
        'post_on_facebook' => $entry->post_on_facebook,
        //official doc https://developers.facebook.com/docs/graph-api/reference/v2.10/post
        //craft field to get the message to post from {default:''}
        'message' => $entry->subTitle,
        //link to the entry page {default:$entry->getUrl()}
        'link' => $entry->getUrl(),
        //field to get the img url from {default:''} --> no image
        'picture' => (count($entry->fb_image) > 0) ? FacebookConnector::getBaseUrl() . $entry->fb_image->first()->getUrl() : '',
        //field to get the caption from {default:''}
        'caption' => $entry->getAuthor()->getName(),
        //field to get the description from {default:''}
        'description' => $entry->teaserSubTitle
    ];
};
```

 - Add Configure screenshot and explenation here
 
 
## Using FacebookConnector

### Todo write doc for this!

## FacebookConnector Roadmap

Some things to do:

* Create a translation for the german language
* Crate a cool Icon
* Testing
* Bugfixing
* Codeguideline fixes
* Release it
* Finish the Documentation
* Add more features

Brought to you by [Simon Müller itsCoding](https://www.itscoding.ch)
