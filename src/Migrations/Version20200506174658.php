<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506174658 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reaction (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, INDEX IDX_A4D707F77E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reaction_workout (reaction_id INT NOT NULL, workout_id INT NOT NULL, INDEX IDX_AA422C13813C7171 (reaction_id), INDEX IDX_AA422C13A6CCCFC9 (workout_id), PRIMARY KEY(reaction_id, workout_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reaction_workout ADD CONSTRAINT FK_AA422C13813C7171 FOREIGN KEY (reaction_id) REFERENCES reaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reaction_workout ADD CONSTRAINT FK_AA422C13A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reaction_workout DROP FOREIGN KEY FK_AA422C13813C7171');
        $this->addSql('DROP TABLE reaction');
        $this->addSql('DROP TABLE reaction_workout');
    }
}
