<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @infection-ignore-all  */
final class Version20250726125857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds raffle event store';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE raffle.event_store (
                id SERIAL PRIMARY KEY,
                aggregate_name VARCHAR NOT NULL,
                aggregate_id UUID NOT NULL,
                aggregate_version INT NOT NULL,
                event_name VARCHAR NOT NULL,
                event_data JSONB NOT NULL,
                UNIQUE (aggregate_name, aggregate_id, aggregate_version)
            );
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS event_store_aggregate_idx
                ON raffle.event_store (aggregate_name, aggregate_id);
        ');
    }
}
