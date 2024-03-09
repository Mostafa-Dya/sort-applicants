<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permissions;

use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    // Register Api (Post, Formdata)
    public function register(Request $request){

        $request->validate([
            "name"  =>  "required",
            "email" =>  "required|string|unique:users",
            "password"  =>  "required|confirmed"
        ]);
        // Save Data in database
        $user = User::create([
            "name" => $request->name,
            "email"=> $request->email,
            "password" => Hash::make($request->password)
        ]);

        Permissions::create([
            'user_id' => $user->id,

            'statusCheck' => false,
            'sortable' => false,

            'canActive'=>true,

            'addApplicants' => false,
            'addCertificate' => false,
            'addJobDescription' => false,
            'addGovernorate' => false,
            'addPublic' => false,

            'editApplicants' => false,
            'editCertificate' => false,
            'editJobDescription' => false,
            'editGovernorate' => false,
            'editPublic' => false,

            'deleteApplicants' => false,
            'deleteCertificate' => false,
            'deleteJobDescription' => false,
            'deleteGovernorate' => false,
            'deletePublic' => false,
        ]);

        
        return response()->json([
            "status" => true,
            "message" => "User registered successfully"
        ]);
    }

    // login Api (Post, Formdata)
    public function login(Request $request){
        // Data validation
        $request->validate([
            "email" =>  "required|string",
            "password"  =>  "required"
        ]);
    
        $user = User::where("email", $request->email)->first();
    
        if (!empty($user) && Hash::check($request->password, $user->password)) {
            // Check if the user is an admin
            if ($user->role === 'Admin') {
                // Admin is allowed to log in without further checks
                $token = $user->createToken("userToken")->plainTextToken;
    
                return response()->json([
                    "status" => true,
                    "message" => "Login successful",
                    "role" => $user->role,
                    "name" => $user->name,
                    "token" => $token
                ]);
            }
    
            // For non-admin users, continue with permission checks
            $permissions = $user->permissions;
    
            if (!$permissions || !$permissions->canActive) {
                return response()->json([
                    "status" => false,
                    "message" => "Account deactivated"
                ]);
            }
    
            // Account is active, proceed with login
            $token = $user->createToken("userToken")->plainTextToken;
    
            return response()->json([
                "status" => true,
                "message" => "Login successful",
                "role" => $user->role,
                "name" => $user->name,
                "permissions" => $permissions, 
                "token" => $token
            ]);
        }
    
        return response()->json([
            "status" => false,
            "message" => "Invalid login details"
        ]);
    }
    

    public function profile(){

        $data = auth()->user();

        return response()->json([
            "status"=>true,
            "message"=>"Profile Data",
            "user"=>$data
        ]);
    }

    // logout Api (Get)
    public function logout(){
        
        // auth()->user()->tokens()->delete();
        return response()->json([
            "status"=>true,
            "message"=>"User Logged out"
        ]);
    }

    public function getUsers() {
        // Retrieve all users from the database
        $users = User::with('permissions')->get();
    
        // Check if any users were found
        if ($users->isEmpty()) {
            return response()->json([
                "status" => false,
                "message" => "No users found"
            ], 404);
        }
    
        // Return the list of users with their permissions
        return response()->json([
            "status" => true,
            "message" => "Users retrieved successfully",
            "users" => $users
        ]);
    }
    

    public function updatePermissions(Request $request, $userId) {

    // Find the user by ID
    $user = User::find($userId);

    // Check if the user exists
    if (!$user) {
        return response()->json([
            "status" => false,
            "message" => "User not found"
        ], 404);
    }

    // Check if the user has permissions
    if (!$user->permissions) {
        return response()->json([
            "status" => false,
            "message" => "User permissions not found"
        ], 404);
    }

        // Validate request data
        $request->validate([
            'statusCheck' => 'boolean',
            'sortable' => 'boolean',
            'addApplicants' => 'boolean',
            'addCertificate' => 'boolean',
            'addJobDescription' => 'boolean',
            'addGovernorate' => 'boolean',
            'addPublic' => 'boolean',
            'editApplicants' => 'boolean',
            'editCertificate' => 'boolean',
            'editJobDescription' => 'boolean',
            'editGovernorate' => 'boolean',
            'editPublic' => 'boolean',
            'deleteApplicants' => 'boolean',
            'deleteCertificate' => 'boolean',
            'deleteJobDescription' => 'boolean',
            'deleteGovernorate' => 'boolean',
            'deletePublic' => 'boolean',
        ]);

        // Update permissions for the user
        $user->permissions()->update([
            'statusCheck' => $request->input('statusCheck', false),
            'sortable' => $request->input('sortable', false),
            'addApplicants' => $request->input('addApplicants', false),
            'addCertificate' => $request->input('addCertificate', false),
            'addJobDescription' => $request->input('addJobDescription', false),
            'addGovernorate' => $request->input('addGovernorate', false),
            'addPublic' => $request->input('addPublic', false),
            'editApplicants' => $request->input('editApplicants', false),
            'editCertificate' => $request->input('editCertificate', false),
            'editJobDescription' => $request->input('editJobDescription', false),
            'editGovernorate' => $request->input('editGovernorate', false),
            'editPublic' => $request->input('editPublic', false),
            'deleteApplicants' => $request->input('deleteApplicants', false),
            'deleteCertificate' => $request->input('deleteCertificate', false),
            'deleteJobDescription' => $request->input('deleteJobDescription', false),
            'deleteGovernorate' => $request->input('deleteGovernorate', false),
            'deletePublic' => $request->input('deletePublic', false),
        ]);

        return response()->json([
            "status" => true,
            "message" => "Permissions updated successfully"
        ]);
    }

    public function getUserById($userId) {
        // Find the user by ID
        $user = User::with('permissions')->find($userId);
    
        // Check if the user exists
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "User not found"
            ], 404);
        }
    
        // Return the user with their permissions
        return response()->json([
            "status" => true,
            "message" => "User retrieved successfully",
            "user" => $user
        ]);
    }
    

    public function deleteUser($userId) {
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "User not found"
            ], 404);
        }
    
        // Check if the authenticated user has permission to delete users
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized: Only admins can delete users"
            ], 401);
        }
    
        // Delete the user
        $user->delete();
    
        return response()->json([
            "status" => true,
            "message" => "User deleted successfully"
        ]);
    }

    public function updateCanActive(Request $request, $userId) {
        // Find the user by ID
        $user = User::find($userId);
    
        // Check if the user exists
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "User not found"
            ], 404);
        }
    
        // Check if the user has permissions
        if (!$user->permissions) {
            return response()->json([
                "status" => false,
                "message" => "User permissions not found"
            ], 404);
        }
    
        // Validate request data
        $request->validate([
            'canActive' => 'required|boolean',
        ]);
    
        // Update the canActive permission for the user
        $user->permissions()->update([
            'canActive' => $request->input('canActive'),
        ]);
    
        return response()->json([
            "status" => true,
            "message" => "CanActive permission updated successfully"
        ]);
    }
}


