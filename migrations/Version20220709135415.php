<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220709135415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, label_id INT DEFAULT NULL, form_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, disabled TINYINT(1) DEFAULT NULL, required TINYINT(1) DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, fieldtype VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5BF5455833B92F39 (label_id), INDEX IDX_5BF545585FF69B7D (form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE input (id INT NOT NULL, checked TINYINT(1) NOT NULL, placeholder VARCHAR(255) DEFAULT NULL, readonly TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(255) NOT NULL, class VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(255) NOT NULL, disabled TINYINT(1) DEFAULT NULL, selected TINYINT(1) DEFAULT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `select` (id INT NOT NULL, multiple TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE select_option (select_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_A6969C5DCCDB6D (select_id), INDEX IDX_A6969C5DA7C41D6F (option_id), PRIMARY KEY(select_id, option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE textarea (id INT NOT NULL, placeholder VARCHAR(255) DEFAULT NULL, readonly TINYINT(1) DEFAULT NULL, cols INT DEFAULT NULL, rowscount INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF5455833B92F39 FOREIGN KEY (label_id) REFERENCES label (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF545585FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE input ADD CONSTRAINT FK_D82832D7BF396750 FOREIGN KEY (id) REFERENCES field (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `select` ADD CONSTRAINT FK_4BF2EAC0BF396750 FOREIGN KEY (id) REFERENCES field (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE select_option ADD CONSTRAINT FK_A6969C5DCCDB6D FOREIGN KEY (select_id) REFERENCES `select` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE select_option ADD CONSTRAINT FK_A6969C5DA7C41D6F FOREIGN KEY (option_id) REFERENCES `option` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE textarea ADD CONSTRAINT FK_89450896BF396750 FOREIGN KEY (id) REFERENCES field (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE input DROP FOREIGN KEY FK_D82832D7BF396750');
        $this->addSql('ALTER TABLE `select` DROP FOREIGN KEY FK_4BF2EAC0BF396750');
        $this->addSql('ALTER TABLE textarea DROP FOREIGN KEY FK_89450896BF396750');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF545585FF69B7D');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF5455833B92F39');
        $this->addSql('ALTER TABLE select_option DROP FOREIGN KEY FK_A6969C5DA7C41D6F');
        $this->addSql('ALTER TABLE select_option DROP FOREIGN KEY FK_A6969C5DCCDB6D');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE form');
        $this->addSql('DROP TABLE input');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE `select`');
        $this->addSql('DROP TABLE select_option');
        $this->addSql('DROP TABLE textarea');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
