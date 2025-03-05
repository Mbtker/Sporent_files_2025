<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdvertisingAgenciesController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "AdvertisingAgencies";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('advertisingagencies')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('advertisingagencies')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('advertisingagencies')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('advertisingagencies')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $TableView =  (string)view('reusable/advertisingAgenciesTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('advertisingAgencies', ['TableView' => $TableView]);
    }

    public function edit(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('advertisingagencies', $request->Phone);

        $iSExistsInThisTable = DB::table('advertisingagencies')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_advertising_agencies', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('advertisingagencies', $request->Email);

        $CheckEmailInThisTable = DB::table('advertisingagencies')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_advertising_agencies', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Status' => $request->Status
        ];

        DB::table('advertisingagencies')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_advertising_agencies', 3);
    }

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('advertisingagencies')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            return (string)view('reusable/advertisingAgenciesTable', ['MyArray' => $MyArray, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['AgencyId']))
        {
            $AgencyId = $_COOKIE['AgencyId'];

            $Agency = DB::table('advertisingagencies')->find($AgencyId);

            GlobalVariables::$SideMenuSelected = "AdvertisingAgencies";

            return view('Details/advertisingAgenciesDetail', ['MyArray' => $Agency]);
        }
    }

    public function GetAgencyInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('advertisingagencies')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
