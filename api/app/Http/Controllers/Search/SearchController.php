<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\Local\CitiesCanada;
use App\Models\Local\CitiesUS;
use App\Models\Settings\SiteSettings;
use App\Models\Item;
use App\Models\User;
use App\Models\ItemType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Core\PostsController;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\ProNetwork\ProNetworkRequests;
use App\Models\Posts\JobPost;
use App\Models\Core\FilePostStored;
use App\Models\Search;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['guest']);
    }

    /**
     * Track search activity with all characteristics
     */
    private function trackSearch(Request $request, $searchTerm, $resultCount, $userType = 'guest')
    {
        try {
            // Extract all filter parameters
            $filters = [
                'job_type' => $request->input('job_type'),
                'job_role' => $request->input('job_role'),
                'location_type' => $request->input('location_type'),
                'location' => $request->input('location'),
                'salary_min' => $request->input('salary_min'),
                'salary_max' => $request->input('salary_max'),
                'benefits' => $request->input('benefits'),
            ];

            // Remove null values
            $filters = array_filter($filters, function($value) {
                return $value !== null;
            });

            Search::create([
                'user_id' => auth()->id(),
                'search_text' => $searchTerm,
                'ip' => $request->ip(),
                'type' => $userType,
                'status' => 'completed',
                'page' => $request->input('page', 1),
                'ttl' => null, // Can be used for search expiry/caching
                'result_num' => $resultCount,
                'filter' => json_encode($filters), // Store filters as JSON (column name is 'filter' not 'filters')
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer'),
            ]);
        } catch (\Exception $e) {
            // Don't break the search if tracking fails
            Log::error('Search tracking error: ' . $e->getMessage());
        }
    }

    /**
     * Unified search - directs to authenticated or guest search based on auth status
     */
    public function search(Request $request)
    {
        if (Auth::check()) {
            return $this->index($request);
        } else {
            return $this->guestSearch($request);
        }
    }

    /**
     * Search for authenticated users - with filters
     */
    public function index(Request $request)
    {
        try {
            $searchTerm = $request->q ?? '';
            $parse_search = preg_replace('~%20| ~', " ", $searchTerm);

            // Build query - only show COMMITTED and non-expired jobs
            $query = JobPost::where('status', 'COMMITTED')
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                });

            // Apply search filter
            if (!empty($parse_search)) {
                $query->where(function($q) use ($parse_search) {
                    $q->where('title', 'like', '%' . $parse_search . '%')
                      ->orWhere('job_description', 'like', '%' . $parse_search . '%')
                      ->orWhere('company_name', 'like', '%' . $parse_search . '%');
                });
            }

            // Apply filters from request
            if ($request->filled('job_type')) {
                $jobTypes = is_array($request->job_type) ? $request->job_type : [$request->job_type];
                $query->whereIn('employer_type', $jobTypes);
            }

            if ($request->filled('job_role')) {
                $jobRoles = is_array($request->job_role) ? $request->job_role : [$request->job_role];
                $query->where(function($q) use ($jobRoles) {
                    foreach ($jobRoles as $role) {
                        $q->orWhere(function($subQ) use ($role) {
                            $subQ->where('position', 'like', '%' . $role . '%')
                                 ->orWhere('title', 'like', '%' . $role . '%')
                                 ->orWhere('primary_tag', 'like', '%' . $role . '%');
                        });
                    }
                });
            }

            if ($request->filled('location_type')) {
                $query->where('location_type', $request->location_type);
            }

            if ($request->filled('location')) {
                $query->where('location_restriction', 'like', '%' . $request->location . '%');
            }

            if ($request->filled('salary_min')) {
                $query->where('salary_min', '>=', $request->salary_min);
            }

            if ($request->filled('salary_max')) {
                $query->where('salary_max', '<=', $request->salary_max);
            }

            // Filter by benefits
            if ($request->filled('benefits')) {
                $benefitsToFilter = is_array($request->benefits) ? $request->benefits : [$request->benefits];
                $query->where(function($q) use ($benefitsToFilter) {
                    foreach ($benefitsToFilter as $benefit) {
                        $q->orWhereJsonContains('benefits', $benefit);
                    }
                });
            }


            // ✅ CRITICAL: Time-based sticky post expiration logic (authenticated search)
            // Only posts within their paid duration appear at top
            $jobs = $query->orderByRaw('(
                (sticky_note_24_hour = 1 AND created_at >= NOW() - INTERVAL 24 HOUR) OR
                (sticky_note_week = 1 AND created_at >= NOW() - INTERVAL 7 DAY) OR
                (sticky_note_month = 1 AND created_at >= NOW() - INTERVAL 30 DAY)
            ) DESC')
            ->orderBy('created_at', 'desc')
            ->limit(300)
            ->get();

            // Transform data for frontend
            $jobs = $jobs->map(function ($job) {
                // Get uploaded image if exists
                $uploadedImage = null;
                $imageFile = FilePostStored::where('post_id', $job->id)
                    ->where('type', 'job_post')
                    ->where('status', 'active')
                    ->first();
                
                if ($imageFile) {
                    $uploadedImage = asset('storage/store_data/posts/draft/' . $imageFile->foldername . '/' . $imageFile->filename);
                }

                // Get first 10 benefits
                $benefits = is_array($job->benefits) ? array_slice($job->benefits, 0, 10) : [];
                
                // Parse tags
                $tags = [];
                if ($job->secondary_tags) {
                    $tags = is_array($job->secondary_tags) ? $job->secondary_tags : json_decode($job->secondary_tags, true);
                }

                return [
                    'id' => $job->id,
                    'slug' => $job->slug,
                    'company' => $job->company_name,
                    'title' => $job->title,
                    'salary' => $job->salary_min && $job->salary_max 
                        ? '$' . number_format($job->salary_min) . ' - $' . number_format($job->salary_max) 
                        : ($job->budget ?? ''),
                    'location' => $job->location_restriction ?? 'Anywhere',
                    'postedDaysAgo' => $job->created_at ? $job->created_at->diffForHumans() : '',
                    'company_logo' => $job->company_logo,
                    'uploaded_image' => $uploadedImage,
                    'job_description' => $job->job_description,
                    'apply_url' => $job->apply_url,
                    'employer_type' => $job->employer_type,
                    'location_type' => $job->location_type,
                    'primary_tag' => $job->primary_tag,
                    'payment_frequency' => $job->payment_frequency,
                    'tags' => $tags,
                    'benefits' => $benefits,
                    'currency' => $job->currency,
                    // Paid feature fields
                    'sticky_note_24_hour' => $job->sticky_note_24_hour,
                    'sticky_note_week' => $job->sticky_note_week,
                    'sticky_note_month' => $job->sticky_note_month,
                    'highlight_post' => $job->highlight_post,
                    'create_qr_code' => $job->create_qr_code,
                    'show_company_logo' => $job->show_company_logo,
                ];
            });

            // Track the search
            $this->trackSearch($request, $parse_search, $jobs->count(), 'authenticated');

            return Inertia::render('Search/SearchResultsAuthenticated', [
                'jobs' => $jobs,
                'search_term' => $parse_search,
            ]);
        } catch (\Exception $e) {
            \Log::error('Job search error: ' . $e->getMessage());
            return Inertia::render('Search/SearchResultsAuthenticated', [
                'jobs' => collect([]),
                'search_term' => $request->q ?? '',
            ]);
        }
    }

    /**
     * Search for guest users - with filters
     */
    public function guestSearch(Request $request)
    {
        try {
            $searchTerm = $request->q ?? '';
            $parse_search = preg_replace('~%20| ~', " ", $searchTerm);

            // Build query - only show COMMITTED and non-expired jobs
            $query = JobPost::where('status', 'COMMITTED')
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                });

            // Apply search filter
            if (!empty($parse_search)) {
                $query->where(function($q) use ($parse_search) {
                    $q->where('title', 'like', '%' . $parse_search . '%')
                      ->orWhere('job_description', 'like', '%' . $parse_search . '%')
                      ->orWhere('company_name', 'like', '%' . $parse_search . '%');
                });
            }

            // Apply filters from request
            if ($request->filled('job_type')) {
                $jobTypes = is_array($request->job_type) ? $request->job_type : [$request->job_type];
                $query->whereIn('employer_type', $jobTypes);
            }

            if ($request->filled('job_role')) {
                $jobRoles = is_array($request->job_role) ? $request->job_role : [$request->job_role];
                $query->where(function($q) use ($jobRoles) {
                    foreach ($jobRoles as $role) {
                        $q->orWhere(function($subQ) use ($role) {
                            $subQ->where('position', 'like', '%' . $role . '%')
                                 ->orWhere('title', 'like', '%' . $role . '%')
                                 ->orWhere('primary_tag', 'like', '%' . $role . '%');
                        });
                    }
                });
            }

            if ($request->filled('location_type')) {
                $query->where('location_type', $request->location_type);
            }

            if ($request->filled('location')) {
                $query->where('location_restriction', 'like', '%' . $request->location . '%');
            }

            if ($request->filled('salary_min')) {
                $query->where('salary_min', '>=', $request->salary_min);
            }

            if ($request->filled('salary_max')) {
                $query->where('salary_max', '<=', $request->salary_max);
            }

            // Filter by benefits
            if ($request->filled('benefits')) {
                $benefitsToFilter = is_array($request->benefits) ? $request->benefits : [$request->benefits];
                $query->where(function($q) use ($benefitsToFilter) {
                    foreach ($benefitsToFilter as $benefit) {
                        $q->orWhereJsonContains('benefits', $benefit);
                    }
                });
            }


            // ✅ Priority by price: Month(3) > Week(2) > 24h(1) > Regular(0)
            $jobs = $query->orderByRaw('CASE
                WHEN sticky_note_month = 1 AND created_at >= NOW() - INTERVAL 30 DAY THEN 3
                WHEN sticky_note_week = 1 AND created_at >= NOW() - INTERVAL 7 DAY THEN 2
                WHEN sticky_note_24_hour = 1 AND created_at >= NOW() - INTERVAL 24 HOUR THEN 1
                ELSE 0
            END DESC')
            ->orderBy('created_at', 'desc')
            ->limit(300)
            ->get();

            // Transform data for frontend
            $jobs = $jobs->map(function ($job) {
                // Get uploaded image if exists
                $uploadedImage = null;
                $imageFile = FilePostStored::where('post_id', $job->id)
                    ->where('type', 'job_post')
                    ->where('status', 'active')
                    ->first();
                
                if ($imageFile) {
                    $uploadedImage = asset('storage/store_data/posts/draft/' . $imageFile->foldername . '/' . $imageFile->filename);
                }

                // Get first 10 benefits
                $benefits = is_array($job->benefits) ? array_slice($job->benefits, 0, 10) : [];
                
                // Parse tags
                $tags = [];
                if ($job->secondary_tags) {
                    $tags = is_array($job->secondary_tags) ? $job->secondary_tags : json_decode($job->secondary_tags, true);
                }

                return [
                    'id' => $job->id,
                    'slug' => $job->slug,
                    'company' => $job->company_name,
                    'title' => $job->title,
                    'salary' => $job->salary_min && $job->salary_max 
                        ? '$' . number_format($job->salary_min) . ' - $' . number_format($job->salary_max) 
                        : ($job->budget ?? ''),
                    'location' => $job->location_restriction ?? 'Anywhere',
                    'postedDaysAgo' => $job->created_at ? $job->created_at->diffForHumans() : '',
                    'company_logo' => $job->company_logo,
                    'uploaded_image' => $uploadedImage,
                    'job_description' => $job->job_description,
                    'apply_url' => $job->apply_url,
                    'employer_type' => $job->employer_type,
                    'location_type' => $job->location_type,
                    'primary_tag' => $job->primary_tag,
                    'payment_frequency' => $job->payment_frequency,
                    'tags' => $tags,
                    'benefits' => $benefits,
                    'currency' => $job->currency,
                    // Paid feature fields
                    'sticky_note_24_hour' => $job->sticky_note_24_hour,
                    'sticky_note_week' => $job->sticky_note_week,
                    'sticky_note_month' => $job->sticky_note_month,
                    'highlight_post' => $job->highlight_post,
                    'create_qr_code' => $job->create_qr_code,
                    'show_company_logo' => $job->show_company_logo,
                ];
            });

            // Track the search
            $this->trackSearch($request, $parse_search, $jobs->count(), 'guest');

            return Inertia::render('Search/SearchResultsGuest', [
                'jobs' => $jobs,
                'search_term' => $parse_search,
            ]);
        } catch (\Exception $e) {
            \Log::error('Guest job search error: ' . $e->getMessage());
            return Inertia::render('Search/SearchResultsGuest', [
                'jobs' => collect([]),
                'search_term' => $request->q ?? '',
            ]);
        }
    }

    /**
     * Landing page - shows latest jobs by default
     */
    public function landing(Request $request)
    {
        try {
            // Get latest jobs - only COMMITTED and non-expired within last 30 days

            $jobs = JobPost::where('status', 'COMMITTED')
                ->where(function($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->where('created_at', '>=', now()->subDays(30))
                // ✅ Priority by price: Month(3) > Week(2) > 24h(1) > Regular(0)
                ->orderByRaw('CASE
                    WHEN sticky_note_month = 1 AND created_at >= NOW() - INTERVAL 30 DAY THEN 3
                    WHEN sticky_note_week = 1 AND created_at >= NOW() - INTERVAL 7 DAY THEN 2
                    WHEN sticky_note_24_hour = 1 AND created_at >= NOW() - INTERVAL 24 HOUR THEN 1
                    ELSE 0
                END DESC')
                ->orderBy('created_at', 'desc')
                ->limit(1000)
                ->get();

            // Transform data for frontend
            $jobs = $jobs->map(function ($job) {
                // Get uploaded image if exists
                $uploadedImage = null;
                $imageFile = FilePostStored::where('post_id', $job->id)
                    ->where('type', 'job_post')
                    ->where('status', 'active')
                    ->first();
                
                if ($imageFile) {
                    $uploadedImage = asset('storage/store_data/posts/draft/' . $imageFile->foldername . '/' . $imageFile->filename);
                }

                // Get first 10 benefits
                $benefits = is_array($job->benefits) ? array_slice($job->benefits, 0, 10) : [];

                return [
                    'id' => $job->id,
                    'slug' => $job->slug,
                    'company' => $job->company_name,
                    'title' => $job->title,
                    'salary' => $job->salary_min && $job->salary_max 
                        ? '$' . number_format($job->salary_min) . ' - $' . number_format($job->salary_max) 
                        : ($job->budget ?? ''),
                    'location' => $job->location_restriction ?? 'Anywhere',
                    'postedDaysAgo' => $job->created_at ? $job->created_at->diffForHumans() : '',
                    'company_logo' => $job->company_logo,
                    'uploaded_image' => $uploadedImage,
                    'job_description' => $job->job_description,
                    'apply_url' => $job->apply_url,
                    'payment_frequency' => $job->payment_frequency,
                    'benefits' => $benefits,
                    'location_type' => $job->location_type,
                    'employer_type' => $job->employer_type,
                    'currency' => $job->currency,
                    // Paid feature fields
                    'sticky_note_24_hour' => $job->sticky_note_24_hour,
                    'sticky_note_week' => $job->sticky_note_week,
                    'sticky_note_month' => $job->sticky_note_month,
                    'highlight_post' => $job->highlight_post,
                    'create_qr_code' => $job->create_qr_code,
                    'show_company_logo' => $job->show_company_logo,
                ];
            });

            return Inertia::render('Landing/Landing', [
                'jobs' => $jobs,
            ]);
        } catch (\Exception $e) {
            \Log::error('Landing page error: ' . $e->getMessage());
            return Inertia::render('Landing/Landing', [
                'jobs' => collect([]),
            ]);
        }
    }

    public function searchOld(Request $request)
    {
        $parse_search = $request->search;
        $pagez = $request->page;
        $parse_search = preg_replace('~%20| ~', " ", $parse_search); 
        $site_settings = SiteSettings::where('name', 'require_content_validation')->first();

        $items = Item::where('ip_title', 'like', '%' .  $parse_search . '%')->where('ip_status', 'active')->with(['user', 'itemType', 'fileStored'])->paginate(16);
        
        return view('old1.pages.search', [
            'items' => $items,
            'data' =>  $parse_search,
            'require_content_validation' => $site_settings->value,
            'page_' =>  $pagez,
        ]); 
    }

    public function searchPost(Request $request)
    {
        $parse_search = $request->search;
        $parse_search = preg_replace('~%20| ~', " ", $parse_search); 
        $site_settings = SiteSettings::where('name', 'require_content_validation')->first();

        $items = Item::where('ip_title', 'like', '%' .  $parse_search . '%')->where('ip_status', 'active')->with(['user', 'itemType', 'fileStored', 'itemLike'])->paginate(100);

        if(Auth::check()){
            return [
                'items' => $items,
                'data' =>  $parse_search,
                'require_content_validation' => $site_settings->value,
                'logged_in_user' => auth()->user()->id,
            ]; 
        }else{
            return [
                'items' => $items,
                'data' =>  $parse_search,
                'require_content_validation' => $site_settings->value,
            ]; 
        }
        
    }

    public function getStatesByCountry(Request $request)
    {
        if($request->value1 == 'Canada'){

            return [
                'AB' => ['Alberta', 'AB'],
                'BC' => ['British Columbia', 'BC'],
                'MB' => ['Manitoba', 'MB'],
                'NB' => ['New Brunswick', 'NB'],
                'NL' => ['Newfoundland and Labrador', 'NL'],
                'NT' => ['Northwest Territories', 'NT'],
                'NS' => ['Nova Scotia', 'NS'],
                'NU' => ['Nunavut', 'NU'],
                'ON' => ['Ontario', 'ON'],
                'PE' => ['Prince Edward Island', 'PE'],
                'QC' => ['Quebec', 'QC'],
                'SK' => ['Saskatchewan', 'SK'],
                'YK' => ['Yukon', 'YK'],
            ];

        }else if($request->value1 == 'US'){

            return [
                'AL' => ['Alabama', 'AL'],
                'AK' => ['Alaska', 'AK'],
                'AZ' => ['Arizona', 'AZ'],
                'AR' => ['Arkansas', 'AR'],
                'AS' => ['American Samoa', 'AS'],
                'CA' => ['California', 'CA'],
                'CO' => ['Colorado', 'CO'],
                'CT' => ['Connecticut', 'CT'],
                'DE' => ['Delaware', 'DE'],
                'DC' => ['District of Columbia', 'DC'],
                'FL' => ['Florida', 'FL'],
                'GA' => ['Georgia', 'GA'],
                'GU' => ['Guam', 'GU'],
                'HI' => ['Hawaii', 'HI'],
                'ID' => ['Idaho', 'ID'],
                'IL' => ['Illinois', 'IL'],
                'IN' => ['Indiana', 'IN'],
                'IA' => ['Indiana', 'IA'],
                'KS' => ['Kansas', 'KS'],
                'KY' => ['Kentucky', 'KY'],
                'LA' => ['Louisiana', 'LA'],
                'ME' => ['Maine', 'ME'],
                'MD' => ['Maryland', 'MD'],
                'MA' => ['Massachusetts', 'MA'],
                'MI' => ['Michigan', 'MI'],
                'MN' => ['Minnesota', 'MNAS'],
                'MS' => ['Mississippi', 'MS'],
                'MO' => ['Missouri', 'MO'],
                'MT' => ['Montana', 'MT'],
                'NE' => ['Nebraska', 'NE'],
                'NV' => ['Nevada', 'NV'],
                'NH' => ['New Hampshire', 'NH'],
                'NJ' => ['New Jersey', 'NJ'],
                'NM' => ['New Mexico', 'NM'],
                'NY' => ['New York', 'NY'],
                'NC' => ['North Carolina', 'NC'],
                'ND' => ['North Dakota', 'ND'],
                'MP' => ['Northern Mariana Islands', 'MP'],
                'OH' => ['Ohio', 'OH'],
                'OK' => ['Oklahoma', 'OK'],
                'OR' => ['Oregon', 'OR'],
                'PA' => ['Pennsylvania', 'PA'],
                'PR' => ['Puerto Rico', 'PR'],
                'RI' => ['Rhode Island', 'RI'],
                'SC' => ['South Carolina', 'SC'],
                'SD' => ['South Dakota', 'SD'],
                'TN' => ['Tennessee', 'TN'],
                'TX' => ['Texas', 'TX'],
                'TT' => ['Trust Territories', 'TT'],
                'UT' => ['Utah', 'VT'],
                'VT' => ['Vermont', 'AL'],
                'VA' => ['Virginia', 'VA'],
                'VI' => ['Virgin Islands', 'VI'],
                'WA' => ['Washington', 'WA'],
                'WV' => ['West Virginia', 'WV'],
                'WI' => ['Wisconsin', 'WI'],
                'WY' => ['Wyoming', 'WY'],
               
            ];
         }

    }

    public function getCitiesByState(Request $request)
    {
        if($request->value1 == 'Canada'){
            $cities = CitiesCanada::where('province_id', $request->value2)->orderBy('city', 'ASC')->get();
            
            return [
                'cities' => $cities
            ];

        }else if($request->value1 == 'US'){
            $cities = CitiesUS::where('state_id', $request->value2)->orderBy('city', 'ASC')->get();
            
            return [
                'cities' => $cities
            ];
        }
        
        return [
            'cities' => []
        ];
    }


    public function searchByCategory(Request $request)
    {
        $parse_search = $request->search;
        $parse_search = preg_replace('~%20| ~', " ", $parse_search); 
        $site_settings = SiteSettings::where('name', 'require_content_validation')->first();
        
        $itemTypes_ids = ItemType::where('circle_item_subtype', 'like', '%' .  $parse_search . '%')->pluck('id');
        $itemTypes_names = ItemType::where('circle_item_subtype', 'like', '%' .  'Baseball & Softball' . '%')->limit(1)->pluck('circle_item_category_type');
        $items = Item::whereIn('circle_item_type_id', $itemTypes_ids)->paginate(16);

        //TODO: Need figure out how to search with like with spaces
        //dd($itemTypes_names);

        return view('old1.pages.search', [
            'items' => $items,
            'data' =>  $parse_search,
            'data2' =>  $itemTypes_names,
            'require_content_validation' => $site_settings->value
        ]); 
    }

    public function searchForProductAndServiceseOnAgoraRequest(Request $request)
    {
        //$searchTerm = $request->q; //query term
        $productType = $request->t; //type: item, service (defined by i or s)

        $searchTerm = $request->q; //query
        
        $parse_search = $request->q;
        $parse_search = preg_replace('~%20| ~', " ", $parse_search); 
        $site_settings = SiteSettings::where('name', 'require_content_validation')->first();

        //$items = Item::where('ip_title', 'like', '%' .  $parse_search . '%')->where('ip_status', 'active')->with(['user', 'itemType', 'fileStored', 'itemLike'])->paginate(8) ->appends(['q' => $parse_search]);

        $items = Item::where('ip_title', 'like', '%' .  $parse_search . '%')->where('ip_status', 'active')->with(['user', 'itemType', 'fileStored', 'itemLike'])->limit(300)->get();

        if(Auth::check()){
            return Inertia::render('ZProtoTypes/SearchTracking/ProductSearchPage', [
                'userImagePath' => asset('storage/store_data/users/'),
                'items' => $items,
                'search_term' =>  $parse_search,
                'require_content_validation' => $site_settings->value,
                'logged_in_user' => auth()->user()->id,
            ]);
        }else{
            return Inertia::render('ZProtoTypes/SearchTracking/ProductSearchPage', [
                'userImagePath' => asset('storage/store_data/users/'),
                'items' => $items,
                'search_term' =>  $parse_search,
                'require_content_validation' => $site_settings->value,
                'logged_in_user' => '',
            ]);
        }
        

        
    }
}
