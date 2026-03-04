<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryEstimate extends Model
{
    protected $fillable = [
        'job_title',
        'location',
        'normalized_title',
        'min_salary',
        'max_salary',
        'currency',
        'source',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
    ];

    /**
     * Normalize job title for better matching
     */
    public static function normalizeTitle($title)
    {
        $normalized = strtolower(trim($title));
        
        // Remove common prefixes/suffixes
        $patterns = [
            '/^(senior|junior|lead|staff|principal|mid-level|entry-level|associate)\s+/i',
            '/\s+(i|ii|iii|iv|v|1|2|3|4|5)$/i',
        ];
        
        foreach ($patterns as $pattern) {
            $normalized = preg_replace($pattern, '', $normalized);
        }
        
        return trim($normalized);
    }

    /**
     * Normalize location for better matching
     */
    public static function normalizeLocation($location)
    {
        $normalized = strtolower(trim($location));
        
        // Remove parenthetical info like (Remote)
        $normalized = preg_replace('/\s*\([^)]*\)/i', '', $normalized);
        
        // Map countries
        $countryMap = [
            'united states' => 'usa',
            'us' => 'usa',
            'canada' => 'canada',
        ];
        
        foreach ($countryMap as $search => $replace) {
            if (stripos($normalized, $search) !== false) {
                return $replace;
            }
        }
        
        return trim($normalized);
    }
}
