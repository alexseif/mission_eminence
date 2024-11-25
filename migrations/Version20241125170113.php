<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125170113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE testimonial ADD testimonial_name VARCHAR(255) DEFAULT NULL, ADD testimonial_original_name VARCHAR(255) DEFAULT NULL, ADD testimonial_mime_type VARCHAR(255) DEFAULT NULL, DROP image_name, DROP image_original_name, DROP image_mime_type, CHANGE image_size testimonial_size INT DEFAULT NULL, CHANGE image_dimensions testimonial_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE testimonial ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_original_name VARCHAR(255) DEFAULT NULL, ADD image_mime_type VARCHAR(255) DEFAULT NULL, DROP testimonial_name, DROP testimonial_original_name, DROP testimonial_mime_type, CHANGE testimonial_size image_size INT DEFAULT NULL, CHANGE testimonial_dimensions image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }
}
