<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @infection-ignore-all */
final class Version20251020183015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the raffle.projection_raffles_v1 table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE raffle.projection_raffles_v1 (
                serial SERIAL PRIMARY KEY,
                id UUID NOT NULL,
                name VARCHAR NOT NULL,
                prize VARCHAR NOT NULL,
                status VARCHAR NOT NULL,
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
                ended_at TIMESTAMPTZ,
                ended_by VARCHAR,
                ended_reason VARCHAR,
                last_occurred_at TIMESTAMPTZ NOT NULL,
                UNIQUE (id)
            );
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffles_name_idx ON raffle.projection_raffles_v1 (name)
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffles_prize_idx ON raffle.projection_raffles_v1 (prize)
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS projection_raffles_status_idx ON raffle.projection_raffles_v1 (status)
        ');

        $this->addSql("
            CREATE INDEX IF NOT EXISTS projection_raffle_not_started_idx
                ON
                    raffle.projection_raffles_v1 (start_at ASC, status)
                WHERE
                    projection_raffles_v1.status = 'created'
        ");

        $this->addSql("
            CREATE INDEX IF NOT EXISTS projection_raffle_not_closed_idx
                ON
                    raffle.projection_raffles_v1 (close_at ASC, status)
                WHERE
                    projection_raffles_v1.status = 'started'
        ");

        $this->addSql("
            CREATE INDEX IF NOT EXISTS projection_raffle_not_drawn_idx
                ON
                    raffle.projection_raffles_v1 (draw_at ASC, status)
                WHERE
                    projection_raffles_v1.status = 'closed'
        ");
    }
}
