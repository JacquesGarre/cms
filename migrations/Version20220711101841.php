<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711101841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attribute (id INT AUTO_INCREMENT NOT NULL, label_id INT DEFAULT NULL, form_id INT NOT NULL, name VARCHAR(255) NOT NULL, disabled TINYINT(1) DEFAULT NULL, required TINYINT(1) DEFAULT NULL, col INT DEFAULT NULL, position INT DEFAULT NULL, checked TINYINT(1) DEFAULT NULL, placeholder VARCHAR(255) DEFAULT NULL, readonly TINYINT(1) DEFAULT NULL, type VARCHAR(255) NOT NULL, default_value VARCHAR(255) DEFAULT NULL, multiple TINYINT(1) DEFAULT NULL, cols INT DEFAULT NULL, height INT DEFAULT NULL, UNIQUE INDEX UNIQ_FA7AEFFB33B92F39 (label_id), INDEX IDX_FA7AEFFB5FF69B7D (form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute_option (attribute_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_78672EEAB6E62EFA (attribute_id), INDEX IDX_78672EEAA7C41D6F (option_id), PRIMARY KEY(attribute_id, option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entity (id INT AUTO_INCREMENT NOT NULL, creation_date DATETIME NOT NULL, update_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entity_meta (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, INDEX IDX_9F3EAD1E81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `index` (id INT AUTO_INCREMENT NOT NULL, model_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_807367017975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE index_column (id INT AUTO_INCREMENT NOT NULL, view_id INT DEFAULT NULL, field_id INT NOT NULL, position INT NOT NULL, INDEX IDX_64CD788F31518C7 (view_id), UNIQUE INDEX UNIQ_64CD788F443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(255) NOT NULL, class VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_item (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, route VARCHAR(255) DEFAULT NULL, INDEX IDX_D754D5507975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(255) NOT NULL, disabled TINYINT(1) DEFAULT NULL, selected TINYINT(1) DEFAULT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attribute ADD CONSTRAINT FK_FA7AEFFB33B92F39 FOREIGN KEY (label_id) REFERENCES label (id)');
        $this->addSql('ALTER TABLE attribute ADD CONSTRAINT FK_FA7AEFFB5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE attribute_option ADD CONSTRAINT FK_78672EEAB6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attribute_option ADD CONSTRAINT FK_78672EEAA7C41D6F FOREIGN KEY (option_id) REFERENCES `option` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entity_meta ADD CONSTRAINT FK_9F3EAD1E81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id)');
        $this->addSql('ALTER TABLE `index` ADD CONSTRAINT FK_807367017975B7E7 FOREIGN KEY (model_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE index_column ADD CONSTRAINT FK_64CD788F31518C7 FOREIGN KEY (view_id) REFERENCES `index` (id)');
        $this->addSql('ALTER TABLE index_column ADD CONSTRAINT FK_64CD788F443707B0 FOREIGN KEY (field_id) REFERENCES attribute (id)');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D5507975B7E7 FOREIGN KEY (model_id) REFERENCES form (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_option DROP FOREIGN KEY FK_78672EEAB6E62EFA');
        $this->addSql('ALTER TABLE index_column DROP FOREIGN KEY FK_64CD788F443707B0');
        $this->addSql('ALTER TABLE entity_meta DROP FOREIGN KEY FK_9F3EAD1E81257D5D');
        $this->addSql('ALTER TABLE attribute DROP FOREIGN KEY FK_FA7AEFFB5FF69B7D');
        $this->addSql('ALTER TABLE `index` DROP FOREIGN KEY FK_807367017975B7E7');
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D5507975B7E7');
        $this->addSql('ALTER TABLE index_column DROP FOREIGN KEY FK_64CD788F31518C7');
        $this->addSql('ALTER TABLE attribute DROP FOREIGN KEY FK_FA7AEFFB33B92F39');
        $this->addSql('ALTER TABLE attribute_option DROP FOREIGN KEY FK_78672EEAA7C41D6F');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('DROP TABLE attribute_option');
        $this->addSql('DROP TABLE entity');
        $this->addSql('DROP TABLE entity_meta');
        $this->addSql('DROP TABLE form');
        $this->addSql('DROP TABLE `index`');
        $this->addSql('DROP TABLE index_column');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE menu_item');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
