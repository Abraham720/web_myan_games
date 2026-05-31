<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $table = 'reactions';
    protected $fillable = ['post_id', 'user_id', 'type'];
    public $timestamps = false;

    // ⚠️ KONFIGURASI UUID (Supabase Auth)
    public $incrementing = false;
    protected $keyType = 'string';

    // Relasi
    public function post() 
    { 
        return $this->belongsTo(Post::class, 'post_id', 'id'); 
    }
    
    public function user() 
    { 
        return $this->belongsTo(Profile::class, 'user_id', 'id'); 
    }
}