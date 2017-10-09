# FacebookConnector plugin for Craft CMS 3.x
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/e5bad32b62e145c29188de7645170778)](https://www.codacy.com/app/boscho87/facebook-connector?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=boscho87/facebook-connector&amp;utm_campaign=Badge_Grade)

Connect your Website with a Facebook Page

![Screenshot](resources/img/icon.png)

## FacebookConnector Overview
 ### Post your Craft entry as a Post on Facebook with simple Setup

## Requirements
This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Contribute to the Project
if you want to help with this project, [read how to contribute](CONTRIBUTE.md)

## Installation
To install the plugin, follow these instructions.
1. Open your terminal and go to your Craft project:

        cd /path/to/project
        
2. Then tell Composer to load the plugin:

       composer require itscoding/facebook-connector
       
3. In the Control Panel, go to Settings → Plugins and click the “Install” button for FacebookConnector.

4. Configure the plugin

## Configuring FacebookConnector
### Post an Entry on facebook
add the file fieldconfig.php and return a callable that returns an array with your fields
`/path/to/project/fieldconfig.php`

```php
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

Because i'm Lazy (I do not like writing Tutorial) [here a youtube link](https://link.com), where the whole setup is explained
     
     feel free to Contribute a written description :)
 
## FacebookConnector Roadmap
Some things to do:

* Finish the Documentation
* Add more features [Ideas]-[priority 1=high 5=low] 
    - Get Events from Facebook  - 2 (CronJobbed Task)
    - Add possibility to choose user to post - 5
    - Show Likes(and like infos) to an entry on the Website - 2
    - etc. (contact me if you have an idea) 

Brought to you by [Simon Müller itsCoding](https://www.itscoding.ch)
