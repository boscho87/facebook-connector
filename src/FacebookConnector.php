<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * Connect a Website to an Facbook Page
 *
 * @link      https://www.itscoding.ch
 * @copyright Copyright (c) 2017 Simon Müller itsCoding
 */

namespace itscoding\facebookconnector;

use itscoding\facebookconnector\services\EntryPoster as EntryPosterService;
use itscoding\facebookconnector\services\EventFetcher as EventFetcherService;
use itscoding\facebookconnector\services\TokenLoader as TokenLoaderService;
use itscoding\facebookconnector\services\EntryPoster;
use itscoding\facebookconnector\services\EventFetcher;
use itscoding\facebookconnector\models\Settings;
use itscoding\facebookconnector\services\TokenLoader;

use itscoding\facebookconnector\widgets\OAuth;
use Craft;
use craft\base\Plugin;
use craft\elements\Entry;
use Craft\events\ModelEvent;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 *
 * @property  EntryPosterService $entryPoster
 * @property  EventFetcherService $eventFetcher
 * @property  TokenLoaderService $tokenLoader
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class FacebookConnector extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * FacebookConnector::$plugin
     *
     * @var FacebookConnector
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * FacebookConnector::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {

        parent::init();
        self::$plugin = $this;
        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'itscoding\facebookconnector\console\controllers';
        }
        $this->setComponents([
                'tokenLoader' => TokenLoader::class,
                'entryPoster' => EntryPoster::class,
                'eventFetcher' => EventFetcher::class
            ]);

        Event::on(
            Entry::class,
            Entry::EVENT_AFTER_SAVE,
            function (ModelEvent $event) {
                if ($event->isValid) {
                    FacebookConnector::$plugin->entryPoster->post($event->sender);
                }
            }
        );

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'facebook-connector/default';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'facebook-connector/default/do-something';
            }
        );


        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                if (Craft::$app->request->get('code')) {
                    FacebookConnector::$plugin->tokenLoader->handleCallback();
                    $errors = FacebookConnector::$plugin->tokenLoader->getErrorMessages();
                    if (!count($errors) > 0) {
                        Craft::$app->session->setNotice('Loaded a Valid Token');
                    }
                    Craft::$app->session->setError(implode(' ', $errors));
                }
                $event->types[] = OAuth::class;
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'facebook-connector',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
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
