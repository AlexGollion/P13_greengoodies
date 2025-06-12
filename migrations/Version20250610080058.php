<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610080058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE order_product_product DROP FOREIGN KEY FK_C99ADC414584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_product_product DROP FOREIGN KEY FK_C99ADC41F65E9B0F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_product_product
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_product ADD product_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6DE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2530ADE6DE18E50B ON order_product (product_id_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE order_product_product (order_product_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_C99ADC41F65E9B0F (order_product_id), INDEX IDX_C99ADC414584665A (product_id), PRIMARY KEY(order_product_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_product_product ADD CONSTRAINT FK_C99ADC414584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_product_product ADD CONSTRAINT FK_C99ADC41F65E9B0F FOREIGN KEY (order_product_id) REFERENCES order_product (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE6DE18E50B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2530ADE6DE18E50B ON order_product
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_product DROP product_id_id
        SQL);
    }
}
