<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611094114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Create the user table
        $this->addSql('CREATE TABLE "user" (
            id INT NOT NULL, 
            email VARCHAR(180) NOT NULL, 
            roles JSON NOT NULL, 
            password VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        // Drop the user table if this migration is rolled back
        $this->addSql('DROP TABLE "user"');
    }
}
