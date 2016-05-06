<?php

namespace Feature\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\StepNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends ApiContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param $baseUri
     */
    public function __construct($baseUri)
    {
        parent::__construct($baseUri);
    }

    /**
     * @Given I login with credentials :arg1 :arg2
     *
     * @param string $username
     * @param string $password
     *
     * @throws \Exception
     */
    public function iLoginWithCredentials($username, $password)
    {
        $this->iSendARequestWithBody(
            'POST',
            '/login', new PyStringNode([
                json_encode(['username' => $username, 'password' => $password]),
            ], 0)
        );
        $this->theResponseCodeShouldBe('200');
        
        $response = json_decode($this->response->getBody(), true);
        if (empty($response['token'])) {
            throw new \Exception('Token not found');
        }
        
        $this->accessToken = $response['token'];
    }

}
