<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamsController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Teams";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('teams')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('teams')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('teams')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('teams')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $GetReady = $this->GetReady($MyArray);

        $TableView =  (string)view('reusable/teamsTable', ['MyArray' => $GetReady, 'iSFromSearch' => false]);

        return view('teams', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        for($y = 0; $y<count($MyArray); $y++)
        {
            $CaptainId = $MyArray[$y]->CaptainId;

            if($CaptainId != null)
            {
                $Captain = DB::table('players')->where('Id', '=', $CaptainId)->first();
                $MyArray[$y]->CaptainName = $Captain->FirstName . ' ' . $Captain->LastName;

            } else
            {
                $MyArray[$y]->CaptainName = '-';
            }

            $TeamLeaderId = $MyArray[$y]->TeamLeaderId;

            if($TeamLeaderId != null)
            {
                $TeamLeader = DB::table('players')->where('Id', '=', $TeamLeaderId)->first();
                $MyArray[$y]->TeamLeader = $TeamLeader->FirstName . ' ' . $TeamLeader->LastName;

            } else
            {
                $MyArray[$y]->TeamLeader = '-';
            }

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

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('teams')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/teamsTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['TeamId']))
        {
            $TeamId = $_COOKIE['TeamId'];

            $Team = DB::table('teams')->find($TeamId);

            $CaptainId = $Team->CaptainId;

            if($CaptainId != null)
            {
                $Captain = DB::table('players')->where('Id', '=', $CaptainId)->first();
                $Team->CaptainName = $Captain->Name;
                $Team->LastActive = $Captain->LastActive;
                $Team->TokenId = $Captain->TokenId;
                $Team->CaptainPhone = $Captain->Phone;

            } else
            {
                $Team->CaptainName = '-';
            }

            GlobalVariables::$SideMenuSelected = "Teams";

            return view('Details/teamDetail', ['Team' => $Team]);
        }
    }
}
