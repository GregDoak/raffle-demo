<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @infection-ignore-all */
final class Version20251021121603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the raffle.projection_raffle_allocations_v1 table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE raffle.projection_raffle_allocations_v1 (
                serial SERIAL PRIMARY KEY,
                raffle_id UUID NOT NULL,
                hash VARCHAR NOT NULL,
                allocated_at TIMESTAMPTZ NOT NULL,
                allocated_to VARCHAR NOT NULL,
                quantity INTEGER NOT NULL,
                last_occurred_at TIMESTAMPTZ NOT NULL,
                UNIQUE (hash)
            );
        ', );

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffle_allocations_idx ON raffle.projection_raffle_allocations_v1 (raffle_id, allocated_at ASC)
        ');
    }
}
