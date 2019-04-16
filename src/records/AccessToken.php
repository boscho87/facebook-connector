<?php


namespace itscoding\facebookconnector\records;

use craft\db\ActiveRecord;

/**
 * AccessToken Record
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class AccessToken extends ActiveRecord
{

    /**
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%facebookconnector_accesstoken}}';
    }
}
