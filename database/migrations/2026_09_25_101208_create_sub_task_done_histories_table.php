<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private $_pre_col = 'sdh_';

    public function up(): void {
        Schema::create('sub_task_done_histories', function (Blueprint $table) {
            $table->unsignedBigInteger($this->_pre_col . 'pst_id')->nullable()->comment('Project Sub Task Id comes from project_sub_tasks table.');
            $table->foreign($this->_pre_col . 'pst_id')->references('pst_id')->on('project_sub_tasks')->nullOnDelete();

            $table->unsignedBigInteger($this->_pre_col . 'done_by');
            $table->foreign($this->_pre_col . 'done_by', $this->_pre_col . 'done_by_foreign')->references('adm_id')->on('admin_users');

            $table->boolean($this->_pre_col . 'is_checked');
            get_created_updated_by_db_column($table, $this->_pre_col, true);
        });
    }

    public function down(): void {
        Schema::dropIfExists('sub_task_done_histories');
    }
};
