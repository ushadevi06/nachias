<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('debit_note_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debit_note_id')->constrained('debit_notes');
            $table->foreignId('charge_id')->constrained('charges');
            $table->string('charge_name')->nullable();
            $table->decimal('charge_amount', 15, 2)->default(0);
            $table->string('tax_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_note_charges');
    }
};
