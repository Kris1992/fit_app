<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200316181409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movement_set DROP FOREIGN KEY FK_C550E57481C06096');
        $this->addSql('DROP INDEX UNIQ_C550E57481C06096 ON movement_set');
        $this->addSql('ALTER TABLE movement_set DROP activity_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movement_set ADD activity_id INT NOT NULL');
        $this->addSql('ALTER TABLE movement_set ADD CONSTRAINT FK_C550E57481C06096 FOREIGN KEY (activity_id) REFERENCES abstract_activity (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C550E57481C06096 ON movement_set (activity_id)');
    }
}
