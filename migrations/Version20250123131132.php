<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250123131132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A23180196901F54 ON chambers (number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CCC2E2CE4AF4C20 ON patients (card_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_969AFE422B36786B ON procedures (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2A23180196901F54 ON chambers');
        $this->addSql('DROP INDEX UNIQ_2CCC2E2CE4AF4C20 ON patients');
        $this->addSql('DROP INDEX UNIQ_969AFE422B36786B ON procedures');
    }
}
