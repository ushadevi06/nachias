<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('erp_rag_knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 50)->index(); // 'menu', 'route', 'documentation', 'sql_schema'
            $table->string('module', 100)->index();      // 'Production', 'Sales', 'Purchase', etc.
            $table->string('screen', 150)->nullable()->index();
            $table->string('title', 255);
            $table->text('keywords')->nullable();
            $table->string('url', 255)->nullable();
            $table->string('menu_path', 255)->nullable();
            $table->mediumText('content');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // Add MySQL Fulltext index for lightning-fast BM25 / natural language search
        DB::statement('ALTER TABLE erp_rag_knowledge_chunks ADD FULLTEXT erp_rag_search_index (title, keywords, content)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_rag_knowledge_chunks');
    }
};
