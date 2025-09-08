<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250907174344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card ADD scheduled_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3E89A792F FOREIGN KEY (scheduled_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_161498D3E89A792F ON card (scheduled_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3E89A792F');
        $this->addSql('DROP INDEX IDX_161498D3E89A792F ON card');
        $this->addSql('ALTER TABLE card DROP scheduled_by_id');
    }
}
