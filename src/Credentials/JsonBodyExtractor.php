<?php

namespace Scheduler\Credentials;

use Equip\Auth\Credentials\ExtractorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Equip\Auth\Credentials;

/**
 * Extracts credentials from top-level properties of a json request body.
 */
class JsonBodyExtractor implements ExtractorInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $identifier
     *                           Name of the property that identifies the user
     * @param string $password
     *                           Name of the property that contains the user password
     */
    public function __construct($identifier = 'username', $password = 'password')
    {
        $this->identifier = $identifier;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(ServerRequestInterface $request)
    {
        $body = (array) $request->getParsedBody();

        if (empty($body[$this->identifier]) || empty($body[$this->password])) {
            return;
        }

        return new Credentials($body[$this->identifier], $body[$this->password]);
    }
}
