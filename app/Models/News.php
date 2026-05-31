<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    protected $table = 'news';
    
    protected $fillable = [
        'user_id',
        'game_title',
        'caption',
        'image_url',
        'upvote_count',
        'downvote_count',
        'score'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'upvote_count' => 'integer',
        'downvote_count' => 'integer',
        'score' => 'integer',
    ];

    // 🔑 FIX: Nonaktifkan updated_at otomatis Laravel
    public $timestamps = false; 

    // ⚠️ UUID CONFIG
    public $incrementing = false;
    protected $keyType = 'string';

    public function user() { return $this->belongsTo(Profile::class, 'user_id', 'id'); }
    public function votes() { return $this->hasMany(NewsVote::class, 'news_id', 'id'); }

    public function updateScoreCounters() {
        $upvotes = $this->votes()->where('type', 'upvote')->count();
        $downvotes = $this->votes()->where('type', 'downvote')->count();
        $this->update([
            'upvote_count' => $upvotes,
            'downvote_count' => $downvotes,
            'score' => $upvotes - $downvotes
        ]);
    }
}