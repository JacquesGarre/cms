<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711155424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE index_column DROP FOREIGN KEY FK_64CD788F443707B0');
        $this->addSql('ALTER TABLE index_column ADD CONSTRAINT FK_64CD788F443707B0 FOREIGN KEY (field_id) REFERENCES attribute (id)');
        $this->addSql('ALTER TABLE `option` DROP value');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE index_column DROP FOREIGN KEY FK_64CD788F443707B0');
        $this->addSql('ALTER TABLE index_column ADD CONSTRAINT FK_64CD788F443707B0 FOREIGN KEY (field_id) REFERENCES attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `option` ADD value VARCHAR(255) NOT NULL');
    }
}
