<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeleteUserDataController extends Controller
{
    public function show()
    {
        return view('deleteYourDataEn');
    }

    public function showAr() {

        return view('deleteYourData');
    }

    public function SendDeleteUserDataRequest(Request $request)
    {
        $rules = [
            'Name' => ['required'],
            'Phone' => ['required']
        ];

        $messages = [
            'Name.required' => 'The full name is required!',
            'Phone.required' => 'The phone is required!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator -> fails())
        {
            return redirect()->back();
        }

        $Check = DB::table('deleteuserdatarequest')
            ->where('Phone','=' , $request->Phone)
            ->where('ApprovedBy','=' , 0)
            ->first();

        if ($Check) {
            return redirect()->back()->with('error_send', 3);
        } else {
            DB::table('deleteuserdatarequest')->insert([
                'FullName' => $request->Name,
                'Phone' => $request->Phone,
                'Email' => $request->Email
            ]);
            return redirect()->back()->with('send_successfully', 2);
        }
    }
}
