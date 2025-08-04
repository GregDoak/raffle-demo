<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @infection-ignore-all */
final class Version20250731200431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the raffle.projection_raffle table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE raffle.projection_raffle (
                serial SERIAL PRIMARY KEY,
                id UUID NOT NULL,
                name VARCHAR NOT NULL,
                prize VARCHAR NOT NULL,
                created_at TIMESTAMPTZ NOT NULL,
                created_by VARCHAR NOT NULL,
                start_at TIMESTAMPTZ NOT NULL,
                started_at TIMESTAMPTZ,
                started_by VARCHAR,
                total_tickets INTEGER NOT NULL,
                remaining_tickets INTEGER NOT NULL,
                ticket_amount INTEGER NOT NULL,
                ticket_currency VARCHAR NOT NULL,
                close_at TIMESTAMPTZ NOT NULL,
                closed_at TIMESTAMPTZ,
                closed_by VARCHAR,
                draw_at TIMESTAMPTZ NOT NULL,
                drawn_at TIMESTAMPTZ,
                drawn_by VARCHAR,
                winning_allocation VARCHAR,
                winning_ticket_number INTEGER ,
                won_by VARCHAR,
                last_occurred_at TIMESTAMPTZ NOT NULL,
                UNIQUE (id)
            );
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffle_not_started_idx
                ON
                    raffle.projection_raffle (start_at ASC, started_at)
                WHERE
                    projection_raffle.started_at IS NULL
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffle_not_closed_idx
                ON
                    raffle.projection_raffle (close_at ASC, closed_at)
                WHERE
                    projection_raffle.closed_at IS NULL
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffle_not_drawn_idx
                ON
                    raffle.projection_raffle (draw_at ASC, drawn_at)
                WHERE
                    projection_raffle.drawn_at IS NULL
        ');
    }
}
