<?php

namespace App\Http\Controllers\Vendor\Voyager;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Address;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Core\FileCredentialStored;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Util\ArrayCollection;
use App\Http\Controllers\Core\NotificationsController;
use Inertia\Inertia;

class ManageUserAccountsController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
        Paginator::useBootstrap();
    }

    public function index(Request $request)
    {
        $users = '';
        $pagination_delimit = 15;

        // Build the query for all users (you can filter by role if needed)
        $query = User::query();

        // Apply search filters
        if ($request->has('firstname') && !empty($request->firstname)) {
            $query->where('firstname', 'like', '%' . $request->firstname . '%');
        } elseif ($request->has('lastname') && !empty($request->lastname)) {
            $query->where('lastname', 'like', '%' . $request->lastname . '%');
        } elseif ($request->has('status') && !empty($request->status)) {
            $query->where('status', 'like', '%' . $request->status . '%');
        } elseif ($request->has('email') && !empty($request->email)) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Order by latest created users
        $users = $query->orderBy('created_at', 'desc')->paginate($pagination_delimit);

        // Prepare search parameters for the frontend
        $searchParams = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'status' => $request->status,
        ];

        return Inertia::render('Admin/UserManagement/UserAccounts', [
            'users' => $users,
            'searchParams' => array_filter($searchParams), // Remove null values
        ]);
    }

    public function updateAccountStatus(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'action' => 'required|string',
            ]);

            $user = User::findOrFail($request->user_id);

            switch ($request->action) {
                case 'activate':
                    $user->update(['status' => 'active']);
                    
                    app(NotificationsController::class)->generateSiteNotification(
                        $user->firstname . ' ' . $user->lastname . ' your account is now Active.',
                        'Your account has been made active. The status of your account allows you to log in and use the site as normal.<br /><br />Thank you, <br />GigBizness Team',
                        'admin_active_message',
                        0,
                        $user->id,
                        'true',
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'User account has been activated successfully.'
                    ]);

                case 'deactivate':
                    $user->update(['status' => 'inactive']);
                    
                    app(NotificationsController::class)->generateSiteNotification(
                        $user->firstname . ' ' . $user->lastname . ' your account is now In-Active.',
                        'Your account has been made in-active. This is likely because of our guidelines, terms or policies have been violated. You will be contacted with regard to the reason of the account being made in-active. You will not be able to log into your account until further notice. If you wish to get in contact with us, please use the contact us form to do so. <br /><br />Thank you, <br />GigBizness Team',
                        'admin_active_message',
                        0,
                        $user->id,
                        'true',
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'User account has been deactivated successfully.'
                    ]);

                case 'suspend':
                    $request->validate(['days' => 'required|integer|min:1']);
                    
                    $suspend_end_date = Carbon::now()->addDays($request->days);
                    
                    $user->update([
                        'suspend_reactive' => $suspend_end_date,
                        'status' => 'inactive'
                    ]);

                    app(NotificationsController::class)->generateSiteNotification(
                        $user->firstname . ' ' . $user->lastname . ' your account has been suspended for ' . $request->days . ' day(s)',
                        'Your account has been temporarily suspended. This is likely because of our guidelines, terms or policies have been violated. You will be contacted with regard to the reason of the account being made in-active. You will not be able to log into your account until this time has been exceeded. If you think this was done by mistake, wish to get in contact with us, please use the contact us form to do so. <br /><br />Thank you, <br />GigBizness Team',
                        'admin_active_message',
                        0,
                        $user->id,
                        'true',
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'User account has been suspended for ' . $request->days . ' day(s).'
                    ]);

                case 'end_suspend':
                    $user->update([
                        'suspend_reactive' => Carbon::now(),
                        'status' => 'active'
                    ]);

                    app(NotificationsController::class)->generateSiteNotification(
                        $user->firstname . ' ' . $user->lastname . ' your account is no longer suspended!',
                        'Your account suspension has been lifted. The status of your account allows you to log in and use the site as normal <br /><br />Thank you, <br />GigBizness Team',
                        'admin_active_message',
                        0,
                        $user->id,
                        'true',
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'User account suspension has been lifted successfully.'
                    ]);

                case 'credential':
                    $request->validate(['credential_action' => 'required|string|in:verify,deny']);
                    
                    if (!FileCredentialStored::where('user_id', $user->id)->exists()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'User does not have credentials uploaded.'
                        ]);
                    }

                    if ($request->credential_action === 'verify') {
                        FileCredentialStored::where('user_id', $user->id)->update(['status' => 'verified']);
                        $user->update(['user_IsVerified' => 'yes']);

                        app(NotificationsController::class)->generateSiteNotification(
                            $user->firstname . ' ' . $user->lastname . ' your credentials have been verified.',
                            'Your account has been verified. You will now be able to upload trade items to your account.<br /><br />Thank you, <br />GigBizness Team',
                            'admin_active_message',
                            0,
                            $user->id,
                            'true',
                        );

                        return response()->json([
                            'success' => true,
                            'message' => 'User credentials have been verified successfully.'
                        ]);
                    } else {
                        FileCredentialStored::where('user_id', $user->id)->update(['status' => 'denied']);
                        $user->update(['user_IsVerified' => 'no']);

                        app(NotificationsController::class)->generateSiteNotification(
                            $user->firstname . ' ' . $user->lastname . ' your credentials have been denied.',
                            'Your credentials have been denied, this is likely due to some inconsistencies in your document versus your account. If you would still like your documentation verified, please use the contact us form to reach out to us so we can take another look. <br /><br />Thank you, <br />GigBizness Team',
                            'admin_active_message',
                            0,
                            $user->id,
                            'true',
                        );

                        return response()->json([
                            'success' => true,
                            'message' => 'User credentials have been denied.'
                        ]);
                    }

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action specified.'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function retrieveCredential(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $user = User::findOrFail($request->user_id);
            $cred = FileCredentialStored::where('user_id', $user->id)->first();
            $address = Address::where('user_id', $user->id)->first();

            if (!$cred) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have credentials uploaded.'
                ]);
            }

            $credentialData = [
                'user_info' => [
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'email' => $user->email,
                    'phone' => $user->phon_num ?? 'N/A',
                ],
                'address_info' => $address ? [
                    'street_num' => $address->addr_street_num,
                    'street' => $address->addr_street,
                    'apartment' => $address->addr_apart_num,
                    'unit' => $address->addr_unit,
                    'suite' => $address->addr_suite,
                    'department' => $address->addr_department,
                    'zip' => $address->addr_zip,
                    'postal_code' => $address->addr_postal_code,
                    'country' => $address->addr_country,
                    'city' => $address->addr_city,
                    'province' => $address->addr_province,
                    'state' => $address->addr_state,
                ] : null,
                'credential_info' => [
                    'status' => $cred->status,
                    'filename' => $cred->filename,
                    'file_url' => asset('storage/store_data/id/' . $cred->foldername . '/' . $cred->filename),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $credentialData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving credentials: ' . $e->getMessage()
            ], 500);
        }
    }

    // Legacy methods for backward compatibility
    public function updateCredentialStatus(Request $request)
    {
        // Redirect to the new unified method
        $request->merge([
            'action' => 'credential',
            'credential_action' => $request->value2 === 'verified' ? 'verify' : 'deny',
            'user_id' => $request->value1
        ]);

        return $this->updateAccountStatus($request);
    }

    public function suspendAccount(Request $request)
    {
        // Redirect to the new unified method
        if ($request->value2 === 'suspend') {
            $request->merge([
                'action' => 'suspend',
                'user_id' => $request->value1,
                'days' => $request->value3
            ]);
        } else {
            $request->merge([
                'action' => 'end_suspend',
                'user_id' => $request->value1
            ]);
        }

        return $this->updateAccountStatus($request);
    }

    public function checkIfActive(Request $request)
    {
        $user = User::find($request->value1);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $data = $user->status === 'inactive' ? 'inactive' : 'active';
        return response()->json(['success' => true, 'status' => $data]);
    }

    public function checkIfSuspended(Request $request)
    {
        $user = User::find($request->value1);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $data = (Carbon::now() > new Carbon($user->suspend_reactive)) ? 'not_suspended' : 'suspended';
        return response()->json(['success' => true, 'status' => $data]);
    }
}
