<?php

use craft\elements\Entry;

return function (Entry $entry) {
    return [
        'post_on_facebook' => true,
        'entry_id' => $entry->id,
        'message' => 'mockMe',
        'link' => $entry->getUrl(),
        'picture' => 'http://wowslider.com/sliders/demo-93/data1/images/sunset.jpg',
        'caption' => 'Simon Müller',
        'description' => 'lorem ipsum dolor',
    ];
};
