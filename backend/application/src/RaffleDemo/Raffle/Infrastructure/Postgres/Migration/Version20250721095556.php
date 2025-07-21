<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @infection-ignore-all  */
final class Version20250721095556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates Raffle schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA raffle');
    }
}
