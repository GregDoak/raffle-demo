<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @infection-ignore-all  */
final class Version20251027203609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop raffles.projection_raffle';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS raffle.projection_raffle');
    }
}
