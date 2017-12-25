<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 25.12.2017
 * Time: 10:53
 */

namespace itscoding\facebookconnector\services;


use craft\base\Component;

class ConfigFileLoader extends Component
{

    /**
     * @var string
     */
    private $configFile;

    /**
     *
     */
    public function init()
    {
        try {
            $this->configFile = \Craft::$app->vendorPath . '/../fieldconfig.php';
        } catch (\Exception $e) {
            $this->configFile;
        }
    }

    /**
     * @return string
     */
    public function getConfigFile(): callable
    {
        if ($this->configFile) {
            $config = include $this->configFile;
            if (is_callable($config)) {
                return $config;
            }
        }
        return function () {
            return [];
        };
    }

    /**
     * @param string $configFile
     */
    public function setConfigFile(?string $configFile): void
    {
        $this->configFile = $configFile;
    }

}
