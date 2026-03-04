<?php

namespace App\Http\Controllers\ProNetwork;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\PostsController;
use App\Http\Controllers\Support\SiteHelperController;
use App\Mail\ProfileUpdateMail;
use App\Models\Core\FileProNetwork;
use App\Models\Core\FileTemporary;
use App\Models\Local\SchoolsCanada;
use App\Models\Local\SchoolsUS;
use App\Models\Posts;
use App\Models\Posts\JobPost;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\ProNetwork\ProNetworkUserProfileAnalytics;
use App\Models\ProNetwork\ProNetworkUserProfileEducation;
use App\Models\ProNetwork\ProNetworkUserProfileExperience;
use App\Models\ProNetwork\ProNetworkUserProfileHonour;
use App\Models\ProNetwork\ProNetworkUserProfileInterest;
use App\Models\ProNetwork\ProNetworkUserProfileSkill;
use App\Models\ProNetwork\ProNetworkUserProfileVolunteering;
use App\Models\Settings\SiteSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ProNetworkProfileController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function index()
    {
       // dd(auth()->user()->posts);
    }

    public function proNetworkProfileView(Request $request)
    {
        $public_view = 'off';

        if(isset($request->public_view) && $request->public_view === 'on'){
            $public_view = $request->public_view;
        }

        $user = User::where('id', auth()->user()->id)->with(['pronetworkprofile','pronetworkprofile_analytics','pronetworkprofile_education','pronetworkprofile_experience','pronetworkprofile_honour','pronetworkprofile_interests','pronetworkprofile_skills','pronetworkprofile_volunteering'])->first(); 

        // Only show DRAFT and COMMITTED posts - never show UNPAID
        $draftPosts = JobPost::where('author_id', auth()->user()->id)
                            ->whereIn('status', ['DRAFT', 'COMMITTED'])
                            ->get(); 


        return Inertia::render('Profile/Profile', [
            'user' => $user,
            'draftPosts' => $draftPosts
            // 'userImagePath' => asset('storage/store_data/users/').'/'.$user->avatar,
            // 'uid_user' => $user,
            // 'uid_id' => $request->uid,
            // 'public_view' => $public_view,
        ]);

        if(ProNetworkUserProfile::where('user_id', $request->uid)->where('status', 'active')->where('type', 'person')->exists()){
            
        }else{
            return redirect()->route('pageNotFound');
        }
    }

    public function proNetworkUpdateProfile(Request $request)
    {
        $user = auth()->user();

        // Track changes
        $changes = [];
        
        if ($user->firstname !== $request->input('firstname')) {
            $changes['First Name'] = ['old' => $user->firstname, 'new' => $request->input('firstname')];
        }
        
        if ($user->lastname !== $request->input('lastname')) {
            $changes['Last Name'] = ['old' => $user->lastname, 'new' => $request->input('lastname')];
        }
        
        if ($user->about !== $request->input('about')) {
            $changes['About'] = ['old' => $user->about ?: '(Not set)', 'new' => $request->input('about')];
        }
        
        if ($user->user_city !== $request->input('user_city')) {
            $changes['Location'] = ['old' => $user->user_city ?: '(Not set)', 'new' => $request->input('user_city')];
        }

        // Update User model fields
        $user->update([
            'firstname' => $request->input('firstname'),
            'lastname'  => $request->input('lastname'),
            'about'     => $request->input('about'),
            'user_city' => $request->input('user_city'),
        ]);

        // Send profile update confirmation email only if there were changes
        if (!empty($changes)) {
            try {
                Mail::to($user->email)->send(new ProfileUpdateMail($user, $changes));
            } catch (\Exception $e) {
                Log::error('Failed to send profile update email: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Profile updated successfully.']);
    }

    // Insert and Update Functions
    public function proNetworkProfileEducationInsertUpdateRecord(Request $request)
    {

        $school_local = '';
        $city = '';
        $state_province = '';

        if($request->value1['country'] === 'United States'){
            $school_local = SchoolsUS::where('NAME', $request->value1['school'])->first();
            $city = $school_local->CITY;
            $state_province = $request->value1['location_state_province_name'];
        }else if($request->value1['country'] === 'Canada'){
            $school_local = SchoolsCanada::where('Facility_Name', $request->value1['school'])->first();
            $city = $school_local->City;
            $state_province = $request->value1['location_state_province_name'];
        }

        if($request->value2 === 'Add'){
            // Insert data into the pronetwork_user_profile_education table
            $education = new ProNetworkUserProfileEducation();
            $education->user_id = auth()->user()->id;
            $education->school = $request->value1['school'];
            $education->degree = $request->value1['degree'];
            $education->field_of_study = $request->value1['field_of_study'];
            $education->grade = $request->value1['grade'];
            $education->description = $request->value1['description'];
            $education->location_city = $city;
            $education->location_country = $request->value1['country'];
            $education->location_state_province = $state_province;
            $education->start_date = $request->value1['start_date'];
            $education->end_date = $request->value1['end_date'];
            $education->location_state_province_abbrv = $request->value1['location_state_province_abbrv'];
            $education->status = 'active';
            $education->type = 'person';
            $education->order = app(SiteHelperController::class)->orderDeterminerForProNetworkProfile(auth()->user()->id, 'education');

            // Save the education record
            if(app(SiteHelperController::class)->isEndDateBeforeStartDate($request->value1['start_date'], $request->value1['end_date'])){
                return 'CANNOT_HAVE_ENDDATE_BEFORE_STARTDATE';
            }else if(app(SiteHelperController::class)->isDateInThePast($request->value1['start_date'], $request->value1['end_date']) === true){
                return 'CANNOT_HAVE_A_PAST_PERIOD_IN_THE_FUTURE';
            }else{
                $education->save();
            }
        }else if($request->value2 === 'Edit'){
            // Update existing record
            $education = ProNetworkUserProfileEducation::find($request->value1['id']);
            
            if ($education && $education->user_id === auth()->user()->id) {
                $education->school = $request->value1['school'];
                $education->degree = $request->value1['degree'];
                $education->field_of_study = $request->value1['field_of_study'];
                $education->grade = $request->value1['grade'];
                $education->description = $request->value1['description'];
                $education->location_city = $city;
                $education->location_country = $request->value1['country'];
                $education->location_state_province = $state_province;
                $education->start_date = $request->value1['start_date'];
                $education->end_date = $request->value1['end_date'];
                $education->location_state_province_abbrv = $request->value1['location_state_province_abbrv'];

                // Validate dates again
                if (app(SiteHelperController::class)->isEndDateBeforeStartDate($request->value1['start_date'], $request->value1['end_date'])) {
                    return 'CANNOT_HAVE_ENDDATE_BEFORE_STARTDATE';
                } else if (app(SiteHelperController::class)->isDateInThePast($request->value1['start_date'], $request->value1['end_date']) === true) {
                    return 'CANNOT_HAVE_A_PAST_PERIOD_IN_THE_FUTURE';
                } else {
                    $education->save();
                }
            } else {
                return response()->json(['message' => 'Education record not found or unauthorized.'], 404);
            }
        }

        // Return a success response
        return response()->json([
            'message' => 'Education profile added successfully'
        ], 201);
    }

    public function proNetworkProfileExperienceInsertUpdateRecord(Request $request)
    {

        if($request->value2 === 'Add'){
            // Create a new Experience entry
            $experience = new ProNetworkUserProfileExperience();
            $experience->user_id = auth()->user()->id;
            $experience->position = $request->value1['position'];
            $experience->company = $request->value1['company'];
            $experience->location_city = $request->value1['location_city'];
            $experience->location_country = $request->value1['location_country'];
            $experience->location_state_province = $request->value1['location_state_province'];
            $experience->start_date = $request->value1['start_date'];
            $experience->end_date = $request->value1['end_date'];
            $experience->status = 'active';
            $experience->type = 'person';
            $experience->order = app(SiteHelperController::class)->orderDeterminerForProNetworkProfile(auth()->user()->id, 'experience');;

            // Save the experience record
            if(app(SiteHelperController::class)->isEndDateBeforeStartDate($request->value1['start_date'], $request->value1['end_date'])){
                return 'CANNOT_HAVE_ENDDATE_BEFORE_STARTDATE';
            }else if(app(SiteHelperController::class)->isDateInThePast($request->value1['start_date'], $request->value1['end_date']) === true){
                return 'CANNOT_HAVE_A_PAST_PERIOD_IN_THE_FUTURE';
            }else{
                $experience->save();
            }
        }else if($request->value2 === 'Edit'){
             // Update existing Experience entry
            $experience = ProNetworkUserProfileExperience::find($request->value1['id']);
            
            if ($experience && $experience->user_id === auth()->user()->id) {
                $experience->position = $request->value1['position'];
                $experience->company = $request->value1['company'];
                $experience->location_city = $request->value1['location_city'];
                $experience->location_country = $request->value1['location_country'];
                $experience->location_state_province = $request->value1['location_state_province'];
                $experience->start_date = $request->value1['start_date'];
                $experience->end_date = $request->value1['end_date'];

                // Validate dates again
                if (app(SiteHelperController::class)->isEndDateBeforeStartDate($request->value1['start_date'], $request->value1['end_date'])) {
                    return 'CANNOT_HAVE_ENDDATE_BEFORE_STARTDATE';
                } else if (app(SiteHelperController::class)->isDateInThePast($request->value1['start_date'], $request->value1['end_date']) === true) {
                    return 'CANNOT_HAVE_A_PAST_PERIOD_IN_THE_FUTURE';
                } else {
                    $experience->save();
                }
            } else {
                return response()->json(['message' => 'Experience record not found or unauthorized.'], 404);
            }
        }

        // Return a success response
        return response()->json([
            'message' => 'Experience added successfully'
        ], 201);
    }

    public function proNetworkProfileHonoursInsertUpdateRecord(Request $request)
    {

        if($request->value2 === 'Add'){
            // Create a new Honour entry
            $honour = new ProNetworkUserProfileHonour();
            $honour->user_id = auth()->user()->id;
            $honour->education_association_id = 0;
            $honour->title = $request->value1['title'];
            $honour->description = $request->value1['description'];
            $honour->issuer = $request->value1['issuer'];
            $honour->issuer_start_date = $request->value1['issuer_start_date'];
            $honour->status = 'active';
            $honour->type = 'status';
            $honour->order = app(SiteHelperController::class)->orderDeterminerForProNetworkProfile(auth()->user()->id, 'honour');

            // Save the honour record
            $honour->save();
            // Save the skill record
            if($request->value1['title'] === "" || $request->value1['description'] === "" || $request->value1['issuer'] === "" || $request->value1['issuer_start_date'] === ""){
                return 'NO_DATA_HAS_BEEN_SUBMITTED_VIA_PROPER_VALIDATION';
            }else{
                $honour->save();
            }
        }else if($request->value2 === 'Edit'){
            // Update existing Honour entry
            $honour = ProNetworkUserProfileHonour::find($request->value1['id']);

            if ($honour && $honour->user_id === auth()->user()->id) {
                // Update fields
                $honour->title = $request->value1['title'];
                $honour->description = $request->value1['description'];
                $honour->issuer = $request->value1['issuer'];
                $honour->issuer_start_date = $request->value1['issuer_start_date'];

                // Validate required fields again
                if (empty($request->value1['title']) || empty($request->value1['description']) || empty($request->value1['issuer']) || empty($request->value1['issuer_start_date'])) {
                    return response()->json(['message' => 'NO_DATA_HAS_BEEN_SUBMITTED_VIA_PROPER_VALIDATION'], 400);
                }

                // Save the updated honour record
                $honour->save();
            } else {
                return response()->json(['message' => 'Honour record not found or unauthorized.'], 404);
            }
        }

        

        // Return a success response
        return response()->json([
            'message' => 'Honour added successfully'
        ], 201);
    }

    public function proNetworkProfileInterestsInsertUpdateRecord(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'group_id' => 'required|integer',
            'type' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
        ]);

        // Create a new Interest entry
        $interest = new ProNetworkUserProfileInterest();
        $interest->user_id = $validatedData['user_id'];
        $interest->group_id = $validatedData['group_id'];
        $interest->type = $validatedData['type'];
        $interest->status = $validatedData['status'];

        // Save the interest record
        $interest->save();

        // Return a success response
        return response()->json([
            'message' => 'Interest added successfully',
            'interest' => $interest
        ], 201);
    }

    public function proNetworkProfileSkillInsertUpdateRecord(Request $request)
    {
        // Validate the incoming request data
        if($request->value2 === 'Add'){
            // Create a new Skill entry
            $skill = new ProNetworkUserProfileSkill();
            $skill->user_id = auth()->user()->id;
            $skill->skill = $request->value1['skill'];
            $skill->description = $request->value1['description'];
            $skill->votes =  0; // Default to 0 if not provided
            $skill->status = 'active';
            $skill->type = 'person';
            $skill->order = app(SiteHelperController::class)->orderDeterminerForProNetworkProfile(auth()->user()->id, 'skill');

            // Save the skill record
            if($request->value1['skill'] === "" || $request->value1['description'] === ""){
                return 'NO_DATA_HAS_BEEN_SUBMITTED_VIA_PROPER_VALIDATION';
            }else{
                $skill->save();
            }
        }else if($request->value2 === 'Edit'){
            // Update existing Skill entry
            $skill = ProNetworkUserProfileSkill::find($request->value1['id']);

            if ($skill && $skill->user_id === auth()->user()->id) {
                // Update fields
                $skill->skill = $request->value1['skill'];
                $skill->description = $request->value1['description'];

                // Validate required fields again
                if (empty($request->value1['skill']) || empty($request->value1['description'])) {
                    return response()->json(['message' => 'NO_DATA_HAS_BEEN_SUBMITTED_VIA_PROPER_VALIDATION'], 400);
                } else {
                    // Save the updated skill record
                    $skill->save();
                }
            } else {
                return response()->json(['message' => 'Skill record not found or unauthorized.'], 404);
            }
        }

        // Return a success response
        return response()->json([
            'message' => 'Skill added successfully'
        ], 201);
    }

    public function proNetworkProfileAboutInsertUpdateRecord(Request $request)
    {
        // Validate the incoming request data
        if($request->value2 === 'Add'){
           
        }else if($request->value2 === 'Edit'){
            // Update existing Skill entry
            $myUserProfile = ProNetworkUserProfile::where('user_id', auth()->user()->id)->first();

            if ($myUserProfile && $myUserProfile->user_id === auth()->user()->id) {
                // Update fields
                $myUserProfile->detailed_about = $request->value1['detailed_about'];
                $myUserProfile->general_location_city = $request->value1['general_location_city'];
                $myUserProfile->general_location_state_province = $request->value1['general_location_state_province'];
                $myUserProfile->general_location_country = $request->value1['general_location_country'];



                // Validate required fields again
                if (empty($request->value1['detailed_about']) 
                || empty($request->value1['general_location_city'])
                || empty($request->value1['general_location_state_province'])
                || empty($request->value1['general_location_country'])) {
                    return response()->json(['message' => 'NO_DATA_HAS_BEEN_SUBMITTED_VIA_PROPER_VALIDATION'], 400);
                } else {

                    $user = User::findOrFail(auth()->user()->id);
                    $user->update([
                        'firstname' => $request->value1['first_name'],
                        'lastname' => $request->value1['last_name'],
                    ]);

                    // Save the updated skill record
                    $myUserProfile->save();
                }
            } else {
                return response()->json(['message' => 'Skill record not found or unauthorized.'], 404);
            }
        }

        // Return a success response
        return response()->json([
            'message' => 'About edited successfully'
        ], 201);
    }

    public function proNetworkProfileVolunteeringInsertUpdateRecord(Request $request)
    {
        if($request->value2 === 'Add'){
            // Create a new Volunteering entry
            $volunteering = new ProNetworkUserProfileVolunteering();
            $volunteering->user_id = auth()->user()->id;
            $volunteering->position = $request->value1['position'];
            $volunteering->volunteer_company = $request->value1['volunteer_company'];
            $volunteering->location_city = $request->value1['location_city'];
            $volunteering->location_country = $request->value1['location_country'];
            $volunteering->location_state_province = $request->value1['location_state_province'];
            $volunteering->start_date = $request->value1['start_date'];
            $volunteering->end_date = $request->value1['end_date'];
            $volunteering->status = 'value';
            $volunteering->type = 'person';
            $volunteering->order = app(SiteHelperController::class)->orderDeterminerForProNetworkProfile(auth()->user()->id, 'volunteer');

            // Save the volunteering record
            if(app(SiteHelperController::class)->isEndDateBeforeStartDate($request->value1['start_date'], $request->value1['end_date'])){
                return 'CANNOT_HAVE_ENDDATE_BEFORE_STARTDATE';
            }else if(app(SiteHelperController::class)->isDateInThePast($request->value1['start_date'], $request->value1['end_date']) === true){
                return 'CANNOT_HAVE_A_PAST_PERIOD_IN_THE_FUTURE';
            }else{
                $volunteering->save();
            }
        }else if($request->value2 === 'Edit'){
            // Find the existing Volunteering entry by ID
            $volunteering = ProNetworkUserProfileVolunteering::find($request->value1['id']);
            
            if(!$volunteering) {
                return response()->json(['message' => 'Volunteering record not found'], 404);
            }

            // Update the Volunteering entry
            $volunteering->position = $request->value1['position'];
            $volunteering->volunteer_company = $request->value1['volunteer_company'];
            $volunteering->location_city = $request->value1['location_city'];
            $volunteering->location_country = $request->value1['location_country'];
            $volunteering->location_state_province = $request->value1['location_state_province'];
            $volunteering->start_date = $request->value1['start_date'];
            $volunteering->end_date = $request->value1['end_date'];

            // Validate the dates
            if(app(SiteHelperController::class)->isEndDateBeforeStartDate($request->value1['start_date'], $request->value1['end_date'])){
                return 'CANNOT_HAVE_ENDDATE_BEFORE_STARTDATE';
            } else if(app(SiteHelperController::class)->isDateInThePast($request->value1['start_date'], $request->value1['end_date']) === true){
                return 'CANNOT_HAVE_A_PAST_PERIOD_IN_THE_FUTURE';
            } else {
                $volunteering->save();
            }
        }

        // Return a success response
        return response()->json([
            'message' => 'Volunteering experience added successfully',
            //'volunteering' => $volunteering
        ], 201);
    }

    // File Upload Functions
    public function proNetworkProfileUpdateProfileHeaderImage(Request $request)
    {
        $generate_constant_an_id = app(SiteHelperController::class)->createAlphaNumericId();
        $file = '';
        $fileName = '';
        $foldername = '';
        $status = '';
        $type = '';
        $tempImages = '';

        // Define validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,gif|max:2048', // 2MB Max
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first('image')
            ], 400);
        }

        // Store the file in db - real
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = str_replace(' ', '_', $file->getClientOriginalName());
            $folder = uniqid().'-'.now()->timestamp;
            $file->storeAs('public/temp_data_img/'.$folder, $filename);

            $tempImages = FileTemporary::create([
                'file_temp_an_id' => $generate_constant_an_id,
                'foldername' => $folder,
                'filename' => $filename,
            ]);
            //return $folder;
        }

        //Store file in file system
        $i = 0;

        //Grab Temp Data
        $profile_an_id = app(SiteHelperController::class)->createAlphaNumericId();
        $file_store_an_id = $generate_constant_an_id;
        $filename = $tempImages->filename;
        $foldername = $tempImages->foldername;
        $status = 'active';
        $type = 'background_profile_image';

        $previousStoreImageVersion = 

        //Send to Update FileProNetwork Data
        $userProfile = ProNetworkUserProfile::where('user_id', auth()->user()->id)->first();
        $storeImage = '';
        if(FileProNetwork::where('id', $userProfile->header_image_id)->exists()){
            
            $storeImage = FileProNetwork::where('id', $userProfile->header_image_id)->first();
        }else{
            //Create the default header image
            $pronetwork_files_pronetwork = FileProNetwork::create([
                'reference_id' => 0,
                'table_reference_name' => '',
                'file_store_an_id' => app(SiteHelperController::class)->createAlphaNumericId(),
                'filename' => app(SiteHelperController::class)->getRandomProNetworkBackgroundProfileImage(),
                'foldername' => '0',
                'status' => 'active',
                'verify_status' => 'non-verified',
                'type' => 'background_profile_image',
                'file_order' => 0
            ]);

            $pronetwork_profile = ProNetworkUserProfile::where('user_id', auth()->user()->id)->update([
                'header_image_id' => $pronetwork_files_pronetwork->id,
            ]);

            $storeImage = FileProNetwork::where('id', $userProfile->header_image_id)->first();
        }
        
        $previousStoreImageVersion = $storeImage; //instance of previous version is kept

        //dd($previousStoreImageVersion);

        if($previousStoreImageVersion->foldername === '0' ){
            $storeImage->file_store_an_id = $generate_constant_an_id;
            $storeImage->foldername = app(SiteHelperController::class)->createAlphaNumericId();
        }
        
        $storeImage->filename = $filename;
        $storeImage->status = $status;
        $storeImage->type = $type;
        $storeImage->file_order = 0;
        
        //Save new Data
        $storeImage->save();

        //******* Transfer images from Temp to Stored in File System *******
        //dd($previousStoreImageVersion->foldername );
        if($previousStoreImageVersion->foldername === '0'){
            Storage::makeDirectory('public/store_data/pronetwork/headers/'.$generate_constant_an_id.'/');
            Storage::move('public/temp_data_img/'.$foldername.'/'.$filename, 'public/store_data/pronetwork/headers/'.$generate_constant_an_id.'/'.$filename);
            Storage::deleteDirectory('public/temp_data_img/'.$foldername.'/');
        }else{
            Storage::deleteDirectory('public/store_data/pronetwork/headers/'.$previousStoreImageVersion->foldername.'/');
            Storage::makeDirectory('public/store_data/pronetwork/headers/'.$previousStoreImageVersion->foldername.'/');
            Storage::move('public/temp_data_img/'.$foldername.'/'.$filename, 'public/store_data/pronetwork/headers/'.$previousStoreImageVersion->foldername.'/'.$filename);
            Storage::deleteDirectory('public/temp_data_img/'.$foldername.'/');
        }

        //Delete Temp Data
        $tempImages->delete();

        return response()->json([
            'status' => 'success',
        ], 201);
    
    }

    public function proNetworkProfileUpdateProfilePictureImage(Request $request)
    {
        //dd($request)
        $generate_constant_an_id = app(SiteHelperController::class)->createAlphaNumericId();
        $file = '';
        $fileName = '';
        $foldername = '';
        $status = '';
        $type = '';
        $tempImages = '';

        // Define validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,gif|max:2048', // 2MB Max
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first('image')
            ], 400);
        }

        // Store the file in db - real
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = str_replace(' ', '_', $file->getClientOriginalName());
            $folder = uniqid().'-'.now()->timestamp;
            $file->storeAs('public/temp_data_img/'.$folder, $filename);

            $tempImages = FileTemporary::create([
                'file_temp_an_id' => $generate_constant_an_id,
                'foldername' => $folder,
                'filename' => $filename,
            ]);
            //return $folder;
        }

        //Store file in file system
        $filename = $tempImages->filename;
        $foldername = $tempImages->foldername;

        //Determine if the user is using a default profile image for avatar
        /*
            - we need to check if they are using the default image because if they are not
            - then we will be generating a ton of folders uneccissarily 
        */
        $raw_avatar_path = auth()->user()->avatar;
        $split_avatar_path = explode("/", $raw_avatar_path);

        //******* Transfer images from Temp to Stored in File System *******
        if($split_avatar_path[0] === 'users'){
            Storage::makeDirectory('public/store_data/users/'.$foldername.'/');
            Storage::move('public/temp_data_img/'.$foldername.'/'.$filename, 'public/store_data/users/'.$foldername.'/'.$filename);
            Storage::deleteDirectory('public/temp_data_img/'.$foldername.'/');

            //Update User Table
            User::where('id', auth()->user()->id)->update(['avatar' => $foldername.'/'.$filename]);
        }else{
            Storage::deleteDirectory('public/store_data/users/'.$split_avatar_path[0].'/');
            Storage::makeDirectory('public/store_data/users/'.$split_avatar_path[0].'/');
            Storage::move('public/temp_data_img/'.$foldername.'/'.$filename, 'public/store_data/users/'.$split_avatar_path[0].'/'.$filename);
            Storage::deleteDirectory('public/temp_data_img/'.$foldername.'/');

            //Update User Table
            User::where('id', auth()->user()->id)->update(['avatar' => $split_avatar_path[0].'/'.$filename]);
        }

        //Delete Temp Data
        $tempImages->delete();

        return response()->json([
            'status' => 'success',
        ], 201);
    
    }

    // Delete Functions
    public function proNetworkProfileEducationDeleteRecord(Request $request)
    {
        $delete = ProNetworkUserProfileEducation::where('id', $request->value1)->delete();
    }

    public function proNetworkProfileExperienceDeleteRecord(Request $request)
    {
        $delete = ProNetworkUserProfileExperience::where('id', $request->value1)->delete();
    }

    public function proNetworkProfileInterestsDeleteRecord(Request $request)
    {
        //$delete = ProNetworkUserProfileSkill::where('id', $request->value1)->delete();
    }

    public function proNetworkProfileSkillDeleteRecord(Request $request)
    {
        $delete = ProNetworkUserProfileSkill::where('id', $request->value1)->delete();
    }

    public function proNetworkProfileVolunteeringDeleteRecord(Request $request)
    {
        $delete = ProNetworkUserProfileVolunteering::where('id', $request->value1)->delete();
    }

    public function searchForPeopleOnProNetworkPage(Request $request)
    {
    }

    // Update the view count
    public function updateViewsCount(Request $request)
    {

        // Find the user profile by user_id
        $profile = ProNetworkUserProfile::where('user_id', $request->value1)->first();
        $current_value = 0;
        if ($profile) {
            if($profile->views_count === '0' || $profile->views_count === 0){
                $current_value = 1;
            }else{
                $current_value = $profile->views_count + 1;
            }
            // Update the views_count field
            $profile->views_count = $current_value ;
            $profile->save();

            return response()->json([
                'message' => 'Views count updated successfully.',
                'views_count' => $profile->views_count,
            ], 200);
        }

        // If profile is not found, return an error response
        return response()->json([
            'message' => 'Profile not found.',
        ], 404);
    }

    /**
     * Delete user account - scrub personal data and set status to inactive
     */
    public function deleteAccount(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            // Validate confirmation word
            $confirmationWord = $request->input('confirmation');
            if (strtolower($confirmationWord) !== 'delete') {
                return response()->json([
                    'success' => false,
                    'message' => 'You must type "delete" to confirm account deletion.'
                ], 400);
            }

            // Archive all user's job posts
            JobPost::where('author_id', $user->id)->update([
                'status' => 'ARCHIVED',
            ]);

            // Scrub personal information
            $user->update([
                'firstname' => 'Deleted',
                'lastname' => 'User',
                'email' => 'deleted_' . $user->id . '@deleted.com',
                'username' => 'deleted_user_' . $user->id,
                'about' => null,
                'user_city' => null,
                'status' => 'inactive',
                'avatar' => 'default-avatar.png',
            ]);

            // Update ProNetwork profile if exists
            $proProfile = ProNetworkUserProfile::where('user_id', $user->id)->first();
            if ($proProfile) {
                $proProfile->update([
                    'detailed_about' => null,
                    'general_location_city' => null,
                    'general_location_state_province' => null,
                    'general_location_country' => null,
                    'status' => 'inactive',
                ]);
            }

            // Delete profile analytics
            ProNetworkUserProfileAnalytics::where('user_id', $user->id)->delete();
            
            // Delete profile sections
            ProNetworkUserProfileEducation::where('user_id', $user->id)->delete();
            ProNetworkUserProfileExperience::where('user_id', $user->id)->delete();
            ProNetworkUserProfileHonour::where('user_id', $user->id)->delete();
            ProNetworkUserProfileSkill::where('user_id', $user->id)->delete();
            ProNetworkUserProfileVolunteering::where('user_id', $user->id)->delete();
            ProNetworkUserProfileInterest::where('user_id', $user->id)->delete();

            // Logout the user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account. Please try again.'
            ], 500);
        }
    }
}