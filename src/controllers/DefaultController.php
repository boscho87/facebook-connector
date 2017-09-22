<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * Connect a Website to an Facbook Page
 *
 * @link      https://www.itscoding.ch
 * @copyright Copyright (c) 2017 Simon Müller itsCoding
 */

namespace boscho87fbconn\facebookconnector\controllers;

use boscho87fbconn\facebookconnector\FacebookConnector;

use craft\web\Controller;

/**
 * Default Controller
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class DefaultController extends Controller
{

    /**
     * @var bool|array Allows anonymous access to this controller's actions.
     * The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    /**
     * redirect to the facebook login form
     * actions/facebook-connector/default/auth
     * @return \yii\web\Response
     */
    public function actionAuth()
    {
        return $this->redirect(FacebookConnector::$plugin->tokenLoader->getLoginUrl());
    }

    /**
     * handle the redirect after the OAtuh dialog
     * redirect from facebook
     * actions/facebook-connector/default/callback
     */
    public function actionCallback()
    {
        if (FacebookConnector::$plugin->tokenLoader->handleCallback()) {
            //Todo render successpage (or redirect)
            return $this->render();
        }
        $errors = FacebookConnector::$plugin->tokenLoader->getErrorMessages();
        //Todo render error page
        return $this->render();
    }

}
