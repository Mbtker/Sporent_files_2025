<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RefereesController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Referees";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('referees')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('referees')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('referees')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('referees')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $GetReady = $this->GetReady($MyArray);

        $TableView =  (string)view('reusable/refereesTable', ['MyArray' => $GetReady, 'iSFromSearch' => false]);

        return view('referees', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        for($y = 0; $y<count($MyArray); $y++)
        {
            $MyArray[$y]->Name = $MyArray[$y]->FirstName . ' ' . $MyArray[$y]->LastName;

            if($MyArray[$y]->EPaymentInfoId != null)
            {
                $MyArray[$y]->EPaymentAvailable =  __('messages.Available');

            } else
            {
                $MyArray[$y]->EPaymentAvailable =  __('messages.Unavailable');
            }
        }

        return $MyArray;
    }

    public function edit(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('referees', $request->Phone);

        $iSExistsInThisTable = DB::table('referees')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_referees', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('referees', $request->Email);

        $CheckEmailInThisTable = DB::table('referees')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_referees', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Status' => $request->Status
        ];

        DB::table('referees')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_referees', 3);
    }

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('referees')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/refereesTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['RefereeId']))
        {
            $RefereeId = $_COOKIE['RefereeId'];

            $Referee = DB::table('referees')->find($RefereeId);

            GlobalVariables::$SideMenuSelected = "Referees";

            return view('Details/refereeDetail', ['User' => $Referee]);
        }
    }

    public function GetRefereeInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('referees')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
