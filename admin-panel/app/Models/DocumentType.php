<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'document_types';
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'title',
        'is_enabled'
    ];
    
    protected $casts = [
        'is_enabled' => 'boolean',
    ];
}
