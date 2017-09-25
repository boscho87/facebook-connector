<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * Connect a Website to an Facbook Page
 *
 * @link      https://www.itscoding.ch
 * @copyright Copyright (c) 2017 Simon Müller itsCoding
 */

namespace itscoding\facebookconnector\services;

use itscoding\facebookconnector\FacebookConnector;
use itscoding\facebookconnector\records\AccessToken;
use Craft;
use Facebook\Authentication\AccessToken as FBAccessToken;
use Facebook\Authentication\OAuth2Client;
use craft\base\Component;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\Helpers\FacebookRedirectLoginHelper;

/**
 * TokenLoader Service
 *
 * @author    Simon Müller itsCoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class TokenLoader extends Component
{

    private $errorMessages = [];

    private $facebookPermissions = ['publish_pages', 'manage_pages'];

    private $appId = '';

    private $appSecret = '';

    private $pageId = '';

    private $baseUrl = '';

    private $apiVersion = '';

    private $initialized = false;

    /**
     * initialize all the options
     */
    public function initialize()
    {
        $this->initialized = true;
        $settings = FacebookConnector::getInstance()->getSettings();
        $this->appId = $settings->appId;
        $this->appSecret = $settings->appSecret;
        $this->apiVersion = $settings->apiVersion;
        $this->pageId = $settings->pageId;
        $this->baseUrl = Craft::$app->request->getHostInfo();
    }

    /**
     * FacebookConnector::$plugin->tokenLoader->getFacebookInstance()
     * @return Facebook
     */
    public function getFacebookInstance()
    {
        if (!$this->initialized) {
            $this->initialize();
        }
        return new Facebook([
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'default_graph_version' => $this->apiVersion,
            'persistent_data_handler' => 'session'
        ]);
    }

    /**
     * generate the url to redirect after the facebook authorization
     * @return string
     */
    public function getLoginUrl()
    {
        $fb = $this->getFacebookInstance();
        $helper = $fb->getRedirectLoginHelper();
        $cpTrigger = '/' . Craft::$app->config->getConfigSettings('general')->cpTrigger;
        return $helper->getLoginUrl(
            $this->baseUrl . $cpTrigger . '/dashboard',
            $this->facebookPermissions
        );
    }

    /**
     * this method is called
     * when facebook redirects back to the page
     * @return bool
     */
    public function handleCallback()
    {
        $fb = $this->getFacebookInstance();
        $helper = $fb->getRedirectLoginHelper();
        $accessToken = $this->getShortLivingAccessToken($helper);
        if ($accessToken) {
            $oAuth2Client = $this->validateToken($fb, $accessToken);
            $accessToken = $this->exchangeShortLivingToken($accessToken, $oAuth2Client);
            $this->storeToken($accessToken);
            $validPageToken = $this->exchangePageToken($accessToken);
            if ($validPageToken) {
                return true;
            }
            //no valid page token, because the user is not allowed on the page
            $this->errorMessages[] = 'user not allowed to post as page: ' . $this->pageId;
        }
        return false;
    }

    /**
     * @return FBAccessToken|null
     * get a valid user access token
     */
    public function loadValidToken()
    {
        return $this->loadTokenFromDb();
    }

    /**
     * load a valid token from the database
     * @return FBAccessToken|null
     */
    private function loadTokenFromDb()
    {
        /** @var  $dbToken AccessToken */
        $dbToken = AccessToken::find()->all();
        if (isset($dbToken[0])) {
            /** @var  $token FBAccessToken */
            $token = unserialize($dbToken[0]->data, [AccessToken::class]);
            if (!$token->isExpired()) {
                return $token;
            }
        }
        return null;
    }

    /**
     * store a token into the database
     * @param FBAccessToken $token
     */
    private function storeToken(FBAccessToken $token)
    {
        AccessToken::deleteAll();
        $dbToken = new AccessToken();
        $dbToken->data = serialize($token);
        $dbToken->save();
    }

    /**
     * get a access_token
     * @param FacebookRedirectLoginHelper $helper
     * @return mixed AccessToken|false
     */
    private function getShortLivingAccessToken(FacebookRedirectLoginHelper $helper)
    {
        try {
            $accessToken = $helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            $this->errorMessages[] = 'Graph returned an error: ' . $e->getMessage();
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            $this->errorMessages[] = 'Facebook SDK returned an error: ' . $e->getMessage();
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                $this->errorMessages[] = "Error: " . $helper->getError() . "\n";
                $this->errorMessages[] = "Error Code: " . $helper->getErrorCode() . "\n";
                $this->errorMessages[] = "Error Reason: " . $helper->getErrorReason() . "\n";
                $this->errorMessages[] = "Error Description: " . $helper->getErrorDescription() . "\n";
                return false;
            }
            $this->errorMessages[] = 'Bad request';
            return false;
        }
        return $accessToken;
    }

    /**
     * @param $facebook
     * @param $accessToken
     * @param $config
     * @return mixed
     */
    private function validateToken(Facebook $facebook, FBAccessToken $accessToken)
    {
        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $facebook->getOAuth2Client();
        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($this->appId);
        $tokenMetadata->validateExpiration();
        return $oAuth2Client;
    }

    /**
     * @param $accessToken
     * @param $oAuth2Client
     * @return mixed
     */
    private function exchangeShortLivingToken(FBAccessToken $accessToken, OAuth2Client $oAuth2Client)
    {
        if (!$accessToken->isLongLived()) {
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                $this->errorMessages[] = "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
            }
        }
        return $accessToken;
    }

    /**
     * exchange the user token with the a page token to post as a page
     * @param FBAccessToken $accessToken
     * @return \Facebook\FacebookResponse
     */
    public function exchangePageToken(FBAccessToken $accessToken)
    {
        $facebook = $this->getFacebookInstance();
        $response = $facebook->get($this->pageId . '?fields=access_token', $accessToken);
        return $response->getDecodedBody()['access_token'];
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }


}
