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
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */

    public $appId = 'appId';

    public $appSecret = 'appSecret';

    public $pageId = 'pageId';

    public $apiVersion = 'v2.10';

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['appId', 'appSecret', 'pageId', 'apiVersion'], 'required'],
        ];
    }
}
