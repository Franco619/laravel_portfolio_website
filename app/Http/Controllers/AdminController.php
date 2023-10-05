<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



use Illuminate\Http\Request;

class AdminController extends Controller

{

    
    public function HomeMain(){
        return view('admin.frontend.index');
    }// end mehtod 

      public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

           $notification = array(
            'message' => 'User Logout  Successfully', 
            'alert-type' => 'success'
        );

        return redirect('/login')->with($notification);
    }//end method

    public function Profile(){
        $id = Auth::user()->id;
        $adminData = User::find($id);

        return view('admin.admin_profile_view', compact('adminData'));
    }//end method 

    public function EditProfile(){
        $id = Auth::user()->id;
        $editData = User::find($id);

        return view('admin.admin_profile_edit', compact('editData'));
    }//end method

    public function StoreProfile(Request $request){
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->username = $request->username;

        if ($request->file('profile_image')) {
           $file = $request->file('profile_image');

           $filename = date('YmdHi').$file->getClientOriginalName();
           $file->move(public_path('upload/admin_images'),$filename);
           $data['profile_image'] = $filename;
        }
        $data->save();

            $notification = array(
            'message' => 'Admin Profile Updated Successfully', 
            'alert-type' => 'info'
        );

        return redirect()->route('admin.profile')->with($notification);
    }//end method

    public function ChangePassword(){

        return view('admin.admin_change_password');
    }//end method 

    
    public function UpdatePassword(Request $request){
   $validateData = $request->validate([
    'oldpassword'=>'required',
    'newpassword'=>'required',
    'confirm_password'=>'required|same:newpassword',
   ]);

   $hashedPassword = Auth::user()->password;
   if(Hash::check($request->oldpassword, $hashedPassword)){
    $users = User::find(Auth::id());
    $users->password = bcrypt($request->newpassword);
    $users->save();

    session()->flash('message', 'Password Updated Successfully');
    return redirect()->back();
   }else{
    session()->flash('message' , 'old password does not match');
    return redirect()->back();
   }
      


    }// End Method
}
