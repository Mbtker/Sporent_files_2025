<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StadiumsController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Stadiums";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('stadiums')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('stadiums')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('stadiums')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('stadiums')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $TableView =  (string)view('reusable/stadiumsTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('stadiums', ['TableView' => $TableView] );
    }

    public function edit(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('stadiums', $request->Phone);

        $iSExistsInThisTable = DB::table('stadiums')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

        if($CheckPhone || $iSExistsInThisTable)
        {
            $rules = [
                'Phone' => 'max:2',
            ];

            $messages = [
                'Phone.max' => __('messages.PhoneAlreadyExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_stadiums', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('stadiums', $request->Email);

        $CheckEmailInThisTable = DB::table('stadiums')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

        if($CheckEmail || $CheckEmailInThisTable)
        {
            $rules = [
                'Email' => 'max:2',
            ];

            $messages = [
                'Email.max' => __('messages.EmailAlreadyExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_stadiums', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Status' => $request->Status
        ];

        DB::table('stadiums')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_stadiums', 3);
    }

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('stadiums')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            return (string)view('reusable/stadiumsTable', ['MyArray' => $MyArray, 'iSFromSearch' => true]);
        }
    }


    public function Details()
    {
        if (isset($_COOKIE['StadiumId']))
        {
            $StadiumId = $_COOKIE['StadiumId'];

            $Stadium = DB::table('stadiums')->find($StadiumId);

            GlobalVariables::$SideMenuSelected = "Stadiums";

            return view('Details/stadiumDetail', ['MyArray' => $Stadium]);
        }
    }

    public function GetStadiumInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('stadiums')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
