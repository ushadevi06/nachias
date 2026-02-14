<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServicesColumnInTaskAssignEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_assign_employees', function (Blueprint $table) {
            $table->dropColumn('services');
            $table->unsignedBigInteger('service_id')->nullable()->after('issued_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_assign_employees', function (Blueprint $table) {
            $table->dropColumn('service_id');
            $table->text('services')->nullable()->after('issued_to');
        });
    }
}
