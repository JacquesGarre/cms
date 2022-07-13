<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713111547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relation ADD view_id INT NOT NULL, ADD mapped_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_6289474931518C7 FOREIGN KEY (view_id) REFERENCES `index` (id)');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_628947492C770F2E FOREIGN KEY (mapped_by_id) REFERENCES attribute (id)');
        $this->addSql('CREATE INDEX IDX_6289474931518C7 ON relation (view_id)');
        $this->addSql('CREATE INDEX IDX_628947492C770F2E ON relation (mapped_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relation DROP FOREIGN KEY FK_6289474931518C7');
        $this->addSql('ALTER TABLE relation DROP FOREIGN KEY FK_628947492C770F2E');
        $this->addSql('DROP INDEX IDX_6289474931518C7 ON relation');
        $this->addSql('DROP INDEX IDX_628947492C770F2E ON relation');
        $this->addSql('ALTER TABLE relation DROP view_id, DROP mapped_by_id');
    }
}
