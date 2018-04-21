<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180421233823 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE address');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A36799605');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A36799605 FOREIGN KEY (productId) REFERENCES products (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, address1 VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, address2 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, city VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, post_code INT NOT NULL, phone INT DEFAULT NULL, country VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_D4E6F81A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A36799605');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A36799605 FOREIGN KEY (productId) REFERENCES products (id)');
    }
}
