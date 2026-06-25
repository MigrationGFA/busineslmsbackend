<?php

use CodeIgniter\Database\Migration;

class AddHearAboutUsToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'hear_about_us' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true, // nullable so existing rows don't break
                'after'      => 'job_title',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'hear_about_us');
    }
}