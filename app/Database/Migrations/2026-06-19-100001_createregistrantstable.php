<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegistrantsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cohort_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['registration', 'brochure'],
                'default'    => 'registration',
            ],
            // shared field across both forms
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            // fields below only filled for type = registration
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'job_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed'],
                'null'       => true,
            ],
            'payment_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('cohort_id');
        $this->forge->addForeignKey('cohort_id', 'cohorts', 'id', '', 'CASCADE');
        $this->forge->createTable('registrants');
    }

    public function down()
    {
        $this->forge->dropTable('registrants');
    }
}
