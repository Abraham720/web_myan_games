<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $table = 'comments';
    protected $fillable = ['post_id', 'user_id', 'content'];
    protected $dates = ['deleted_at'];

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