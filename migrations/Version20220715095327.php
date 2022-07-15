<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220715095327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `index` DROP FOREIGN KEY FK_807367017975B7E7');
        $this->addSql('ALTER TABLE `index` ADD CONSTRAINT FK_807367017975B7E7 FOREIGN KEY (model_id) REFERENCES form (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `index` DROP FOREIGN KEY FK_807367017975B7E7');
        $this->addSql('ALTER TABLE `index` ADD CONSTRAINT FK_807367017975B7E7 FOREIGN KEY (model_id) REFERENCES form (id)');
    }
}
