<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220823141136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add relation betwenn publication and Guide user';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publications ADD guide_id INT NOT NULL');
        $this->addSql('ALTER TABLE publications ADD CONSTRAINT FK_32783AF4D7ED1D4B FOREIGN KEY (guide_id) REFERENCES Guides (id)');
        $this->addSql('CREATE INDEX IDX_32783AF4D7ED1D4B ON publications (guide_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publications DROP FOREIGN KEY FK_32783AF4D7ED1D4B');
        $this->addSql('DROP INDEX IDX_32783AF4D7ED1D4B ON publications');
        $this->addSql('ALTER TABLE publications DROP guide_id');
    }
}
