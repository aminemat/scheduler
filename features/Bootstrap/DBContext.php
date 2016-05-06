<?php

namespace Feature\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use DateTime;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DBContext implements Context, SnippetAcceptingContext
{
    /**
     * @var Connection
     */
    private $conn;
    /**
     * @var
     */
    private $dbUrl;
    /**
     * @var string
     */
    private $schemaFile;

    /**
     * DBContext constructor.
     * @param string $dbUrl
     * @param string $schemaFile
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct($dbUrl, $schemaFile)
    {
        $config = new Configuration();
        $connectionParams = array(
            'url' => (string) $dbUrl,
        );
        $this->conn = DriverManager::getConnection($connectionParams, $config);
        $this->dbUrl = $dbUrl;
        $this->schemaFile = $schemaFile;
    }


    /**
     * Prepare system for test suitebefore it runs
     * @BeforeScenario
     * @param BeforeScenarioScope $scope
     */
    public function prepare(BeforeScenarioScope $scope)
    {
        $this->recreateDatabase();

    }

    private function recreateDatabase()
    {
        $schemaFile = __DIR__ . '/../../' . $this->schemaFile;
        if (!file_exists($schemaFile)) {
            throw new \Exception('database file does not exits');
        }

        $sql = file_get_contents($schemaFile);
        $this->conn->executeQuery($sql);
    }

    /**
     * @Given I have users:
     * @param TableNode $table
     */
    public function iHaveUsers(TableNode $table)
    {
        foreach ($table as $row) {
            $this->conn->createQueryBuilder()
                ->insert('user')
                ->values([
                    'id' => '?',
                    'name' => '?',
                    'email' => '?',
                    'role' => '?',
                    'password' => '?',
                    'phone' => '?',
                ])
                ->setParameters(array_values($row))
                ->execute();
        }
    }

    /**
     * @Given I have shifts:
     */
    public function iHaveShifts(TableNode $table)
    {
        foreach ($table as $row) {
            $this->conn->createQueryBuilder()
                ->insert('shift')
                ->values([
                    'id' => '?',
                    'employee_id' => '?',
                    'manager_id' => '?',
                    'break' => '?',
                    'start_time' => '?',
                    'end_time' => '?',
                ])
                ->setParameter(1, $row['id'])
                ->setParameter(2, $row['employee_id'])
                ->setParameter(3, $row['manager_id'])
                ->setParameter(4, $row['break'])
                ->setParameter(5, new \DateTime($row['start_time']), 'datetimetz')
                ->setParameter(6, new \DateTime($row['end_time']), 'datetimetz')
                ->execute();
        }
    }
    

    /**
     * @Given I set request header :arg1 with value :arg2
     */
    public function iSetRequestHeaderWithValue($arg1, $arg2)
    {
        throw new PendingException();
    }

}
