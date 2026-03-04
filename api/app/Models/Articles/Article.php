<?php

namespace App\Models\Articles;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'file_store_an_id',
        'subject',
        'description',
        'link',
        'status',
        'type',
        'views',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'views' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all files associated with this article using FilePostStored.
     */
    public function files()
    {
        return $this->hasMany(\App\Models\Core\FilePostStored::class, 'post_id')
                    ->where('type', 'article')
                    ->orderBy('order', 'asc');
    }

    /**
     * Get files from new FileArticle system (Chrome extension imports).
     */
    public function articleFiles()
    {
        return $this->hasMany(FileArticle::class, 'reference_id')
                    ->where('table_reference_name', 'articles')
                    ->orderBy('file_order', 'asc');
    }

    /**
     * Get verified files only.
     */
    public function verifiedFiles()
    {
        return $this->hasMany(\App\Models\Core\FilePostStored::class, 'post_id')
                    ->where('type', 'article')
                    ->where('verify', 'verified')
                    ->orderBy('order', 'asc');
    }

    /**
     * Increment the view count for the article.
     *
     * @return bool
     */
    public function incrementViews(): bool
    {
        return $this->increment('views');
    }

    /**
     * Scope a query to only include articles with a specific status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include articles with a specific type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to order articles by most viewed.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    /**
     * Scope a query to order articles by most recent.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
