<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';
    protected $fillable = ['username'];
    
    // ⚠️ KONFIGURASI UUID (Supabase Auth)
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;

    // ⚠️ Cast created_at ke datetime agar bisa pakai diffForHumans()
    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relasi
    public function posts() 
    { 
        return $this->hasMany(Post::class, 'user_id', 'id'); 
    }
    
    public function comments() 
    { 
        return $this->hasMany(Comment::class, 'user_id', 'id'); 
    }
    
    public function news() 
    { 
        return $this->hasMany(News::class, 'user_id', 'id'); 
    }

    // 🔥 Stats Helpers (untuk ditampilkan di view)
    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }
    
    public function getNewsCountAttribute()
    {
        return $this->news()->count();
    }
    
    public function getTotalLikesAttribute()
    {
        // Hitung total like yang diterima dari semua post user ini
        $postIds = $this->posts()->pluck('id');
        return \App\Models\Reaction::whereIn('post_id', $postIds)
                                   ->where('type', 'like')
                                   ->count();
    }
    
    public function getTotalUpvotesAttribute()
    {
        // Hitung total upvote yang diterima dari semua news user ini
        $newsIds = $this->news()->pluck('id');
        return \App\Models\NewsVote::whereIn('news_id', $newsIds)
                                   ->where('type', 'upvote')
                                   ->count();
    }
}