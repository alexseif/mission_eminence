<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125170826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE testimonial ADD testimonial VARCHAR(255) NOT NULL, DROP testimonial_size, DROP testimonial_dimensions, DROP testimonial_name, DROP testimonial_original_name, DROP testimonial_mime_type');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE testimonial ADD testimonial_size INT DEFAULT NULL, ADD testimonial_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD testimonial_name VARCHAR(255) DEFAULT NULL, ADD testimonial_original_name VARCHAR(255) DEFAULT NULL, ADD testimonial_mime_type VARCHAR(255) DEFAULT NULL, DROP testimonial');
    }
}
