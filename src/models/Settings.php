<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * Connect a Website to an Facbook Page
 *
 * @link      https://www.itscoding.ch
 * @copyright Copyright (c) 2017 Simon Müller itsCoding
 */

namespace itscoding\facebookconnector\models;

use craft\base\Model;

/**
 * FacebookConnector Settings Model
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class Settings extends Model
{


    /**
     * @var string application id of facebook app
     */
    public $appId = 'appId';

    /**
     * @var string application secret of facebook app
     */
    public $appSecret = 'appSecret';

    /**
     * @var string pageId to post to
     */
    public $pageId = 'pageId';

    /**
     * @var string facebook api version
     */
    public $apiVersion = 'v3.1';

    /**
     * Returns the validation rules for attributes.
     * @return array
     */
    public function rules()
    {
        return [
            [['appId', 'appSecret', 'pageId', 'apiVersion'], 'required'],
        ];
    }
}
