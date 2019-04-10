<?php

namespace App\Modules\User\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Image;

class UserController extends Controller
{

    public function userSignup(Request $request)
    {
        if (Session::has('userDetails')) {
            return redirect('user-dashboard');
        } else


            if ($request->isMethod('post')) {

                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'username' => 'required',
                    'first_name' => 'required',
                    'file' =>'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
                    'password' => 'required|min:3',
                    'confirm_password' => 'required|same:password',
                ]);
                if ($validator->fails()) {
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                } else {
                    try {

//                        $edate=strtotime($_POST['edate']);
//                        $edate=date("Y-m-d",$edate);


                        $obj = new User();
                        $file = $request->file('file');

                        //you also need to keep file extension as well
                        $name = $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();
                        //using the array instead of object
                        $thumb_img = \Intervention\Image\Facades\Image::make($file->getRealPath())->resize(100, 100);
//                        $img = Image::make('public/foo.jpg')->resize(320, 240)->insert('public/watermark.png');
                        $image['filePath'] = $name;
                        $file->move(public_path() . '/uploads/', $name);

                        $obj->username = $request->all()['email'];
                        $obj->first_name = $request->all()['email'];
                        $obj->last_name = $request->all()['email'];
                        $obj->email = $request->all()['email'];
                        $obj->password = Hash::make($request->all()['password']);
                        $obj->office_address = $request->all()['office_address'];
                        $obj->residence_address = $request->all()['residence_address'];
                        $obj->profile_image = $name;
                        $obj->user_role = 1;
                        $obj->user_status = 'active';
                        $obj->save();
                        return redirect('/user-signin')->with('status', 'Inserted Successfully');

                    } catch (\Exception $e) {
                        return $e->getMessage();
                    }
                }
            }

        return view('User::userSignup');

    }


    public function userSignin(Request $request)
    {
        if (Session::has('userDetails')) {
            return redirect('user-dashboard');
        } else
            if ($request->isMethod('post')) {

                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                ]);
                if ($validator->fails()) {
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                } else {
                    try {
                        $email = $request->all()['email'];
                        $password = $request->all()['password'];
                        if (Auth::attempt(['email' => $email, 'password' => $password])) {
                            $userdata = json_decode(Auth::user(), true);

                            Session::put('userDetails', $userdata);
                            if ($userdata['user_role'] == 1) {      //1 is for user and 0 is for admin
                                return redirect('/user-dashboard');
                            } else
                                return redirect('/adminDashboard');

                        } else {
                            return back()->with('status','Creds not matched');
                        }
                    } catch (\Exception $e) {
                        return $e->getMessage();
                    }

                }
            }
        return view('User::userSignin');
    }


    public function userDashboard()
    {
        if (Session::has('userDetails')) {
            $obj = new User();
            $userDetails = json_decode(json_encode($obj::all(), true), true);
            return view('User::userDashboard', ['userDetails' => $userDetails]);
        } else
            return redirect('/user-signup');
    }


    public function editUserProfile(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'username' => 'required',
                'first_name' => 'required',
                'file' => 'required',

            ]);
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $obj = new User();
            $obj->username = $request->all()['email'];
            $obj->first_name = $request->all()['email'];
            $obj->last_name = $request->all()['email'];
            $obj->email = $request->all()['email'];
            $obj->office_address = $request->all()['office_address'];
            $obj->residence_address = $request->all()['residence_address'];

//            if (isset($request->all()['file'])) {
            $file = $request->file('file');
            $name = $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();
            $image['filePath'] = $name;
            $file->move(public_path() . '/uploads/', $name);
            $obj->profile_image = $name;
//            }
            try {
                $user = User::where("id", $id);
                $new_user_data = [];
                $new_user_data['username'] = $request->all()['username'];
                $new_user_data['first_name'] = $request->all()['first_name'];
                $new_user_data['last_name'] = $request->all()['last_name'];
                $new_user_data['email'] = $request->all()['email'];
                $new_user_data['office_address'] = $request->all()['office_address'];
                $new_user_data['residence_address'] = $request->all()['residence_address'];
                $new_user_data['profile_image'] = $name;

                $updateData = $obj::where('id', $id)->update($new_user_data);
                if ($updateData) {
                    return redirect('/user-dashboard');
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }

        }
        $obj = new User();
        $userDetails = json_decode(json_encode($obj::all(), true), true);
        return view('User::editProfile', ['userDetails' => $userDetails]);

    }

    public function logout()
    {

        session()->forget('userDetails');
        session()->flush();
        return redirect('/user-signin');
    }

}
