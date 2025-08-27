<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250827142512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE story_like (id INT AUTO_INCREMENT NOT NULL, story_id INT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3ACE2C9DAA5D4036 (story_id), INDEX IDX_3ACE2C9DA76ED395 (user_id), UNIQUE INDEX UNIQ_STORY_USER (story_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE story_like ADD CONSTRAINT FK_3ACE2C9DAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id)');
        $this->addSql('ALTER TABLE story_like ADD CONSTRAINT FK_3ACE2C9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE story_like DROP FOREIGN KEY FK_3ACE2C9DAA5D4036');
        $this->addSql('ALTER TABLE story_like DROP FOREIGN KEY FK_3ACE2C9DA76ED395');
        $this->addSql('DROP TABLE story_like');
    }
}
