<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeaguesController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Leagues";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('leagues')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('leagues')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('leagues')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('leagues')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $GetReady = $this->GetReady($MyArray);

        $TableView =  (string)view('reusable/leaguesTable', ['MyArray' => $GetReady, 'iSFromSearch' => false]);

        return view('leagues', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        for($y = 0; $y<count($MyArray); $y++)
        {
            $StadiumId = $MyArray[$y]->StadiumId;

            if($StadiumId != null)
            {
                $Stadium = DB::table('stadiums')->where('Id', '=', $StadiumId)->first();
                $MyArray[$y]->StadiumName = $Stadium->Name;

            } else
            {
                $MyArray[$y]->StadiumName = '-';
            }
        }

        return $MyArray;
    }

    public function edit(Request $request)
    {
        $CheckTopic = DB::table('leagues')->where('Topic', '=', $request->Topic)->where('Id', '!=', $request->Id)->first();

        if($CheckTopic)
        {
            $rules = [
                'Topic' => 'max:2',
            ];

            $messages = [
                'Topic.max' => __('messages.LeagueTopicIsExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_leagues', 1);
            }
        }

        $Data = [
            'Topic' => $request->Topic,
            'Location' => $request->Location,
            'Fee' => $request->Fee,
            'Status' => $request->Status
        ];

        DB::table('leagues')
            ->where('Id', $request->Id)
            ->update($Data);

        return redirect()->back()->with('error_edit_leagues', 3);
    }


    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('leagues')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Topic', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/leaguesTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['LeagueId']))
        {
            $LeagueId = $_COOKIE['LeagueId'];

            $League = DB::table('leagues')->find($LeagueId);

            GlobalVariables::$SideMenuSelected = "Leagues";

            $StadiumId = $League->StadiumId;

            if($StadiumId != null)
            {
                $Stadium = DB::table('stadiums')->where('Id', '=', $StadiumId)->first();
                $League->StadiumName = $Stadium->Name;

            } else
            {
                $League->StadiumName = '-';
            }

            return view('Details/leaguesDetail', ['MyArray' => $League]);
        }
    }

    public function GetLeaguesInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('leagues')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
