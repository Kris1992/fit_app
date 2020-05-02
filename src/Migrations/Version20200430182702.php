<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200430182702 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE route_data (id INT AUTO_INCREMENT NOT NULL, altitude_max DOUBLE PRECISION DEFAULT NULL, altitude_min DOUBLE PRECISION DEFAULT NULL, temperature INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE workout ADD route_data_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE workout ADD CONSTRAINT FK_649FFB72CFA6FCA4 FOREIGN KEY (route_data_id) REFERENCES route_data (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_649FFB72CFA6FCA4 ON workout (route_data_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE workout DROP FOREIGN KEY FK_649FFB72CFA6FCA4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0E3BD61CE (available_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE route_data');
        $this->addSql('DROP INDEX UNIQ_649FFB72CFA6FCA4 ON workout');
        $this->addSql('ALTER TABLE workout DROP route_data_id');
    }
}
