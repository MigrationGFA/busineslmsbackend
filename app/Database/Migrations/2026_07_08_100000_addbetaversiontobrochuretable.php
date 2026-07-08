<?php

use CodeIgniter\Database\Migration;

class AddBetaVersionToBrochureTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('brochure_requests', [
            'version' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'full_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('brochure_requests', 'version');
    }
}
