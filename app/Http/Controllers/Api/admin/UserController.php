<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\CorporateInfo;
use App\Models\ManageUser;
use App\Models\ServiceCenter;
use App\Models\TechInfo;
use App\Models\User;
use App\Models\UserRestriction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //  $corporateInfo = CorporateInfo::latest()->first();
    public function testing() {
        $latest_tech_ref_id = 'BEL-ANT-A01-03-T99';

        // Extract the numeric part from the tech reference number
        $numeric_part = substr($latest_tech_ref_id, -2);
        
        // Increment the numeric part by converting it to an integer
        $next_numeric_part = intval($numeric_part) + 1;
        
        // Pad the numeric part with leading zeros if necessary
        $padded_numeric_part = str_pad($next_numeric_part, 2, '0', STR_PAD_LEFT);
        
        // Generate the next tech reference number by combining the prefix and the incremented numeric part
        $next_tech_ref_number =   'T' . $padded_numeric_part;
        
        return $next_tech_ref_number;
        


        
    }

    /** 
     * Display a listing of the role branch manager.
     */
    public function branchmanager($id){
        $branch_manager = ManageUser::join('users', 'users.id', '=', 'manage_users.user_id')
            ->where('manage_users.corporate_manager_id', $id)     
            ->where('manage_users.branch_manager_id', 0) 
            ->select('users.id', DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS fullname"))
            ->orderBy('users.first_name','asc')
            ->get();

        return $branch_manager;
    }

    /**
     * Display a listing of the role corporate account.
     */
    public function corporate() {
        $corporate = User::where('role_id', 2)->get();
        return $corporate;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
 
        // return UserResource::collection(
        //     // Service::orderBy('id','desc')->get()
        //     User::join('roles', 'roles.id', '=', 'users.role')
        //     ->orderBy('users.id','desc')->get()
        //  ); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
            $data = $request->validated();
            $data['password'] = bcrypt('welcome@123');
            $data['created_by'] = $request->user_id;

            if ($request->user_role == 1) { 
                if ($request->role_id == 1) {
                    $user = User::create($data);
                } else {
                    $user = User::create($data);
                    $corporateInfo = CorporateInfo::latest()->first();
                    $ref = $corporateInfo['reference_id'] ?? null;

                    if ($ref) {
                        $letter = $ref[0];
                        $number = intval(substr($ref, 1));

                        if ($number == 99) {
                            // Increment the letter
                            $letter++;
                            if ($letter == 'Z') {
                                $letter = 'A'; // Reset to 'A' if it reaches 'Z'
                            }

                            // Reset the number
                            $number = 0;
                        } else {
                            $number++;
                        }

                        $str1 = str_pad($number, 2, '0', STR_PAD_LEFT);
                        $reference_id = $letter . $str1;
                    } else {
                        $reference_id = 'A00';
                    }

                    UserRestriction::create([
                        'user_id' => $user->id,
                        'allowed_sc' => $request->allowed_sc,
                        'allowed_bm' => $request->allowed_bm
                    ]);

                    CorporateInfo::create([
                        'corporate_id' => $user->id,
                        'reference_id' => $reference_id,
                    ]);
                }
                
            } else if ($request->user_role == 2) {
                $corporate_manager = ManageUser::join('users', 'users.id', '=', 'manage_users.user_id')
                                ->where('manage_users.corporate_manager_id', $request->user_id)     
                                ->where('manage_users.branch_manager_id', 0)
                                ->count();
                $restriction = UserRestriction::where('user_id', $request->user_id)->first();
                $restriction_count = $restriction['allowed_bm'] ?? 0;

                if ($request->role_id == 3) {
                    if ($corporate_manager == $restriction_count) {
                        return response([
                            'errors' => [ 'restriction' => ['Not allowed to create another Branch Manager']]
                        ], 422);
                    } else {
                        $user = User::create($data);
                        ManageUser::create([
                            'user_id' => $user->id,
                            'service_center_id' => $request->service_center_id,
                            'corporate_manager_id' => $request->user_id,
                            'branch_manager_id' => $request->branch_manager_id
                        ]);
                    }
                } else {
                    $sc = ServiceCenter::where('id', $request->service_center_id)->first();
                    $latest_tech_ref_id = TechInfo::where('service_center_id',  $request->service_center_id)->latest()->first();

                    if ($latest_tech_ref_id) {
                        // Extract the numeric part from the tech reference number
                        $numeric_part = substr($latest_tech_ref_id['tech_ref_id'], -2);
                        
                        // Increment the numeric part by converting it to an integer
                        $next_numeric_part = intval($numeric_part) + 1;
                        
                        // Pad the numeric part with leading zeros if necessary
                        $padded_numeric_part = str_pad($next_numeric_part, 2, '0', STR_PAD_LEFT);
                        
                        // Generate the next tech reference number by combining the prefix and the incremented numeric part
                        $next_tech_ref_number =   'T' . $padded_numeric_part;
                    } else {
                        $next_tech_ref_number = 'T01';
                    }

                    $data['is_activated'] = 1;

                    $user = User::create($data);
                    ManageUser::create([
                        'user_id' => $user->id,
                        'service_center_id' => $request->service_center_id,
                        'corporate_manager_id' => $request->user_id,
                        'branch_manager_id' => $request->branch_manager_id
                    ]);
                    TechInfo::create([
                        'service_center_id' => $request->service_center_id, 
                        'tech_id' => $user->id,
                        'tech_ref_id' => $sc['reference_number'] . '-' . $next_tech_ref_number,
                    ]);
                }
                    
              
                
            } else if ($request->user_role == 3) {
                $corporate = ManageUser::where('user_id', $request->user_id)->first();
                $sc = ServiceCenter::where('id', $request->service_center_id)->first();
                $latest_tech_ref_id = TechInfo::where('service_center_id',  $request->service_center_id)->latest()->first();
                $latest_tech_ref_number = $latest_tech_ref_id ? intval(substr($latest_tech_ref_id['tech_ref_id'], 1)) : 0;
                $next_tech_ref_number = $latest_tech_ref_number + 1;
                $next_tech_ref_id = 'T' . str_pad($next_tech_ref_number, 2, '0', STR_PAD_LEFT);
                $data['is_activated'] = 1;

                $user = User::create($data);
                ManageUser::create([
                    'user_id' => $user->id,
                    'service_center_id' => $corporate['service_center_id'],
                    'corporate_manager_id' => $corporate['corporate_manager_id'],
                    'branch_manager_id' => $request->user_id    
                ]);
                TechInfo::create([
                    'service_center_id' => $corporate['service_center_id'], 
                    'tech_id' => $user->id, 
                    'tech_ref_id' => $sc['reference_number'] . '-' . $next_tech_ref_id,
                ]);

            }

            return response(new UserResource($user), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::where('id', $id)->first();

        if ($user['role_id'] == 1) {
            // DB::enableQueryLog(); .id', 'desc')
            return UserResource::collection(
                DB::select("SELECT a.*, b.name, c.allowed_bm, c.allowed_sc, CONCAT(d.first_name, ' ', d.last_name) AS created_by, CONCAT(e.first_name, ' ', e.last_name) AS updated_by
                FROM users a
                INNER JOIN roles b ON b.id = a.role_id 
                LEFT JOIN user_restrictions c ON c.user_id = a.id
                LEFT JOIN users d ON d.id = a.created_by
                LEFT JOIN users e ON e.id = a.updated_by
                ORDER BY a.id DESC")
            ); 
            // return DB::getQueryLog(); 
        }else if ($user['role_id'] == 2) {
            return UserResource::collection(
               DB::select("SELECT a.*, h.tech_ref_id, b.name, d.id as service_center_id, d.name as service_center, concat(e.first_name, ' ', e.last_name) as branch_manager, CONCAT(f.first_name, ' ', f.last_name) as created_by, CONCAT(g.first_name, ' ', g.last_name) as updated_by
                    FROM users a
                    INNER JOIN roles b ON a.role_id = b.id
                    INNER JOIN (
                        SELECT user_id, branch_manager_id, service_center_id
                        FROM manage_users
                        WHERE corporate_manager_id = $id
                    ) c ON a.id = c.user_id
                    INNER JOIN service_centers d ON c.service_center_id = d.id
                    LEFT JOIN users e ON c.branch_manager_id = e.id
                    LEFT JOIN users f ON f.id = a.created_by
                    LEFT JOIN users g ON g.id = a.updated_by
                    LEFT JOIN tech_infos h ON h.tech_id = a.id
                    ORDER BY a.id DESC")
            ); 
        }
        else if ($user['role_id'] == 3) {
            return UserResource::collection(
               DB::select("SELECT a.*, h.tech_ref_id, b.name, CONCAT(f.first_name, ' ', f.last_name) as created_by, CONCAT(g.first_name, ' ', g.last_name) as updated_by
                    FROM users a
                    INNER JOIN roles b ON a.role_id = b.id
                    INNER JOIN (
                        SELECT user_id
                        FROM manage_users
                        WHERE branch_manager_id = $id
                    ) c ON a.id = c.user_id
                    LEFT JOIN users f ON f.id = a.created_by
                    LEFT JOIN users g ON g.id = a.updated_by
                    LEFT JOIN tech_infos h ON h.tech_id = a.id
                    ORDER BY a.id DESC")
            ); 
        }else if ($user['role_id'] == 4) {
            return UserResource::collection(
               DB::select("SELECT a.*, b.name
                    FROM users a
                    INNER JOIN roles b ON a.role_id = b.id
                    WHERE a.id = $id")
            );
        } 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $request->validated();

        $user = User::find($request->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;
        $user->role_id = $request->role_id;
        $user->updated_by = $request->user_id;
        $user->save(); 
        
        if ($request->user_role == 1) {
            $restriction = UserRestriction::where('user_id', $request->id)->first();
            $restriction->allowed_sc = $request->allowed_sc;
            $restriction->allowed_bm = $request->allowed_bm;
            $restriction->save();
        } else if ($request->user_role == 2) {
            $restriction = ManageUser::where('user_id', $request->id)->first();
            $restriction->corporate_manager_id = $request->user_id;
            $restriction->branch_manager_id = $request->branch_manager_id;
            $restriction->service_center_id = $request->service_center_id;
            $restriction->save();
        } else if ($request->user_role == 3) {
            // $restriction = ManageUser::where('user_id', $request->id)->first();
            // $restriction->branch_manager_id = $request->user_id;
            // $restriction->service_center_id = $request->service_center_id;
            // $restriction->save();
        }

        return response(new UserResource($user), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
