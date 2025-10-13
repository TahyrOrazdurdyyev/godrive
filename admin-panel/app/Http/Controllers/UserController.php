<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;

class UserController extends Controller
{

     public function __construct()
    {
       $this->middleware('auth'); 
    }


	  public function index()
    {

        return view("users.index");
       
    }


  public function edit($id)
  {
      return view('users.edit')->with('id',$id);
  }

  public function profile()
  {
      $user = Auth::user();
   
      return view('users.profile', compact(['user']));
  }

  public function update(Request $request,$id){

    $name = $request->input('name');
    $password = $request->input('password');
    $old_password = $request->input('old_password');
    $email = $request->input('email');
    
    if($password == ''){
        $validator = Validator::make($request->all(), [
    		'name' => 'required|max:255',
            'email'=>'required|email'
		]);   
    }else{
        $user = Auth::user();
        if(password_verify($old_password,$user->password)){
          $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'email'=>'required|email'
          ]);

        }else{
            return Redirect()->back()->with(['message' => "Please enter correct old password"]);
        }
     
    }

    if ($validator->fails()) {
      $error = $validator->errors()->first();
      return Redirect()->back()->with(['message' => $error]);
    } 

    $user = User::find($id);
    if($user) {
      $user->name = $name;
      $user->email = $email;
      if($password != ''){
          $user->password = Hash::make($password);
      }
      $user->save();
    }

    return redirect()->back();
  }

  public function view($id){
    return view('users.view', compact('id'));
  }

  public function getUsersList(Request $request)
  {
      try {
          $status = $request->input('status', 'all');

          $query = Customer::query();

          if ($status === 'active') {
              $query->where('is_active', 1);
          } elseif ($status === 'inactive') {
              $query->where('is_active', 0);
          }

          $users = $query->orderBy('created_at', 'desc')->get();

          return response()->json([
              'success' => true,
              'users' => $users
          ]);
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => $e->getMessage()
          ], 500);
      }
  }

  public function toggleStatus(Request $request, $id)
  {
      try {
          $user = Customer::find($id);

          if (!$user) {
              return response()->json([
                  'success' => false,
                  'message' => 'User not found'
              ], 404);
          }

          $user->is_active = $request->input('is_active') ? 1 : 0;
          $user->save();

          return response()->json([
              'success' => true,
              'message' => 'Status updated successfully'
          ]);
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => 'Error: ' . $e->getMessage()
          ], 500);
      }
  }
}
