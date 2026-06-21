<?php

use CodeIgniter\Database\Migration;

class CreateCohortsTable extends Migration
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
            'cohort' => [
                'type'       => 'INT',
                'constraint' => 5,
                'comment'    => 'Cohort number, e.g. 1, 2, 3',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'comment'    => 'Internal reference, e.g. ogun-1. Not used in the public URL.',
            ],
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Used in the public URL, e.g. /apply/ogun',
            ],
            'logo_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'primary_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'brochure_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'open', 'closed'],
                'default'    => 'draft',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('state');
        $this->forge->createTable('cohorts');
    }

    public function down()
    {
        $this->forge->dropTable('cohorts');
    }
}
