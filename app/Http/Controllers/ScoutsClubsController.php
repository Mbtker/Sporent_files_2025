<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ScoutsClubsController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "ScoutsClubs";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('scoutsofclubs')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('scoutsofclubs')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('scoutsofclubs')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('scoutsofclubs')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        for($y = 0; $y<count($MyArray); $y++)
        {
            $MyArray[$y] = $this->GetReady($MyArray[$y]);
        }

        $TableView =  (string)view('reusable/scoutsClubsTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('scoutsClubs', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        $TeamId = $MyArray->TeamId;

        if($TeamId != null)
        {
            $Team = DB::table('teams')->where('Id', '=', $TeamId)->first();

            if(app()->getLocale() == 'en')
            {
                $MyArray->TeamName = $Team->NameEn;

            } else
            {
                $MyArray->TeamName = $Team->NameAr;
            }

        } else
        {
            $MyArray->TeamName = '-';
        }

        return $MyArray;
    }

    public function edit(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('scoutsofclubs', $request->Phone);

        $iSExistsInThisTable = DB::table('scoutsofclubs')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_scouts_clubs', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('scoutsofclubs', $request->Email);

        $CheckEmailInThisTable = DB::table('scoutsofclubs')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_scouts_clubs', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Status' => $request->Status
        ];

        DB::table('scoutsofclubs')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_scouts_clubs', 3);
    }


    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('scoutsofclubs')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/scoutsClubsTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['ScoutClubsId']))
        {
            $ScoutClubsId = $_COOKIE['ScoutClubsId'];

            $ScoutClubs = DB::table('scoutsofclubs')->find($ScoutClubsId);

            GlobalVariables::$SideMenuSelected = "ScoutsClubs";

            $ScoutClubs = $this->GetReady($ScoutClubs);

            return view('Details/scoutClubsDetail', ['User' => $ScoutClubs]);
        }
    }

    public function GetScoutClubsInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('scoutsofclubs')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
