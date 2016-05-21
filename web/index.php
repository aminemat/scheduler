<?php

require __DIR__ . '/../vendor/autoload.php';

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

Equip\Application::build()
    ->setConfiguration([
        Equip\Configuration\AurynConfiguration::class,
        Equip\Configuration\DiactorosConfiguration::class,
        Equip\Configuration\PayloadConfiguration::class,
        Equip\Configuration\RelayConfiguration::class,
        App\Config\ApplicationConfig::class,
        Equip\Configuration\MonologConfiguration::class,
        App\Config\SchedulerConfig::class
    ])
    ->setMiddleware([
        Relay\Middleware\ResponseSender::class,
        Equip\Handler\ExceptionHandler::class,
        Equip\Handler\DispatchHandler::class,
        Relay\Middleware\JsonContentHandler::class,
        Relay\Middleware\FormContentHandler::class,
        Equip\Auth\AuthHandler::class,
        Scheduler\Middleware\UserExtractor::class,
        Scheduler\Middleware\AuthorizationChecker::class,
        Equip\Handler\ActionHandler::class
    ])
    ->setRouting(function (Equip\Directory $directory) {
        return $directory
            ->post('/v1/login', Scheduler\Auth\LoginAction::class)
            ->get('/v1/shifts', new \Equip\Action(
                \Scheduler\Domain\Shift\Action\ShiftProvider::class,
                Scheduler\Responder\ShiftsResponder::class
            ))
            ->get('/v1/employee/{id}', new \Equip\Action(
                \Scheduler\Domain\User\Action\UserProvider::class,
                Scheduler\Responder\UserResponder::class
            ))
            ->post('/v1/shifts', new \Equip\Action(
                \Scheduler\Domain\Shift\Action\ShiftScheduler::class,
                Scheduler\Responder\ShiftResponder::class
            ))
            ->get('/v1/worked-hours', new \Equip\Action(
                \Scheduler\Domain\Shift\Action\WorkSummaryProvider::class,
                Scheduler\Responder\WorkSummaryResponder::class
            ))
            ->put('/v1/shifts/{id}', new \Equip\Action(
                \Scheduler\Domain\Shift\Action\ShiftUpdater::class,
                Scheduler\Responder\ShiftResponder::class
            ))
            ;
    })
    ->run();
