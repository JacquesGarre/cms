<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713111210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relation DROP INDEX UNIQ_6289474931518C7, ADD INDEX IDX_6289474931518C7 (view_id)');
        $this->addSql('ALTER TABLE relation DROP INDEX UNIQ_628947492C770F2E, ADD INDEX IDX_628947492C770F2E (mapped_by_id)');
        $this->addSql('ALTER TABLE relation CHANGE view_id view_id INT DEFAULT NULL, CHANGE mapped_by_id mapped_by_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relation DROP INDEX IDX_6289474931518C7, ADD UNIQUE INDEX UNIQ_6289474931518C7 (view_id)');
        $this->addSql('ALTER TABLE relation DROP INDEX IDX_628947492C770F2E, ADD UNIQUE INDEX UNIQ_628947492C770F2E (mapped_by_id)');
        $this->addSql('ALTER TABLE relation CHANGE view_id view_id INT NOT NULL, CHANGE mapped_by_id mapped_by_id INT NOT NULL');
    }
}
