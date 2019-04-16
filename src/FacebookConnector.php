<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * Connect a Website to an Facebook Page
 *
 * @link      https://www.itscoding.ch
 * @copyright Copyright (c) 2017 Simon Müller itsCoding
 */

namespace itscoding\facebookconnector;

use craft\web\twig\variables\CraftVariable;
use Facebook\Exceptions\FacebookResponseException;
use itscoding\facebookconnector\services\ConfigFileLoader;
use itscoding\facebookconnector\services\EntryPersist as EntryPersistService;
use itscoding\facebookconnector\services\EntryPoster as EntryPosterService;
use itscoding\facebookconnector\services\EntryFetcher as EntryFetcherService;
use itscoding\facebookconnector\services\TokenLoader as TokenLoaderService;
use itscoding\facebookconnector\services\ConfigFileLoader as ConfigFileLoaderService;
use itscoding\facebookconnector\services\EntryPoster;
use itscoding\facebookconnector\services\EntryFetcher;
use itscoding\facebookconnector\models\Settings;
use itscoding\facebookconnector\services\TokenLoader;
use itscoding\facebookconnector\variables\EntryVariable;
use itscoding\facebookconnector\widgets\OAuth;
use Craft;
use craft\base\Plugin;
use craft\elements\Entry;
use Craft\events\ModelEvent;
use craft\console\Application as ConsoleApplication;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;

/**
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 *
 * @property  EntryPosterService $entryPoster
 * @property  EntryFetcherService $entryFetcher
 * @property  TokenLoaderService $tokenLoader
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class FacebookConnector extends Plugin
{


    
    /**
     * @var FacebookConnector
     */
    public static $plugin;

    /**
     * executed on every request
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'itscoding\facebookconnector\console\controllers';
        }
        $this->setComponents([
            'tokenLoader' => TokenLoaderService::class,
            'entryPoster' => EntryPosterService::class,
            'entryFetcher' => EntryFetcherService::class,
            'entryPersist' => EntryPersistService::class,
            'configFileLoader' => ConfigFileLoaderService::class
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('facebook', EntryVariable::class);
            }
        );

        Event::on(Entry::class, Entry::EVENT_AFTER_SAVE, function (ModelEvent $event) {
            if ($event->isValid) {
                FacebookConnector::$plugin->entryPoster->post($event->sender);
            }
        });

        Event::on(Dashboard::class, Dashboard::EVENT_REGISTER_WIDGET_TYPES, function (RegisterComponentTypesEvent $event) {
            $errors = [];
            $errors[] = Craft::$app->request->get('error_message');
            if (Craft::$app->request->get('code')) {
                try {
                    FacebookConnector::$plugin->tokenLoader->handleCallback();
                } catch (FacebookResponseException $e) {
                    $errors[] = $e->getMessage();
                }
                array_merge($errors, FacebookConnector::$plugin->tokenLoader->getErrorMessages());
                $errors = array_filter($errors);
                if ($errors) {
                    Craft::$app->session->setError(implode(' ', $errors));
                }
                if ((!count($errors)) > 0) {
                    Craft::$app->session->setNotice(
                        Craft::t('facebook-connector', 'Loaded a Valid Token')
                    );
                }
            }
            $event->types[] = OAuth::class;
        });


        Craft::info(
            Craft::t(
                'facebook-connector',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Creates and returns the model used to store the plugin’s settings.
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'facebook-connector/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
