<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230122163935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id BIGSERIAL NOT NULL, name VARCHAR(250) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX category_name_idx ON category (name)');
        $this->addSql('CREATE TABLE product (id BIGSERIAL NOT NULL, category_id BIGINT DEFAULT NULL, name VARCHAR(250) NOT NULL, description VARCHAR(1000) NOT NULL, weight BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX product_category_id_idx ON product (category_id)');
        $this->addSql('CREATE INDEX product_name_idx ON product (name)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT product_to_category_fk FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP CONSTRAINT product_to_category_fk');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE product');
    }
}
