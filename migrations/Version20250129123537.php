<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129123537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_course DROP FOREIGN KEY FK_73CC7484A76ED395');
        $this->addSql('ALTER TABLE user_course DROP FOREIGN KEY FK_73CC7484591CC992');
        $this->addSql('DROP INDEX IDX_73CC7484A76ED395 ON user_course');
        $this->addSql('ALTER TABLE user_course ADD id INT AUTO_INCREMENT NOT NULL, ADD completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD certificate_path VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, CHANGE user_id student_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user_course ADD CONSTRAINT FK_73CC7484CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_course ADD CONSTRAINT FK_73CC7484591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_73CC7484CB944F1A ON user_course (student_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_course MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user_course DROP FOREIGN KEY FK_73CC7484CB944F1A');
        $this->addSql('ALTER TABLE user_course DROP FOREIGN KEY FK_73CC7484591CC992');
        $this->addSql('DROP INDEX IDX_73CC7484CB944F1A ON user_course');
        $this->addSql('DROP INDEX `PRIMARY` ON user_course');
        $this->addSql('ALTER TABLE user_course DROP id, DROP completed_at, DROP certificate_path, DROP created_at, DROP updated_at, CHANGE student_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_course ADD CONSTRAINT FK_73CC7484A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_course ADD CONSTRAINT FK_73CC7484591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_73CC7484A76ED395 ON user_course (user_id)');
        $this->addSql('ALTER TABLE user_course ADD PRIMARY KEY (user_id, course_id)');
    }
}
