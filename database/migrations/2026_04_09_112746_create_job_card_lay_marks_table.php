<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_card_lay_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_fabric_detail_id')->constrained('job_card_fabric_details')->onDelete('cascade');
            $table->integer('mark_no')->default(1);
            $table->longText('sizes')->nullable(); // JSON array of sizes
            $table->string('sleeve_type', 10)->nullable(); // F/S or H/S
            $table->decimal('lay_mark_meter', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void 
     */
    public function down()
    {
        Schema::dropIfExists('job_card_lay_marks');
    }
};
