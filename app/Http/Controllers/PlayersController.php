<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlayersController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Players";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('players')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('players')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('players')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('players')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $GetReady = $this->GetReady($MyArray);

        $TableView =  (string)view('reusable/playersTable', ['MyArray' => $GetReady, 'iSFromSearch' => false]);

        return view('players', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        for($y = 0; $y<count($MyArray); $y++)
        {
            $TeamId = $MyArray[$y]->TeamId;

            $MyArray[$y]->Name = $MyArray[$y]->FirstName . ' ' . $MyArray[$y]->LastName;

            if($TeamId != null)
            {
                $Team = DB::table('teams')->where('Id', '=', $TeamId)->first();

                if(app()->getLocale() == 'en')
                {
                    $MyArray[$y]->TeamName = $Team->NameEn;

                } else
                {
                    $MyArray[$y]->TeamName = $Team->NameAr;
                }

            } else
            {
                $MyArray[$y]->TeamName = '-';
            }

            $PositionId = $MyArray[$y]->PositionId;

            if($PositionId != null)
            {
                $Position = DB::table('playerposition')->where('Id', '=', $PositionId)->first();

                if(app()->getLocale() == 'en')
                {
                    $MyArray[$y]->PositionName = $Position->PositionEn;

                } else
                {
                    $MyArray[$y]->PositionName = $Position->PositionAr;
                }

            } else
            {
                $MyArray[$y]->PositionName = '-';
            }
        }

        return $MyArray;
    }

    public function edit(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('players', $request->Phone);

        $iSExistsInThisTable = DB::table('players')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_players', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('players', $request->Email);

        $CheckEmailInThisTable = DB::table('players')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_players', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Status' => $request->Status
        ];

        DB::table('players')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_players', 3);
    }


    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('players')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/supervisorsTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['PlayerId']))
        {
            $PlayerId = $_COOKIE['PlayerId'];

            $Player = DB::table('players')->find($PlayerId);

            GlobalVariables::$SideMenuSelected = "Players";

            return view('Details/playersDetail', ['User' => $Player]);
        }
    }

    public function GetPlayerInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('players')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
