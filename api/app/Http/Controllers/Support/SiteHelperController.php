<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Mail\SiteMailServer;
use App\Models\ProNetwork\ProNetworkUserProfileEducation;
use App\Models\ProNetwork\ProNetworkUserProfileExperience;
use App\Models\ProNetwork\ProNetworkUserProfileHonour;
use App\Models\ProNetwork\ProNetworkUserProfileInterest;
use App\Models\ProNetwork\ProNetworkUserProfileSkill;
use App\Models\ProNetwork\ProNetworkUserProfileVolunteering;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SebastianBergmann\Environment\Console;

class SiteHelperController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function data(Request $request)
    {

    }

    public function grabFirstCharacter($str)
    {
        return $str[0];
    }

    /**
     * Handle unsubscribe email POST request
    */
    public function unsubscribeEmail(Request $request)
    {
        // Here you would mark the user/email as unsubscribed in your database or mailing list provider.
        // For demo, just return success. You can extend this to actually update a DB table if needed.

        // Optionally, you can get the email from the request or session if you want to track who unsubscribed.
        // $email = $request->input('email');

        return response()->json(['success' => true]);
    }

    public function randomColor()
    {
        $colors = array("red",  "yellow", "green", "blue",  "purple", "pink", "indigo");
        $num = array_rand($colors);
        return $colors[$num];
    }

    public function sendSiteEmail($toEmail, $title, $body, $firstname)
    {
        $details = [
            'title' => $title,
            'body' => $body,
            'firstname' => $firstname,
        ];

       // while testing, always send to static email, 
       // in prod change to $toEmail
       //Mail::to($toEmail)->send(new SiteMailServer($details, $title));
       Mail::to('codnerkj@gmail.com')->send(new SiteMailServer($details, $title));
    }

    public function createAlphaNumericId(){
        return uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;
    }

    public function isEndDateBeforeStartDate($startDate, $endDate) {
        // Convert the date strings to DateTime objects
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
    
        // Compare the two dates
        if ($end < $start) {
            return true; // End date is before start date
        } else {
            return false; // End date is not before start date
        }
    }

    function isDateInThePast($startDate, $endDate) {
        // Get the current date
        $currentDate = new DateTime();
        
        // Convert the date strings to DateTime objects
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // If both the start and end dates are before the current date, return true
        if ($start < $currentDate && $end < $currentDate) {
            return true;
        }
        
        // Otherwise, return false if either start or end date is in the future
        return false;
    }

    // *********PRONETWORK HELPERS***********
    //returns next number of the current order
    function orderDeterminerForProNetworkProfile($id, $type) { 
        // Get the current date
        $obj = '';
        $result = '';

        if($type === 'education'){
            if(ProNetworkUserProfileEducation::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileEducation::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }else if($type === 'experience'){
            if(ProNetworkUserProfileExperience::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileExperience::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
                
            }else{
                $result = '0';
            }
        }else if($type === 'honour'){
            if(ProNetworkUserProfileHonour::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileHonour::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }else if($type === 'interest'){
            if(ProNetworkUserProfileInterest::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileInterest::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }else if($type === 'skill'){
            if(ProNetworkUserProfileSkill::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileSkill::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }else if($type === 'volunteer'){
            if(ProNetworkUserProfileVolunteering::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileVolunteering::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }
        
        return $result;
    }

    function getRandomProNetworkBackgroundProfileImage() {
        $images = array("background_1.png", "background_2.png", 
                        "background_3.png", "background_4.png",  
                        "background_5.png", "background_6.png", 
                        "background_7.png", "background_8.png",
                        "background_9.png",
                        "background_10.png"
                        ,"background_11.png"
                        ,"background_12.png"
                        ,"background_13.png"
                        ,"background_14.png"
                        ,"background_15.png");
        $num = array_rand($images);
        return $images[$num];
    }



    
}
