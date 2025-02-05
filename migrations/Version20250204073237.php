<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204073237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambers_patients CHANGE chambers_id chambers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chambers_patients ADD CONSTRAINT FK_E24B50C047FF4606 FOREIGN KEY (chambers_id) REFERENCES chambers (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambers_patients DROP FOREIGN KEY FK_E24B50C047FF4606');
        $this->addSql('ALTER TABLE chambers_patients CHANGE chambers_id chambers_id INT NOT NULL');
    }
}
