<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @infection-ignore-all */
final class Version20251113195509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the notification.notifications table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE SCHEMA notification
        ');

        $this->addSql('
            CREATE TABLE notification.notifications (
                serial SERIAL PRIMARY KEY,
                id UUID NOT NULL,
                type VARCHAR NOT NULL,
                channel VARCHAR NOT NULL,
                recipient VARCHAR NOT NULL,
                cc_recipients JSONB NOT NULL,
                bcc_recipients JSONB NOT NULL,
                sender VARCHAR NOT NULL,
                subject VARCHAR NOT NULL,
                body TEXT NOT NULL,
                status VARCHAR NOT NULL,
                occurred_at TIMESTAMPTZ NOT NULL,
                UNIQUE (id)
            );
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS type_idx
                ON notification.notifications (type);
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS channel_idx
                ON notification.notifications (channel);
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS recipient_idx
                ON notification.notifications (recipient);
        ');
    }
}
