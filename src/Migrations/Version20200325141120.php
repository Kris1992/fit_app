<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200325141120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE workout_like_user DROP FOREIGN KEY FK_763C6D63F95D969A');
        $this->addSql('ALTER TABLE workout_like_workout DROP FOREIGN KEY FK_101DF1B5F95D969A');
        $this->addSql('DROP TABLE workout_like');
        $this->addSql('DROP TABLE workout_like_user');
        $this->addSql('DROP TABLE workout_like_workout');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE workout_like (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE workout_like_user (workout_like_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_763C6D63F95D969A (workout_like_id), INDEX IDX_763C6D63A76ED395 (user_id), PRIMARY KEY(workout_like_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE workout_like_workout (workout_like_id INT NOT NULL, workout_id INT NOT NULL, INDEX IDX_101DF1B5F95D969A (workout_like_id), INDEX IDX_101DF1B5A6CCCFC9 (workout_id), PRIMARY KEY(workout_like_id, workout_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE workout_like_user ADD CONSTRAINT FK_763C6D63A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workout_like_user ADD CONSTRAINT FK_763C6D63F95D969A FOREIGN KEY (workout_like_id) REFERENCES workout_like (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workout_like_workout ADD CONSTRAINT FK_101DF1B5A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workout_like_workout ADD CONSTRAINT FK_101DF1B5F95D969A FOREIGN KEY (workout_like_id) REFERENCES workout_like (id) ON DELETE CASCADE');
    }
}
