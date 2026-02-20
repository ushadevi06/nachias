<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('backup_no')->unique();
            $table->string('filename');
            $table->enum('backup_type', ['Full', 'Database Only', 'Files Only'])->default('Database Only');
            $table->string('file_size')->nullable();
            $table->string('location')->default('Local');
            $table->enum('status', ['Pending', 'Running', 'Success', 'Failed'])->default('Pending');
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
