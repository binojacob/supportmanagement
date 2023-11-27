<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231123104733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments_support_case (comments_id INT NOT NULL, support_case_id INT NOT NULL, INDEX IDX_33F8604663379586 (comments_id), INDEX IDX_33F860467225DC09 (support_case_id), PRIMARY KEY(comments_id, support_case_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments_support_case ADD CONSTRAINT FK_33F8604663379586 FOREIGN KEY (comments_id) REFERENCES comments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments_support_case ADD CONSTRAINT FK_33F860467225DC09 FOREIGN KEY (support_case_id) REFERENCES support_case (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments_support_case DROP FOREIGN KEY FK_33F8604663379586');
        $this->addSql('ALTER TABLE comments_support_case DROP FOREIGN KEY FK_33F860467225DC09');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE comments_support_case');
    }
}
