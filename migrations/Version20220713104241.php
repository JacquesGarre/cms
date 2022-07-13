<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713104241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE relation (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, view_id INT NOT NULL, position INT DEFAULT NULL, INDEX IDX_628947497975B7E7 (model_id), UNIQUE INDEX UNIQ_6289474931518C7 (view_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_628947497975B7E7 FOREIGN KEY (model_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_6289474931518C7 FOREIGN KEY (view_id) REFERENCES `index` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE relation');
    }
}
