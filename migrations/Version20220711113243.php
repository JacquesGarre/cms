<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711113243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entity ADD model_id INT NOT NULL');
        $this->addSql('ALTER TABLE entity ADD CONSTRAINT FK_E2844687975B7E7 FOREIGN KEY (model_id) REFERENCES form (id)');
        $this->addSql('CREATE INDEX IDX_E2844687975B7E7 ON entity (model_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entity DROP FOREIGN KEY FK_E2844687975B7E7');
        $this->addSql('DROP INDEX IDX_E2844687975B7E7 ON entity');
        $this->addSql('ALTER TABLE entity DROP model_id');
    }
}
