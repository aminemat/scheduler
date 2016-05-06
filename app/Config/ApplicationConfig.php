<?php

namespace App\Config;

use Auryn\Injector;
use Equip\Configuration\ConfigurationInterface;
use MicroLib\Config as cfg;

/**
 * Class ApplicationConfig
 * Loads the configuration file specific to the environment
 */
class ApplicationConfig implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $configFile;

    /**
     * @param string $configFile
     *
     * @throws \Exception
     */
    public function __construct($configFile = null)
    {
        if (empty($configFile)) {
            $configFile = __DIR__ . "/../../config.json";
        }

        if (!is_file($configFile) || !is_readable($configFile)) {
            throw new \Exception(sprintf('config file [%s] not found', $configFile));
        }

        $this->configFile = $configFile;
    }

    /**
     * @inheritDoc
     */
    public function apply(Injector $injector)
    {
        $injector->share(Config::class);

        $injector->prepare(Config::class, function (Config $config, Injector $injector) {
            return $config->withData(cfg\load($this->configFile));
        });
    }
}
