<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FileArticle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'files_articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_id',
        'table_reference_name',
        'file_store_an_id',
        'filename',
        'foldername',
        'status',
        'verify_status',
        'type',
        'file_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reference_id' => 'integer',
        'file_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the owning model (polymorphic relation).
     * 
     * This allows the file to belong to any model (Article, Post, etc.)
     */
    public function referenceable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'table_reference_name', 'reference_id');
    }

    /**
     * Scope a query to only include files with a specific status.
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
     * Scope a query to only include verified files.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('verify_status', 'verified');
    }

    /**
     * Scope a query to only include files with a specific type.
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
     * Scope a query to order files by their order field.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query, string $direction = 'asc')
    {
        return $query->orderBy('file_order', $direction);
    }

    /**
     * Scope a query to get files for a specific reference.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $referenceId
     * @param string|null $tableName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForReference($query, int $referenceId, ?string $tableName = null)
    {
        $query->where('reference_id', $referenceId);
        
        if ($tableName) {
            $query->where('table_reference_name', $tableName);
        }
        
        return $query;
    }

    /**
     * Get the full file path.
     *
     * @return string|null
     */
    public function getFullPathAttribute(): ?string
    {
        if (!$this->foldername || !$this->filename) {
            return null;
        }
        
        return $this->foldername . '/' . $this->filename;
    }

    /**
     * Get the file URL.
     *
     * @return string|null
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->full_path) {
            return null;
        }
        
        return asset('storage/' . $this->full_path);
    }

    /**
     * Check if the file is verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verify_status === 'verified';
    }

    /**
     * Mark the file as verified.
     *
     * @return bool
     */
    public function markAsVerified(): bool
    {
        return $this->update(['verify_status' => 'verified']);
    }

    /**
     * Mark the file as unverified.
     *
     * @return bool
     */
    public function markAsUnverified(): bool
    {
        return $this->update(['verify_status' => 'unverified']);
    }
}
