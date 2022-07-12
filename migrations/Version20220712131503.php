<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712131503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `index` ADD order_by_id INT DEFAULT NULL, ADD order_direction VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `index` ADD CONSTRAINT FK_80736701297954F9 FOREIGN KEY (order_by_id) REFERENCES index_column (id)');
        $this->addSql('CREATE INDEX IDX_80736701297954F9 ON `index` (order_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `index` DROP FOREIGN KEY FK_80736701297954F9');
        $this->addSql('DROP INDEX IDX_80736701297954F9 ON `index`');
        $this->addSql('ALTER TABLE `index` DROP order_by_id, DROP order_direction');
    }
}
