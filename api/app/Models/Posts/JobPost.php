<?php

namespace App\Models\Posts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'order_id',
        'category_id',
        'company_info_id',
        'draft_id',
        'title',
        'seo_title',
        'excerpt',
        'body',
        'image',
        'company_logo',
        'slug',
        'slug_trans',
        'meta_description',
        'meta_keywords',
        'status',                 // ENUM('COMMITTED','UNPAID','UNPAID_TO_BE_PAID_DRAFT','UNPAID_TO_BE_PAID_DRAFT_REMOVED','DRAFT','ARCHIVED','REMOVED')
        'featured',
        'position',
        'company_name',
        'job_description',
        'employer_type',          // ENUM('full-time','part-time','contractor','temporary','internship','per diem','volunteer','onsite')
        'primary_tag',            // ENUM('software development','customer service','sales','marketing','design','frontend','backend','legal','Quality assurance','testing','non-tech','other','JavaScript','React')
        'secondary_tags',
        'skills',
        'budget',
        'currency',
        'salary_min',
        'salary_max',
        'payment_frequency',      // ENUM('milestone','hourly','one-time')
        'location_type',          // ENUM('remote','on-site','hybrid')
        'location_restriction',
        'show_company_logo',
        'highlight_company_with_color',
        'brand_color',
        'email_blast_job',
        'auto_match_applicant',
        'create_qr_code',
        'highlight_post',
        'sticky_note_24_hour',
        'sticky_note_week',
        'sticky_note_month',
        'geo_lock_post',
        'benefits',
        'how_to_apply',
        'apply_url',
        'apply_email_address',
        'company_twitter',
        'company_email',
        'location_country',
        'location_state_province',
        'location_city',
        'location_zip_postal',
        'location_long',
        'location_lat',
        'invoice_email',
        'invoice_address',
        'invoice_notes_po_box_number',
        'feedback_box',
        'pay_later',
        'views',
        'clicks',
        'base_post',
        'free_tier',
        'expires_at', // ✅ added so it can be mass-assigned
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'featured' => 'boolean',
        'show_company_logo' => 'boolean',
        'email_blast_job' => 'boolean',
        'auto_match_applicant' => 'boolean',
        'create_qr_code' => 'boolean',
        'highlight_post' => 'boolean',
        'sticky_note_24_hour' => 'boolean',
        'sticky_note_week' => 'boolean',
        'sticky_note_month' => 'boolean',
        'geo_lock_post' => 'boolean',
        'highlight_company_with_color' => 'boolean',
        'highlight_company' => 'boolean',
        'pay_later' => 'boolean',
        'free_tier' => 'boolean',
        'secondary_tags' => 'array',
        'skills' => 'array',
        'benefits' => 'array',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'views' => 'integer',
        'clicks' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expires_at' => 'datetime', // ✅ cast to Carbon instance
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'DRAFT',
        'featured' => false,
        'show_company_logo' => false,
        'email_blast_job' => false,
        'auto_match_applicant' => false,
        'create_qr_code' => false,
        'highlight_post' => false,
        'sticky_note_24_hour' => false,
        'sticky_note_week' => false,
        'sticky_note_month' => false,
        'geo_lock_post' => false,
        'highlight_company_with_color' => false,
        'pay_later' => false,
    ];

    /**
     * Relationships
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        // return $this->belongsTo(Category::class, 'category_id');
    }
}
