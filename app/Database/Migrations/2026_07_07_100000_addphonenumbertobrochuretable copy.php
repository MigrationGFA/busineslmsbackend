<?php

use CodeIgniter\Database\Migration;

class AddFullNameToBrochureTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('brochure_requests', [
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'phone_number',
            ],
        ]);
    }

    
    public function down()
    {
        $this->forge->dropColumn('brochure_requests', 'full_name');
    }
}
