# FacebookConnector plugin for Craft CMS 3.x
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/a7f189dbcb6645f6bef11d3ad00b617f)](https://www.codacy.com/app/boscho87/facebook-connector?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=boscho87/facebook-connector&amp;utm_campaign=Badge_Grade)

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
   
    //you can to some fancy stuff with callables 👍 !
    $description = function() use ($entry){
      return $entry->teaserSubTitle;  
    };
    
    //callables will not work here!
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
        'description' => $description() 
    ];
};
```

### Fetch Entries from facebook
To fetch entries from Facebook, you need to run some scripts (if you need to reload posts automatic, setup cronjobs for this)

```shell
#in your project (craft) dir run:
php craft facebook-connector/entry-fetch/fetch-list #loads a list with the entries (id, title and content)
php craft facebook-connector/entry-fetch/fetch-detail #loads the details for all entries, saved before
```
be aware, that the entries are incomplete, if the details are missing! (you can check in your twig template if an entry is incomplete, before you render it)

##### Entry properties:
```php
  [
    'id' => $this->primaryKey(),  // like every craft entry
    'fbId' => $this->string(255)->unique(),
    'dateCreated' => $this->dateTime()->notNull(), // like every craft entry
    'dateUpdated' => $this->dateTime()->notNull(), // like every craft entry
    'created' => $this->string(255)->notNull(), 
    'has_detail' => $this->boolean(), // if the entry details are loaded 
    'type' => $this->string(), // type like ['share','photo','youtube']
    'image_src' => $this->string(510), //
    'image_height' => $this->string(), //
    'image_width' => $this->string(), // 
    'target' => $this->string(510), // target url from facebook 
    'url' => $this->string(510), // facebook post url
    'internal' => $this->boolean(), // true if the post is from one of the craft pages where the plugin is installed
    'uid' => $this->uid(), // like every craft entry
    'title' => $this->string(510), // should be clear
    'video' => $this->string(510), // [its a link] only set if its a video 'type' (atm. works only wiht youtube
    'content' => $this->text() // text body 
  ]
```

##### Example (in action)
```html
<div class="col-lg-8 col-md-10 mx-auto">
    {% for fbEntry in craft.facebook.getEntries.where('internal = 0').andWhere('type != "event"').limit(3).all %}
        <div style="position: relative;">
            {% if fbEntry.type == 'youtube' %}
                <h3>    {{ fbEntry.title }}    </h3>
                <iframe width="560" height="315"
                        src="https://www.youtube.com/embed{{ fbEntry.video }}?rel=0&amp;controls=0&amp;showinfo=0"
                        frameborder="0"
                        allowfullscreen></iframe>
            {% else %}
                <div class="post-preview" style="float: left;width: 70%;">
                    <h2 class="post-title">
                        {{ fbEntry.title ?: 'Beitrag vom '~fbEntry.created|date('d.m.y') }}
                    </h2>
                    <p style="padding-bottom: 20px">{{ fbEntry.content }}</p>
                    <p class="post-meta" style="bottom: 0;position: absolute; ">
                        Gepostet auf
                        {% if fbEntry.target %}
                            <a href="{{ fbEntry.target }}" target="_blank"> Facebook</a>
                        {% else %}
                            Facebook
                        {% endif %}
                        am {{ fbEntry.created|date('d.m.y') }} um {{ fbEntry.created|date('H:i') }}
                    </p>
                </div>
                <div style="float: left;width: 30%">
                    {% if fbEntry.image_src %}
                        <img class="img-fluid" src="{{ fbEntry.image_src }}" alt="">
                    {% endif %}
                </div>
            {% endif %}
            <div style="clear: both"></div>
            <hr>
            </div>
    {% endfor %}
</div>
```





Because i'm too lazy (I do not like writing Tutorials) [here is a youtube link](https://link.com), where the whole setup is explained
     
     feel free to Contribute a written description :)
 
## FacebookConnector Roadmap
Some things to do:

* Finish the Documentation
* Add more features [Ideas]-[priority 1=high 5=low] 
  
    - Add possibility to choose user to post - 5
    - Show Likes(and like infos) to an entry on the Website - 4
    - etc. (contact me if you have an idea) 

Brought to you by [Simon Müller itsCoding](https://www.itscoding.ch)
