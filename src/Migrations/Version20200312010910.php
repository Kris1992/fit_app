<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312010910 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE movement_set (id INT AUTO_INCREMENT NOT NULL, workout_id INT NOT NULL, activity_id INT NOT NULL, distance DOUBLE PRECISION NOT NULL, duration_seconds INT NOT NULL, burnout_energy INT NOT NULL, INDEX IDX_C550E574A6CCCFC9 (workout_id), UNIQUE INDEX UNIQ_C550E57481C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movement_set ADD CONSTRAINT FK_C550E574A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id)');
        $this->addSql('ALTER TABLE movement_set ADD CONSTRAINT FK_C550E57481C06096 FOREIGN KEY (activity_id) REFERENCES abstract_activity (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE movement_set');
    }
}
