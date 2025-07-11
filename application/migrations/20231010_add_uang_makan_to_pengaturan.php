<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_uang_makan_to_pengaturan extends CI_Migration {

    public function up() {
        $fields = [
            'uang_makan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'default' => 20000
            ]
        ];
        $this->dbforge->add_column('pengaturan', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('pengaturan', 'uang_makan');
    }
}