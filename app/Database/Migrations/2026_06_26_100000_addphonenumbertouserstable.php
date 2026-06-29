<?php

use CodeIgniter\Database\Migration;

class AddPhoneNumberToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'phone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true, // nullable so existing rows don't break
                'after'      => 'hear_about_us',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'phone_number');
    }
}
