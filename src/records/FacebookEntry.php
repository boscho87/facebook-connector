<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 05.11.2017
 * Time: 11:15
 */

namespace itscoding\facebookconnector\records;

use craft\db\ActiveRecord;

class FacebookEntry extends ActiveRecord
{

    /**
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%facebookconnector_facebook_entry}}';
    }
}
