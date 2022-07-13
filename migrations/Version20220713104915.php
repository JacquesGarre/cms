<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713104915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relation ADD mapped_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_628947492C770F2E FOREIGN KEY (mapped_by_id) REFERENCES attribute (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_628947492C770F2E ON relation (mapped_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relation DROP FOREIGN KEY FK_628947492C770F2E');
        $this->addSql('DROP INDEX UNIQ_628947492C770F2E ON relation');
        $this->addSql('ALTER TABLE relation DROP mapped_by_id');
    }
}
