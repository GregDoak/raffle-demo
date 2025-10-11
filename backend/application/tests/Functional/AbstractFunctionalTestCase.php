<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function base64_encode;
use function sprintf;

abstract class AbstractFunctionalTestCase extends WebTestCase
{
    protected const string ADMIN_USER = 'admin@example.com';
    protected Application $application;
    protected KernelBrowser $client;
    protected Connection $connection;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->application = new Application($this->client->getKernel());

        $this->connection = self::getContainer()->get(Connection::class);

        $this->connection->beginTransaction();
    }

    public function getAdminUserToken(): string
    {
        return sprintf('Bearer %s', base64_encode(self::ADMIN_USER));
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();

        parent::tearDown();
    }
}
