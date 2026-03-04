<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_info';

    /**
     * Automatically append logo_url to model's array/JSON output.
     */
    protected $appends = ['logo_url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'company_description',
        'company_website',
        'company_social_1',
        'company_social_2',
        'company_social_3',
        'social_clicks_1',
        'social_clicks_2',
        'social_clicks_3',
        'search_click',
        'status',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all job posts for this company.
     */
    public function jobPosts()
    {
        return $this->hasMany(JobPost::class, 'company_name', 'company_name');
    }

    /**
     * Accessor for logo_url attribute (for use in API/JSON, etc).
     * Usage: $company->logo_url
     */
    public function getLogoUrlAttribute()
    {
        return self::findCompanyLogo($this->id);
    }

    

    /**
     * Find the company logo by company id or name.
     * Returns the logo URL or null.
     *
     * @param int|string $companyIdOrName
     * @return string|null
    */
    public static function findCompanyLogo($companyIdOrName)
    {
        // Try to find by id or name
        $company = is_numeric($companyIdOrName)
            ? self::find($companyIdOrName)
            : self::where('company_name', $companyIdOrName)->first();

        if (!$company) {
            return null;
        }

        // Try to find a job post with a logo for this company
        $jobWithLogo = 
            \App\Models\Posts\JobPost::where('company_name', $company->company_name)
                ->where('status', 'COMMITTED')
                ->first();

        if ($jobWithLogo) {
            // Check for uploaded image using FilePostStored model
            $imageFile = \App\Models\Core\FilePostStored::where('post_id', $jobWithLogo->id)
                ->where('type', 'job_post')
                ->where('status', 'active')
                ->first();

            if ($imageFile) {
                return asset('storage/store_data/posts/draft/' . $imageFile->foldername . '/' . $imageFile->filename);
            } elseif ($jobWithLogo->company_logo) {
                // Fallback to company_logo field if exists
                return $jobWithLogo->company_logo;
            }
        }
        return null;
    }
}
