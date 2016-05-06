<?php

namespace Test\Credentials;

use Equip\Auth\Credentials;
use Psr\Http\Message\ServerRequestInterface;
use Scheduler\Credentials\JsonBodyExtractor;
use Mockery as m;

class JsonBodyExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function test_returns_a_credentials_instance_if_parameters_are_present_in_request()
    {
        $extractor = new JsonBodyExtractor();

        $requestMock = m::mock(ServerRequestInterface::class);
        $requestMock->shouldReceive('getParsedBody')->andReturn([
            'username' => 'foo',
            'password' => 'bar'
        ]);

        $actualCredentials = $extractor->getCredentials($requestMock);
        $expectedCredentials = new Credentials('foo', 'bar');

        $this->assertEquals($expectedCredentials, $actualCredentials);
    }

    public function test_returns_null_if_parameters_are_not_present_in_request()
    {
        $extractor = new JsonBodyExtractor();

        $requestMock = m::mock(ServerRequestInterface::class);
        $requestMock->shouldReceive('getParsedBody')->andReturn(null);


        $this->assertNull($extractor->getCredentials($requestMock));
    }
}
