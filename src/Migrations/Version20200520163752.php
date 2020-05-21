<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520163752 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reaction (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, workout_id INT NOT NULL, type INT NOT NULL, INDEX IDX_A4D707F77E3C61F9 (owner_id), INDEX IDX_A4D707F7A6CCCFC9 (workout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friend (id INT AUTO_INCREMENT NOT NULL, inviter_id INT NOT NULL, invitee_id INT NOT NULL, status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_55EEAC61B79F4F04 (inviter_id), INDEX IDX_55EEAC617A512022 (invitee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abstract_activity (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, energy INT NOT NULL, discriminator VARCHAR(255) NOT NULL, FULLTEXT INDEX IDX_5CD92D785E237E068CDE5729 (name, type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weight_activity (id INT NOT NULL, repetitions_avg_min INT NOT NULL, repetitions_avg_max INT NOT NULL, weight_avg_min DOUBLE PRECISION NOT NULL, weight_avg_max DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, route_data_id INT DEFAULT NULL, burnout_energy_total INT NOT NULL, start_at DATETIME NOT NULL, duration_seconds_total INT NOT NULL, distance_total DOUBLE PRECISION DEFAULT NULL, repetitions_total INT DEFAULT NULL, dumbbell_weight DOUBLE PRECISION DEFAULT NULL, image_filename VARCHAR(255) DEFAULT NULL, INDEX IDX_649FFB72A76ED395 (user_id), INDEX IDX_649FFB7281C06096 (activity_id), UNIQUE INDEX UNIQ_649FFB72CFA6FCA4 (route_data_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_token (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(188) NOT NULL, expired_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BEAB6C245F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attachment (id INT AUTO_INCREMENT NOT NULL, curiosity_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_795FD9BBD1C57774 (curiosity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE curiosity (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(40) NOT NULL, slug VARCHAR(100) NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, main_image_filename VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_743FC380989D9B62 (slug), INDEX IDX_743FC380F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movement_set (id INT AUTO_INCREMENT NOT NULL, workout_id INT NOT NULL, activity_id INT NOT NULL, distance DOUBLE PRECISION NOT NULL, duration_seconds INT NOT NULL, burnout_energy INT NOT NULL, INDEX IDX_C550E574A6CCCFC9 (workout_id), INDEX IDX_C550E57481C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, password_token_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, login VARCHAR(255) NOT NULL, roles JSON NOT NULL, first_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, second_name VARCHAR(255) NOT NULL, agreed_terms_at DATETIME NOT NULL, image_filename VARCHAR(255) DEFAULT NULL, failed_attempts INT NOT NULL, birthdate DATE DEFAULT NULL, gender VARCHAR(25) NOT NULL, weight INT DEFAULT NULL, height INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649AA08CB10 (login), UNIQUE INDEX UNIQ_8D93D649D1497579 (password_token_id), FULLTEXT INDEX IDX_8D93D649A9D1C132DA64C6A8 (first_name, second_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bodyweight_activity (id INT NOT NULL, repetitions_avg_min INT NOT NULL, repetitions_avg_max INT NOT NULL, intensity VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, activity_type VARCHAR(255) NOT NULL, activity_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge_user (challenge_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_843CD1CF98A21AC6 (challenge_id), INDEX IDX_843CD1CFA76ED395 (user_id), PRIMARY KEY(challenge_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movement_set_activity (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movement_activity (id INT NOT NULL, speed_average_min DOUBLE PRECISION NOT NULL, speed_average_max DOUBLE PRECISION NOT NULL, intensity VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route_data (id INT AUTO_INCREMENT NOT NULL, altitude_max DOUBLE PRECISION DEFAULT NULL, altitude_min DOUBLE PRECISION DEFAULT NULL, temperature INT DEFAULT NULL, weather_conditions VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id)');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC61B79F4F04 FOREIGN KEY (inviter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC617A512022 FOREIGN KEY (invitee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE weight_activity ADD CONSTRAINT FK_E654682FBF396750 FOREIGN KEY (id) REFERENCES abstract_activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workout ADD CONSTRAINT FK_649FFB72A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE workout ADD CONSTRAINT FK_649FFB7281C06096 FOREIGN KEY (activity_id) REFERENCES abstract_activity (id)');
        $this->addSql('ALTER TABLE workout ADD CONSTRAINT FK_649FFB72CFA6FCA4 FOREIGN KEY (route_data_id) REFERENCES route_data (id)');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BBD1C57774 FOREIGN KEY (curiosity_id) REFERENCES curiosity (id)');
        $this->addSql('ALTER TABLE curiosity ADD CONSTRAINT FK_743FC380F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE movement_set ADD CONSTRAINT FK_C550E574A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id)');
        $this->addSql('ALTER TABLE movement_set ADD CONSTRAINT FK_C550E57481C06096 FOREIGN KEY (activity_id) REFERENCES abstract_activity (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D1497579 FOREIGN KEY (password_token_id) REFERENCES password_token (id)');
        $this->addSql('ALTER TABLE bodyweight_activity ADD CONSTRAINT FK_E7F39F54BF396750 FOREIGN KEY (id) REFERENCES abstract_activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE challenge_user ADD CONSTRAINT FK_843CD1CF98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE challenge_user ADD CONSTRAINT FK_843CD1CFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movement_set_activity ADD CONSTRAINT FK_863B9356BF396750 FOREIGN KEY (id) REFERENCES abstract_activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movement_activity ADD CONSTRAINT FK_53546176BF396750 FOREIGN KEY (id) REFERENCES abstract_activity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE weight_activity DROP FOREIGN KEY FK_E654682FBF396750');
        $this->addSql('ALTER TABLE workout DROP FOREIGN KEY FK_649FFB7281C06096');
        $this->addSql('ALTER TABLE movement_set DROP FOREIGN KEY FK_C550E57481C06096');
        $this->addSql('ALTER TABLE bodyweight_activity DROP FOREIGN KEY FK_E7F39F54BF396750');
        $this->addSql('ALTER TABLE movement_set_activity DROP FOREIGN KEY FK_863B9356BF396750');
        $this->addSql('ALTER TABLE movement_activity DROP FOREIGN KEY FK_53546176BF396750');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F7A6CCCFC9');
        $this->addSql('ALTER TABLE movement_set DROP FOREIGN KEY FK_C550E574A6CCCFC9');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D1497579');
        $this->addSql('ALTER TABLE attachment DROP FOREIGN KEY FK_795FD9BBD1C57774');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F77E3C61F9');
        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC61B79F4F04');
        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC617A512022');
        $this->addSql('ALTER TABLE workout DROP FOREIGN KEY FK_649FFB72A76ED395');
        $this->addSql('ALTER TABLE curiosity DROP FOREIGN KEY FK_743FC380F675F31B');
        $this->addSql('ALTER TABLE challenge_user DROP FOREIGN KEY FK_843CD1CFA76ED395');
        $this->addSql('ALTER TABLE challenge_user DROP FOREIGN KEY FK_843CD1CF98A21AC6');
        $this->addSql('ALTER TABLE workout DROP FOREIGN KEY FK_649FFB72CFA6FCA4');
        $this->addSql('DROP TABLE reaction');
        $this->addSql('DROP TABLE friend');
        $this->addSql('DROP TABLE abstract_activity');
        $this->addSql('DROP TABLE weight_activity');
        $this->addSql('DROP TABLE workout');
        $this->addSql('DROP TABLE password_token');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('DROP TABLE curiosity');
        $this->addSql('DROP TABLE movement_set');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE bodyweight_activity');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE challenge_user');
        $this->addSql('DROP TABLE movement_set_activity');
        $this->addSql('DROP TABLE movement_activity');
        $this->addSql('DROP TABLE route_data');
    }
}
