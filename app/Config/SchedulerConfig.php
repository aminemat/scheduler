<?php

namespace App\Config;

use Auryn\Injector;
use Doctrine\DBAL\Connection;
use Scheduler\Credentials\JsonBodyExtractor;
use Scheduler\Domain\Shift\Contract\ShiftFactoryInterface;
use Scheduler\Domain\Shift\Contract\ShiftRepositoryInterface;
use Equip\Auth\AdapterInterface;
use Equip\Auth\Credentials\ExtractorInterface as CredentialsExtractorInterface;
use Equip\Auth\Jwt\FirebaseGenerator;
use Equip\Auth\Jwt\FirebaseParser;
use Equip\Auth\Jwt\GeneratorInterface;
use Equip\Auth\Jwt\ParserInterface;
use Equip\Auth\Token\ExtractorInterface as TokenExtractorInterface;
use Equip\Auth\Token\QueryExtractor;
use Equip\Configuration\ConfigurationInterface;
use Scheduler\Auth\Adapter;
use Scheduler\Domain\User\Contract\TokenRepositoryInterface;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Persistence\DBALShiftRepository;
use Scheduler\Persistence\DBALTokenRepository;
use Scheduler\Persistence\DBALUserRepository;
use Scheduler\Persistence\ShiftFactory;

/**
 * Class SchedulerConfig
 * Wires the scheduler Application services
 */
class SchedulerConfig implements ConfigurationInterface
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Applies a configuration set to a dependency injector.
     *
     * @param Injector $injector
     */
    public function apply(Injector $injector)
    {
        $this->prepareRepositories($injector);
        $this->prepareJWTConfig($injector);
        $this->aliasInterfaces($injector);
    }

    /**
     * @param Injector $injector
     * @throws \Auryn\ConfigException
     */
    public function prepareRepositories(Injector $injector)
    {
        $DBALConnection = $injector->execute('Doctrine\DBAL\DriverManager::getConnection', [
            [
                'url' => $this->config['DB_URL']
            ]
        ]);

        $injector->define(DBALUserRepository::class, [$DBALConnection]);
        $injector->define(DBALTokenRepository::class, [$DBALConnection]);
        $injector->define(DBALShiftRepository::class, [$DBALConnection]);
        $injector->delegate(Connection::class, 'Doctrine\DBAL\DriverManager::getConnection');
    }

    /**
     * @param Injector $injector
     */
    public function prepareJWTConfig(Injector $injector)
    {
        $injector->define(
            'Equip\\Auth\\Jwt\\Configuration',
            [
                ':publicKey' => $this->config['JWT_KEY'],
                ':ttl' => $this->config['JWT_TTL'], // in seconds, e.g. 2 hours
                ':algorithm' => $this->config['JWT_ALG'],
            ]
        );

        $injector->define(
            QueryExtractor::class,
            [':parameter' => 'access-token']
        );
    }

    /**
     * @param Injector $injector
     * @throws \Auryn\ConfigException
     */
    public function aliasInterfaces(Injector $injector)
    {
        $injector->alias(
            UserRepositoryInterface::class,
            DBALUserRepository::class
        );

        $injector->alias(
            TokenExtractorInterface::class,
            QueryExtractor::class
        );

        $injector->alias(
            CredentialsExtractorInterface::class,
            JsonBodyExtractor::class
        );

        $injector->alias(
            UserRepositoryInterface::class,
            DBALUserRepository::class
        );

        $injector->alias(
            ShiftRepositoryInterface::class,
            DBALShiftRepository::class
        );

        $injector->alias(
            TokenRepositoryInterface::class,
            DBALTokenRepository::class
        );

        $injector->alias(
            AdapterInterface::class,
            Adapter::class
        );

        $injector->alias(
            GeneratorInterface::class,
            FirebaseGenerator::class
        );

        $injector->alias(
            ParserInterface::class,
            FirebaseParser::class
        );

        $injector->alias(
            ShiftFactoryInterface::class,
            ShiftFactory::class
        );
    }
}
