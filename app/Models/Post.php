<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';
    protected $fillable = ['user_id', 'game_title', 'image_url', 'caption'];
    protected $dates = ['deleted_at'];
    public $timestamps = true; // uses created_at & updated_at

    // ⚠️ KONFIGURASI UUID (Supabase Auth)
    public $incrementing = false;      // ID bukan auto-increment
    protected $keyType = 'string';     // ID bertipe string (UUID)

    // Relasi - foreign key disebutkan eksplisit
    public function user() 
    { 
        return $this->belongsTo(Profile::class, 'user_id', 'id'); 
    }
    
    public function comments() 
    { 
        return $this->hasMany(Comment::class, 'post_id', 'id'); 
    }
    
    public function reactions() 
    { 
        return $this->hasMany(Reaction::class, 'post_id', 'id'); 
    }
}