<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226003101 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE abstract_activity (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, energy INT NOT NULL, discriminator VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE weight_activity DROP type, DROP name, DROP energy, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE weight_activity ADD CONSTRAINT FK_E654682FBF396750 FOREIGN KEY (id) REFERENCES abstract_activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movement_activity DROP type, DROP name, DROP energy, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE movement_activity ADD CONSTRAINT FK_53546176BF396750 FOREIGN KEY (id) REFERENCES abstract_activity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE weight_activity DROP FOREIGN KEY FK_E654682FBF396750');
        $this->addSql('ALTER TABLE movement_activity DROP FOREIGN KEY FK_53546176BF396750');
        $this->addSql('DROP TABLE abstract_activity');
        $this->addSql('ALTER TABLE movement_activity ADD type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD energy INT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE weight_activity ADD type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD energy INT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
