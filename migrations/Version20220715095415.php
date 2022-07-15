<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220715095415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `index` DROP FOREIGN KEY FK_80736701297954F9');
        $this->addSql('ALTER TABLE `index` ADD CONSTRAINT FK_80736701297954F9 FOREIGN KEY (order_by_id) REFERENCES index_column (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `index` DROP FOREIGN KEY FK_80736701297954F9');
        $this->addSql('ALTER TABLE `index` ADD CONSTRAINT FK_80736701297954F9 FOREIGN KEY (order_by_id) REFERENCES index_column (id)');
    }
}
