<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506191734 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE reaction_workout');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reaction_workout (reaction_id INT NOT NULL, workout_id INT NOT NULL, INDEX IDX_AA422C13813C7171 (reaction_id), INDEX IDX_AA422C13A6CCCFC9 (workout_id), PRIMARY KEY(reaction_id, workout_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reaction_workout ADD CONSTRAINT FK_AA422C13813C7171 FOREIGN KEY (reaction_id) REFERENCES reaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reaction_workout ADD CONSTRAINT FK_AA422C13A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id) ON DELETE CASCADE');
    }
}
