<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220715101736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D55031518C7');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D55031518C7 FOREIGN KEY (view_id) REFERENCES `index` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D55031518C7');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D55031518C7 FOREIGN KEY (view_id) REFERENCES `index` (id)');
    }
}
