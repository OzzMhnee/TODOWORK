<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903155239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Supprimer les contraintes de clé étrangère
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4ACC9A20');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        // Modifier les colonnes pour les passer en NOT NULL
        $this->addSql('ALTER TABLE comment CHANGE card_id card_id INT NOT NULL, CHANGE author_id author_id INT NOT NULL');
        // Recréer les contraintes de clé étrangère
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les contraintes de clé étrangère
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4ACC9A20');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        // Remettre les colonnes en NULLABLE
        $this->addSql('ALTER TABLE comment CHANGE card_id card_id INT DEFAULT NULL, CHANGE author_id author_id INT DEFAULT NULL');
        // Recréer les contraintes de clé étrangère
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
    }
}
