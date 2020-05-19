<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200518202350 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE reaction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, workout_id INTEGER NOT NULL, type INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_A4D707F77E3C61F9 ON reaction (owner_id)');
        $this->addSql('CREATE INDEX IDX_A4D707F7A6CCCFC9 ON reaction (workout_id)');
        $this->addSql('CREATE TABLE friend (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, inviter_id INTEGER NOT NULL, invitee_id INTEGER NOT NULL, status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_55EEAC61B79F4F04 ON friend (inviter_id)');
        $this->addSql('CREATE INDEX IDX_55EEAC617A512022 ON friend (invitee_id)');
        $this->addSql('CREATE TABLE abstract_activity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, energy INTEGER NOT NULL, discriminator VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_5CD92D785E237E068CDE5729 ON abstract_activity (name, type)');
        $this->addSql('CREATE TABLE weight_activity (id INTEGER NOT NULL, repetitions_avg_min INTEGER NOT NULL, repetitions_avg_max INTEGER NOT NULL, weight_avg_min DOUBLE PRECISION NOT NULL, weight_avg_max DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE workout (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, activity_id INTEGER NOT NULL, route_data_id INTEGER DEFAULT NULL, burnout_energy_total INTEGER NOT NULL, start_at DATETIME NOT NULL, duration_seconds_total INTEGER NOT NULL, distance_total DOUBLE PRECISION DEFAULT NULL, repetitions_total INTEGER DEFAULT NULL, dumbbell_weight DOUBLE PRECISION DEFAULT NULL, image_filename VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_649FFB72A76ED395 ON workout (user_id)');
        $this->addSql('CREATE INDEX IDX_649FFB7281C06096 ON workout (activity_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_649FFB72CFA6FCA4 ON workout (route_data_id)');
        $this->addSql('CREATE TABLE password_token (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, token VARCHAR(188) NOT NULL, expired_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BEAB6C245F37A13B ON password_token (token)');
        $this->addSql('CREATE TABLE attachment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, curiosity_id INTEGER DEFAULT NULL, filename VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_795FD9BBD1C57774 ON attachment (curiosity_id)');
        $this->addSql('CREATE TABLE curiosity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(40) NOT NULL, slug VARCHAR(100) NOT NULL, content CLOB NOT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, main_image_filename VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_743FC380989D9B62 ON curiosity (slug)');
        $this->addSql('CREATE INDEX IDX_743FC380F675F31B ON curiosity (author_id)');
        $this->addSql('CREATE TABLE movement_set (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, workout_id INTEGER NOT NULL, activity_id INTEGER NOT NULL, distance DOUBLE PRECISION NOT NULL, duration_seconds INTEGER NOT NULL, burnout_energy INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_C550E574A6CCCFC9 ON movement_set (workout_id)');
        $this->addSql('CREATE INDEX IDX_C550E57481C06096 ON movement_set (activity_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, password_token_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, login VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , first_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, second_name VARCHAR(255) NOT NULL, agreed_terms_at DATETIME NOT NULL, image_filename VARCHAR(255) DEFAULT NULL, failed_attempts INTEGER NOT NULL, birthdate DATE DEFAULT NULL, gender VARCHAR(25) NOT NULL, weight INTEGER DEFAULT NULL, height INTEGER DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649AA08CB10 ON user (login)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D1497579 ON user (password_token_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A9D1C132DA64C6A8 ON user (first_name, second_name)');
        $this->addSql('CREATE TABLE bodyweight_activity (id INTEGER NOT NULL, repetitions_avg_min INTEGER NOT NULL, repetitions_avg_max INTEGER NOT NULL, intensity VARCHAR(25) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE movement_set_activity (id INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE movement_activity (id INTEGER NOT NULL, speed_average_min DOUBLE PRECISION NOT NULL, speed_average_max DOUBLE PRECISION NOT NULL, intensity VARCHAR(25) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE route_data (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, altitude_max DOUBLE PRECISION DEFAULT NULL, altitude_min DOUBLE PRECISION DEFAULT NULL, temperature INTEGER DEFAULT NULL, weather_conditions VARCHAR(255) DEFAULT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

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
        $this->addSql('DROP TABLE movement_set_activity');
        $this->addSql('DROP TABLE movement_activity');
        $this->addSql('DROP TABLE route_data');
    }
}
