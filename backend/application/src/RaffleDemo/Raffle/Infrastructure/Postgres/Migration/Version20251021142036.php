<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @infection-ignore-all  */
final class Version20251021142036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the raffle.projection_raffle_winners_v1 table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE raffle.projection_raffle_winners_v1 (
                serial SERIAL PRIMARY KEY,
                raffle_id UUID NOT NULL,
                raffle_allocation_hash VARCHAR NOT NULL,
                drawn_at TIMESTAMPTZ NOT NULL,
                winning_ticket_number INTEGER NOT NULL,
                winner VARCHAR NOT NULL,
                last_occurred_at TIMESTAMPTZ NOT NULL,
                UNIQUE (raffle_id),
                UNIQUE (raffle_allocation_hash)
            );
        ', );

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffle_winners_idx ON raffle.projection_raffle_winners_v1 (raffle_id, drawn_at ASC)
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffle_winners_drawn_at_idx ON raffle.projection_raffle_winners_v1 (drawn_at ASC)
        ');
    }
}
