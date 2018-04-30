<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180430172602 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders ADD shipping_address_id INT NOT NULL, ADD billing_address_id INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE4D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES user_addresses (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE79D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES user_addresses (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE4D4CFF2B ON orders (shipping_address_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE79D0C0E4 ON orders (billing_address_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE4D4CFF2B');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE79D0C0E4');
        $this->addSql('DROP INDEX IDX_E52FFDEE4D4CFF2B ON orders');
        $this->addSql('DROP INDEX IDX_E52FFDEE79D0C0E4 ON orders');
        $this->addSql('ALTER TABLE orders DROP shipping_address_id, DROP billing_address_id');
    }
}
