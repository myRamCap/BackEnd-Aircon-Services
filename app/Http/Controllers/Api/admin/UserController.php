<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\ManageUser;
use App\Models\User;
use App\Models\UserRestriction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
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
            

            if ($request->user_role == 1) { 
                $user = User::create($data);
                UserRestriction::create([
                    'user_id' => $user->id,
                    'allowed_sc' => $request->allowed_sc,
                    'allowed_bm' => $request->allowed_bm
                ]);
            } else if ($request->user_role == 2) {
                $corporate_manager = ManageUser::join('users', 'users.id', '=', 'manage_users.user_id')
                                ->where('manage_users.corporate_manager_id', $request->user_id)     
                                ->where('manage_users.branch_manager_id', 0)
                                ->count();
                $restriction = UserRestriction::where('user_id', $request->user_id)->first();
                $restriction_count = $restriction['allowed_bm'] ?? 0;
    

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
                
            } else if ($request->user_role == 3) {
                $corporate = ManageUser::where('user_id', $request->user_id)->first();
                $user = User::create($data);
                ManageUser::create([
                    'user_id' => $user->id,
                    'service_center_id' => $corporate['service_center_id'],
                    'corporate_manager_id' => $corporate['corporate_manager_id'],
                    'branch_manager_id' => $request->user_id    
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

        if ($user['role_id'] === 1) {
            // DB::enableQueryLog();
            return UserResource::collection(
                User::join('roles', 'roles.id', '=', 'users.role_id')
                ->join('user_restrictions', 'user_restrictions.user_id', '=', 'users.id')
                ->select('users.*', 'roles.name',  'user_restrictions.allowed_bm', 'user_restrictions.allowed_sc')
                ->where('users.role_id', '=', 2)
                ->orderBy('users.id','desc')->get()
            ); 
            // return DB::getQueryLog();
        }else if ($user['role_id'] == 2) {
            return UserResource::collection(
               DB::select("SELECT a.*, b.name, d.id as service_center_id, d.name as service_center, concat(e.first_name, ' ', e.last_name) as branch_manager
                    FROM users a
                    INNER JOIN roles b ON a.role_id = b.id
                    INNER JOIN (
                        SELECT user_id, branch_manager_id, service_center_id
                        FROM manage_users
                        WHERE corporate_manager_id = $id
                    ) c ON a.id = c.user_id
                    INNER JOIN service_centers d ON c.service_center_id = d.id
                    LEFT JOIN users e ON c.branch_manager_id = e.id")
            ); 
        }
        else if ($user['role_id'] == 3) {
            return UserResource::collection(
               DB::select("SELECT a.*, b.name
                    FROM users a
                    INNER JOIN roles b ON a.role_id = b.id
                    INNER JOIN (
                        SELECT user_id
                        FROM manage_users
                        WHERE branch_manager_id = $id
                    ) c ON a.id = c.user_id")
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
