<?php

namespace itscoding\facebookconnector\records;

use craft\db\ActiveRecord;

class PostMemorize extends ActiveRecord
{

    /**
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%facebookconnector_postmemorize}}';
    }
}
