<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function show($id) {
        return RoleResource::collection(
            DB::select("SELECT *
                FROM roles
                WHERE FIND_IN_SET(
                    id, 
                    (SELECT b.role_access
                        FROM users a
                        INNER JOIN roles b ON a.role_id = b.id WHERE a.id = $id))
            ")
         ); 
    }
}
