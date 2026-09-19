<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErpRagKnowledgeChunk extends Model
{
    protected $table = 'erp_rag_knowledge_chunks';

    protected $fillable = [
        'source_type',
        'module',
        'screen',
        'title',
        'keywords',
        'url',
        'menu_path',
        'content',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
