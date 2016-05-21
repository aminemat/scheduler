<?php

namespace Feature\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use PHPUnit_Framework_Assert;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PHPUnit_Framework_Assert as Assert;
use Tebru\MultiArray;
use UnexpectedValueException;

/**
 * Defines application features from the specific context.
 */
class ApiContext implements Context
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var array
     */
    protected $placeHolders = [];

    /**
     * @var MultiArray
     */
    protected $responseBody;
    
    protected $accessToken;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($baseUri)
    {
        $client = new Client(['base_uri' => $baseUri]);
        $this->client = $client;
    }

    /**
     * Sets a HTTP Header.
     *
     * @param string $name  header name
     * @param string $value header value
     *
     * @Given /^I set header "([^"]*)" with value "([^"]*)"$/
     */
    public function iSetHeaderWithValue($name, $value)
    {
        $this->addHeader($name, $value);
    }


    /**
     * Sends HTTP request to specific relative URL.
     *
     * @param string $method request method
     * @param string $url    relative url
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)"$/
     */
    public function iSendARequest($method, $url)
    {
        $url = $this->prepareUrl($url);

        if (version_compare(ClientInterface::VERSION, '6.0', '>=')) {
            $this->request = new Request($method, $url, $this->headers);
        } else {
            $this->request = $this->client->request($method, $url);
            if (!empty($this->headers)) {
                $this->request->addHeaders($this->headers);
            }
        }

        $this->sendRequest();
    }


    /**
     * Sends HTTP request to specific URL with raw body from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $string request body
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with body:$/
     */
    public function iSendARequestWithBody($method, $url, PyStringNode $string)
    {
        $url = $this->prepareUrl($url);
        $string = (string) $string;

        $this->request = new Request($method, $url, $this->headers, $string);

        $this->sendRequest();
    }


    /**
     * Checks that response has specific status code.
     *
     * @param string $code status code
     *
     * @Then /^(?:the )?response code should be (\d+)$/
     */
    public function theResponseCodeShouldBe($code)
    {
        $expected = intval($code);
        $actual = intval($this->response->getStatusCode());
        Assert::assertSame($expected, $actual);
    }


    /**
     * Checks that response body contains JSON from PyString.
     *
     * Do not check that the response body /only/ contains the JSON from PyString,
     *
     * @param PyStringNode $jsonString
     *
     * @throws \RuntimeException
     *
     * @Then /^(?:the )?response should contain json:$/
     */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        $etalon = json_decode($this->replacePlaceHolder($jsonString->getRaw()), true);
        $actual = json_decode($this->response->getBody(), true);

        if (null === $etalon) {
            throw new \RuntimeException(
                "Can not convert etalon to json:\n" . $this->replacePlaceHolder($jsonString->getRaw())
            );
        }

        if (null === $actual) {
            throw new \RuntimeException(
                "Can not convert actual to json:\n" . $this->replacePlaceHolder((string) $this->response->getBody())
            );
        }

        Assert::assertGreaterThanOrEqual(count($etalon), count($actual));
        foreach ($etalon as $key => $needle) {
            Assert::assertArrayHasKey($key, $actual);
            Assert::assertEquals($etalon[$key], $actual[$key]);
        }
    }


    /**
     * Prints last response body.
     *
     * @Then print response
     */
    public function printResponse()
    {
        $request = $this->request;
        $response = $this->response;

        echo sprintf(
            "%s %s => %d:\n%s",
            $request->getMethod(),
            (string) ($request instanceof RequestInterface ? $request->getUri() : $request->getUrl()),
            $response->getStatusCode(),
            (string) $response->getBody()
        );
    }

    /**
     * Returns headers, that will be used to send requests.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Adds header
     *
     * @param string $name
     * @param string $value
     */
    protected function addHeader($name, $value)
    {
        if (isset($this->headers[$name])) {
            if (!is_array($this->headers[$name])) {
                $this->headers[$name] = array($this->headers[$name]);
            }

            $this->headers[$name][] = $value;
        } else {
            $this->headers[$name] = $value;
        }
    }

    /**
     * Removes a header identified by $headerName
     *
     * @param string $headerName
     */
    protected function removeHeader($headerName)
    {
        if (array_key_exists($headerName, $this->headers)) {
            unset($this->headers[$headerName]);
        }
    }

    private function sendRequest()
    {
        try {
            $this->response = $this->getClient()->send($this->request);
            $this->setMultiArray();
        } catch (RequestException $e) {
            $this->response = $e->getResponse();

            if (null === $this->response) {
                throw $e;
            }
        }
    }

    private function getClient()
    {
        if (null === $this->client) {
            throw new \RuntimeException('Client has not been set in WebApiContext');
        }

        return $this->client;
    }


    protected function prepareUrl($url)
    {
        if (!empty($this->accessToken)) {
            $operator = (strpos($url, '?') === false) ? '?' : '&';
            $url .= $operator . 'access-token=' . $this->accessToken;
        }

        return ltrim($url, '/');
    }

    /**
     * Replaces placeholders in provided text.
     *
     * @param string $string
     *
     * @return string
     */
    protected function replacePlaceHolder($string)
    {
        foreach ($this->placeHolders as $key => $val) {
            $string = str_replace($key, $val, $string);
        }

        return $string;
    }

    /**
     * @param $array
     */
    private function removeIgnoredValues(&$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $this->removeIgnoredValues($value);
            }
            
            if ('<ignore>' == $value) {
                unset($array[$key]);
            }
        }
        
        return $array;
    }

    /**
     * @Then /^The response validates:$/
     * @param TableNode $tableNode
     * @throws Exception
     */
    public function theResponseValidates(TableNode $tableNode)
    {
        try {
            foreach ($tableNode->getColumnsHash() as $row) {
                $property = $row['property'];
                $value = $row['value'];
                $type = $row['type'];

                $this->thePropertyExists($property);
                $this->thePropertyTypeIs($property, $type);
                $this->thePropertyEquals($property, $type, $value);
            }
        } catch (Exception $e) {
            $this->printLastApiResponse();

            throw $e;
        }
    }

    /**
     * @Then /^The "([^"]*)" property exists$/
     * @param string $property
     */
    public function thePropertyExists($property)
    {
        $exception = new UnexpectedValueException(sprintf('The property "%s" does not exist', $property));
        assert($this->responseBody->exists($property), $exception);
    }

    /**
     * @Then /^The "([^"]*)" property is of type "([^"]*)"$/
     * @param string $property
     * @param string $type
     */
    public function thePropertyTypeIs($property, $type)
    {
        $actualType = strtolower(gettype($this->responseBody->get($property)));
        $exception = new UnexpectedValueException(sprintf('The property "%s" does not equal expected type "%s", instead got "%s"', $property, $type, $actualType));
        assert($type === $actualType, $exception);
    }

    /**
     * @Then /^The "([^"]*)" property of type "([^"]*)" equals "([^"]*)"$/
     * @param string $property
     * @param string $type
     * @param string $value
     */
    public function thePropertyEquals($property, $type, $value)
    {
        if ('<skip>' === $value) {
            return null;
        }

        $actual = $this->responseBody->get($property);

        if ('<empty>' === $value) {
            PHPUnit_Framework_Assert::assertEmpty($actual);
            return;
        }

        if ('<url>' === $value) {
            PHPUnit_Framework_Assert::assertNotFalse(filter_var($actual, FILTER_VALIDATE_URL));
            return;
        }

        $exception = new Exception(sprintf('The property "%s" does not equal expected value "%s", instead got "%s"', $property, $value, (is_array($actual) ? json_encode($actual) : $actual)));

        if ($this->isRegex($value)) {
            $match = preg_match($value, $actual);
            assert(1 === $match, $exception);
        } else {
            $value = $this->getRealType($value, $type);
            assert($actual === $value, $exception);
        }
    }

    /**
     * Check if the string is surrounded by /.../
     *
     * @param string $string
     * @return bool
     */
    private function isRegex($string)
    {
        return ('/' === substr($string, 0, 1) && '/' === substr($string, -1));
    }

    /**
     * Get the real type of a value in a string
     *
     * @param string $value
     * @param string $type
     * @return bool|float|int|null
     */
    private function getRealType($value, $type)
    {
        switch ($type) {
            case 'integer':
                $value = (int)$value;
                break;
            case 'double':
                $value = (double)$value;
                break;
            case 'boolean':
                if ('true' === $value) {
                    $value = true;
                } elseif ('false' === $value) {
                    $value = false;
                } else {
                    throw new InvalidArgumentException('Variable of type boolean must be true or false');
                }
                break;
            case 'array':
                $value = explode(',', $value);
                break;
            case 'null':
                if ('null' === $value) {
                    $value = null;
                } else {
                    throw new InvalidArgumentException('Variable of type null must be null');
                }
                break;
            case 'string':
                break;
            default:
                throw new InvalidArgumentException('Unsupported type');
        }

        return $value;
    }

    private function printLastApiResponse()
    {
        echo json_encode(json_decode($this->response->getBody()->getContents()), JSON_PRETTY_PRINT);        
    }

    private function setMultiArray()
    {
        $responseContent = json_decode($this->response->getBody()->getContents(), true);
        $error = json_last_error();
        if ($error) {
            throw new InvalidArgumentException('Invalid json provided: ' . print_r($responseContent, true));
        }
        $this->responseBody = new MultiArray($responseContent);
    }
}
