<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711133244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE index_column DROP FOREIGN KEY FK_64CD788F443707B0');
        $this->addSql('ALTER TABLE index_column CHANGE field_id field_id INT NOT NULL');
        $this->addSql('ALTER TABLE index_column ADD CONSTRAINT FK_64CD788F443707B0 FOREIGN KEY (field_id) REFERENCES attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D55031518C7');
        $this->addSql('DROP INDEX IDX_D754D55031518C7 ON menu_item');
        $this->addSql('ALTER TABLE menu_item DROP view_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE index_column DROP FOREIGN KEY FK_64CD788F443707B0');
        $this->addSql('ALTER TABLE index_column CHANGE field_id field_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE index_column ADD CONSTRAINT FK_64CD788F443707B0 FOREIGN KEY (field_id) REFERENCES attribute (id)');
        $this->addSql('ALTER TABLE menu_item ADD view_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D55031518C7 FOREIGN KEY (view_id) REFERENCES `index` (id)');
        $this->addSql('CREATE INDEX IDX_D754D55031518C7 ON menu_item (view_id)');
    }
}
