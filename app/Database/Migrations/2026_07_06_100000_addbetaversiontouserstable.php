<?php

use CodeIgniter\Database\Migration;

class AddBetaVersionToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'version' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'phone_number',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'version');
    }
}
