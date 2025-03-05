<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MatchesController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Matches";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('matches')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('matches')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('matches')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('matches')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        for($y = 0; $y<count($MyArray); $y++)
        {
            $MyArray[$y] = $this->GetReady($MyArray[$y]);
        }

        $TableView =  (string)view('reusable/matchesTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('matches', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        $MatchTypeId = $MyArray->MatchTypeId;

        if($MatchTypeId != null)
        {
            $MatchType = DB::table('matchstype')->where('Id', '=', $MatchTypeId)->first();

            if(app()->getLocale() == 'en')
            {
                $MyArray->MatchType = $MatchType->NameEn;

            } else
            {
                $MyArray->MatchType = $MatchType->NameAr;
            }

        } else
        {
            $MyArray->MatchType = '-';
        }

        $LeagueId = $MyArray->LeagueId;

        if($LeagueId != null)
        {
            $League = DB::table('leagues')->where('Id', '=', $LeagueId)->first();
            $MyArray->LeagueTopic = $League->Topic;

        } else
        {
            $MyArray->LeagueTopic = '-';
        }

        $StadiumId = $MyArray->StadiumId;

        if($StadiumId != null)
        {
            $Stadium = DB::table('stadiums')->where('Id', '=', $StadiumId)->first();
            $MyArray->StadiumName = $Stadium->Name;

        } else
        {
            $MyArray->StadiumName = '-';
        }

        $CommentatorId = $MyArray->CommentatorId;

        if($CommentatorId != null)
        {
            $Commentator = DB::table('commentators')->where('Id', '=', $CommentatorId)->first();
            $MyArray->CommentatorName = $Commentator->Name;

        } else
        {
            $MyArray->CommentatorName = '-';
        }

        $FirstTeamId = $MyArray->FirstTeamId;

        if($FirstTeamId != null)
        {
            $FirstTeam = DB::table('teams')->where('Id', '=', $FirstTeamId)->first();

            if(app()->getLocale() == 'en')
            {
                $MyArray->FirstTeamName = $FirstTeam->NameEn;

            } else
            {
                $MyArray->FirstTeamName = $FirstTeam->NameAr;
            }

        } else
        {
            $MyArray->FirstTeamName = '-';
        }

        $SecondTeamId = $MyArray->SecondTeamId;

        if($SecondTeamId != null)
        {
            $SecondTeam = DB::table('teams')->where('Id', '=', $SecondTeamId)->first();

            if(app()->getLocale() == 'en')
            {
                $MyArray->SecondTeamName = $SecondTeam->NameEn;

            } else
            {
                $MyArray->SecondTeamName = $SecondTeam->NameAr;
            }

        } else
        {
            $MyArray->SecondTeamName = '-';
        }

        return $MyArray;
    }

    public function edit(Request $request)
    {
        $CheckTopic = DB::table('matches')->where('Topic', '=', $request->Topic)->where('Id', '!=', $request->Id)->first();

        if($CheckTopic)
        {
            $rules = [
                'Topic' => 'max:2',
            ];

            $messages = [
                'Topic.max' => __('messages.LeagueMatchIsExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_match', 1);
            }
        }

        $Data = [
            'Topic' => $request->Topic,
            'Location' => $request->Location,
            'MatchDate' => $request->MatchDate,
            'Status' => $request->Status
        ];

        DB::table('matches')
            ->where('Id', $request->Id)
            ->update($Data);

        return redirect()->back()->with('error_edit_matches', 3);
    }


    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('matches')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Topic', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/matchesTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['MatchId']))
        {
            $MatchId= $_COOKIE['MatchId'];

            $Match = DB::table('matches')->find($MatchId);

            GlobalVariables::$SideMenuSelected = "Matches";

            $Match = $this->GetReady($Match);

            return view('Details/matchDetail', ['MyArray' => $Match]);
        }
    }

    public function GetMatchInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('matches')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
