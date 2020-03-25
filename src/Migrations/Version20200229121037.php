<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200229121037 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX IDX_2DA17977E7927C74 ON user');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_2da17977e7927c74 TO UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_2da17977d1497579 TO UNIQ_8D93D649D1497579');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE FULLTEXT INDEX IDX_2DA17977E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649d1497579 TO UNIQ_2DA17977D1497579');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_2DA17977E7927C74');
    }
}
