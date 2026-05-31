<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsVote extends Model
{
    protected $table = 'news_votes';
    protected $fillable = ['news_id', 'user_id', 'type'];
    public $timestamps = false;

    // ⚠️ KONFIGURASI UUID (Supabase Auth)
    public $incrementing = false;
    protected $keyType = 'string';

    // Relasi
    public function news() 
    { 
        return $this->belongsTo(News::class, 'news_id', 'id'); 
    }
    
    public function user() 
    { 
        return $this->belongsTo(Profile::class, 'user_id', 'id'); 
    }
}