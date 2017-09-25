<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * Connect a Website to an Facbook Page
 *
 * @link      https://www.itscoding.ch
 * @copyright Copyright (c) 2017 Simon Müller itsCoding
 */

namespace itscoding\facebookconnector\widgets;

use itscoding\facebookconnector\assetbundles\oauthwidget\OAuthWidgetAsset;
use itscoding\facebookconnector\FacebookConnector;
use Craft;
use craft\base\Widget;

/**
 * FacebookConnector Widget
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class OAuth extends Widget
{

    /**
     * Todo remove this
     * @var string The message to display
     */
    public $message = 'Hello, world.';


    /**
     * Returns the display name of this class.
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('facebook-connector', 'Facebook OAuth');
    }

    /**
     * Returns the path to the widget’s SVG icon.
     * @return string|null The path to the widget’s SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias("@itscoding/facebookconnector/assetbundles/oauthwidget/dist/img/OAuthWidget-icon.svg");
    }

    /**
     * Returns the widget’s maximum colspan.
     * @return int|null The widget’s maximum colspan, if it has one
     */
    public static function maxColspan()
    {
        return null;
    }

    /**
     * Returns the widget's body HTML.
     * @return string
     */
    public function getBodyHtml()
    {
        Craft::$app->getView()->registerAssetBundle(OAuthWidgetAsset::class);
        $cpTrigger = Craft::$app->config->getConfigSettings('general')->cpTrigger;
        return Craft::$app->getView()->renderTemplate(
            'facebook-connector/_components/widgets/OAuth_body',
            [
                'settings' => FacebookConnector::$plugin->getSettings(),
                'settingsLink' => '/' . $cpTrigger . '/settings/plugins/facebook-connector',
                'oAuthStatus' => FacebookConnector::$plugin->tokenLoader->loadValidToken(),
                'tokenLink' => FacebookConnector::$plugin->tokenLoader->getLoginUrl()
            ]
        );
    }
}
