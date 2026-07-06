<?php

use CodeIgniter\Database\Migration;

class AddPhoneNumberToBrochureTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('brochure_requests', [
            'phone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'email',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('brochure_requests', 'phone_number');
    }
}
