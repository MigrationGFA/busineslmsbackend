<?php

use CodeIgniter\Database\Migration;

class CreateBrochureRequestsTable extends Migration
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
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('cohort_id');
        $this->forge->addForeignKey('cohort_id', 'cohorts', 'id', '', 'CASCADE');
        $this->forge->createTable('brochure_requests');
    }

    public function down()
    {
        $this->forge->dropTable('brochure_requests');
    }
}
