<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\APIs\LeaguePageAPIs\FirebaseController;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchesController extends Controller
{
    use GeneralTrait;

    public function GetMangeMatches(Request $request)
    {
        $GetTime = $request->Time;
        $GetLeagueId = $request->LeagueId;
        $GetChampionshipId = $request->ChampionshipId;

		// $GetDate = Carbon::now()->format('Y-m-d G:i A');

        $GetDate = Carbon::now()->format('Y-m-d');

        if ($GetChampionshipId != 0) {
            $Array = DB::table('matches')->where('ChampionshipId','=' , $GetChampionshipId)->orderByRaw('MatchDate ASC')->get();
        } else if($GetLeagueId == 0)
        {
            if($GetTime == "Now")
            {
                $Array = DB::table('matches')->whereDate('MatchDate','=' , $GetDate)->orderByRaw('MatchDate ASC')->get();

            } else if($GetTime == "Upcoming")
            {
                $Array = DB::table('matches')->whereDate('MatchDate','>' , $GetDate)->orderByRaw('MatchDate ASC')->get();

            } else
            {
                $Array = DB::table('matches')->whereDate('MatchDate','<' , $GetDate)->orderByRaw('MatchDate ASC')->get();
            }
        } else
        {
            $Array = DB::table('matches')->where('LeagueId','=' , $GetLeagueId)->orderByRaw('MatchDate ASC')->get();
        }

        for($y = 0; $y<count($Array); $y++)
        {
            $Array[$y] = $this->GetMatchReady($Array[$y]);
        }

        $ResultSorted = collect($Array)->sortBy('MatchDate')->toArray();

        $FinalResultSorted = [];

        foreach($ResultSorted as $Object) {
            $FinalResultSorted[] = $Object;
        }

        return $this->returnDate('Data', $FinalResultSorted);
    }

    static function GetMatchReady($Match)
    {
        $MatchTypeId = $Match->MatchTypeId;
        if($MatchTypeId != null && $MatchTypeId != 0)
        {
            $Match->MatchType = DB::table('matchstype')->where('Id', '=', $MatchTypeId)->first();
        } else
        {
            $Match->MatchType = null;
        }

        $LeagueId = $Match->LeagueId;
        if($LeagueId != 0)
        {
            $Match->League = DB::table('leagues')->where('Id', '=', $LeagueId)->first();
        } else
        {
            $Match->League = null;
        }

        $StadiumId = $Match->StadiumId;
        if($StadiumId != 0)
        {
            $Match->Stadium = DB::table('stadiums')->where('Id', '=', $StadiumId)->first();
        } else
        {
            $Match->Stadium = null;
        }

        $RefereeInfo = DB::table('matchstaff')->where('MatchId', '=', $Match->Id)->where('UserAccountTypeId', '=', 3)->first();
        if($RefereeInfo)
        {
            $Match->Referee = DB::table('referees')->where('Id', '=', $RefereeInfo->UserId)->first();
        } else
        {
            $Match->Referee = null;
        }

        $CommentatorInfo = DB::table('matchstaff')->where('MatchId', '=', $Match->Id)->where('UserAccountTypeId', '=', 4)->first();
        if($CommentatorInfo)
        {
            $Match->Commentator = DB::table('commentators')->where('Id', '=', $CommentatorInfo->UserId)->first();
        } else
        {
            $Match->Commentator = null;
        }

        $FirstTeamId = $Match->FirstTeamId;
        if($FirstTeamId != 0)
        {
            $Match->FirstTeam = DB::table('teams')->where('Id', '=', $FirstTeamId)->first();
            $ContGol = DB::table('matchdetails')
                ->where('MatchId', '=', $Match->Id)
                ->where('TeamId', '=', $FirstTeamId)
                ->where('Type', '=', 'Goal')
                ->count();
            $Match->FirstTeam->GoalCount = $ContGol;

            $FirstGoalPenaltyShootouts = DB::table('matchdetails')
                ->where('matchId', '=', $Match->Id)
                ->where('TeamId', '=', $FirstTeamId)
                ->where('Type', '=', 'GoalPenaltyShootouts')
                ->count();

            $Match->FirstTeam->GoalPenaltyShootouts = $FirstGoalPenaltyShootouts;

        } else
        {
            $Match->FirstTeam = null;
        }

        $SecondTeamId = $Match->SecondTeamId;
        if($SecondTeamId != 0)
        {
            $Match->SecondTeam = DB::table('teams')->where('Id', '=', $SecondTeamId)->first();
            $ContGol = DB::table('matchdetails')
                ->where('MatchId', '=', $Match->Id)
                ->where('TeamId', '=', $SecondTeamId)
                ->where('Type', '=', 'Goal')
                ->count();

            $Match->SecondTeam->GoalCount = $ContGol;

            $SecondGoalPenaltyShootouts = DB::table('matchdetails')
                ->where('matchId', '=', $Match->Id)
                ->where('TeamId', '=', $SecondTeamId)
                ->where('Type', '=', 'GoalPenaltyShootouts')
                ->count();

            $Match->SecondTeam->GoalPenaltyShootouts = $SecondGoalPenaltyShootouts;
        } else
        {
            $Match->SecondTeam = null;
        }

        $Match->MatchDate = date('Y-m-d  h:i A', strtotime($Match->MatchDate));

        return $Match;
    }

    public function GetMatchInfo(Request $request)
    {
        $GetMatchId = $request->Id;

        $Match = DB::table('matches')->where('Id', '=', $GetMatchId)->first();

        $Match = $this->GetMatchReady($Match);

        $Match = $this->GetMatchDetails($Match);

        return $this->returnDate('Data', $Match);
    }

    public function GetMatchDetails($MyArray)
    {
        $MatchId = $MyArray->Id;

        $FirstTeamId = $MyArray->FirstTeamId;

        $FRedCartCount = DB::table('matchdetails')
            ->where('matchId', '=', $MatchId)
            ->where('TeamId', '=', $FirstTeamId)
            ->where('Type', '=', 'RedCard')
            ->count();

        $FYellowCartCount = DB::table('matchdetails')
            ->where('matchId', '=', $MatchId)
            ->where('TeamId', '=', $FirstTeamId)
            ->where('Type', '=', 'YellowCard')
            ->count();

        $FGoalCount = DB::table('matchdetails')
            ->where('matchId', '=', $MatchId)
            ->where('TeamId', '=', $FirstTeamId)
            ->where('Type', '=', 'Goal')
            ->count();

        $FGoalPenaltyShootouts = DB::table('matchdetails')
            ->where('matchId', '=', $MatchId)
            ->where('TeamId', '=', $FirstTeamId)
            ->where('Type', '=', 'GoalPenaltyShootouts')
            ->count();

        $MyArray->FirstTeamStatistics = ['RedCartCount' => $FRedCartCount, 'YellowCartCount' => $FYellowCartCount, 'GoalCount' => $FGoalCount, 'GoalPenaltyShootouts' => $FGoalPenaltyShootouts];

        $SecondTeam = $MyArray->SecondTeamId;

        $SRedCartCount = DB::table('matchdetails')
            ->where('matchId', '=', $MatchId)
            ->where('TeamId', '=', $SecondTeam)
            ->where('Type', '=', 'RedCard')
            ->count();

        $SYellowCartCount = DB::table('matchdetails')
            ->where('matchId', '=', $MatchId)
            ->where('TeamId', '=', $SecondTeam)
            ->where('Type', '=', 'YellowCard')
            ->count();

        $SGoalCount = DB::table('matchdetails')
            ->where('matchId', '=', $MatchId)
            ->where('TeamId', '=', $SecondTeam)
            ->where('Type', '=', 'Goal')
            ->count();

        $SGoalPenaltyShootouts = DB::table('matchdetails')
            ->where('matchId', '=', $MatchId)
            ->where('TeamId', '=', $SecondTeam)
            ->where('Type', '=', 'GoalPenaltyShootouts')
            ->count();

        $MyArray->SecondTeamStatistics = ['RedCartCount' => $SRedCartCount, 'YellowCartCount' => $SYellowCartCount, 'GoalCount' => $SGoalCount, 'GoalPenaltyShootouts' => $SGoalPenaltyShootouts];

        return $MyArray;
    }

    public function AddMatchDetails(Request $request)
    {
        $GetMatchId = $request->MatchId;
        $GetTeamId = $request->TeamId;
        $GetPlayerId = $request->PlayerId;
        $GetType = $request->Type;
        $GetAssistantId = $request->AssistantId;
        $GetIsPenalty = $request->IsPenalty;
        $GetMinutes = $request->Minutes;
        $GetSeconds = $request->Seconds;
        $GetAddById = $request->AddById;
        $GetAddByAccountTypeId = $request->AddByAccountTypeId;

        $Data = [
            'matchId' => $GetMatchId,
            'TeamId' => $GetTeamId,
            'PlayerId' => $GetPlayerId,
            'Type' => $GetType,
            'AssistantId' => $GetAssistantId,
            'IsPenalty' => $GetIsPenalty,
            'Minutes' => $GetMinutes,
            'Seconds' => $GetSeconds,
            'AddById' => $GetAddById,
            'AddByAccountTypeId' => $GetAddByAccountTypeId,
        ];

        DB::table('matchdetails')->insert($Data);

        $GetMatchInfo = DB::table('matches')->where('Id', '=', $GetMatchId)->first();

        if ($GetMatchInfo) {
            $MatchLeagueId = $GetMatchInfo->LeagueId;
            if ($MatchLeagueId != null && $MatchLeagueId != 0 && ($GetType == 'Goal' || $GetType == 'GoalPenaltyShootouts')) {
                  FirebaseController::UpdateFirebase($MatchLeagueId, false, true, true, false, false, false, false, false);
          }
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetStaffMatches(Request $request)
    {
        $GetStaffId = $request->StaffId;
        $GetAccountTypeId = $request->AccountTypeId;
        $Result = [];

        $MyArray = DB::table('matchstaff')
            ->where('UserAccountTypeId','=' , $GetAccountTypeId)
            ->where('UserId','=' , $GetStaffId)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($MyArray); $y++)
        {
            $MatchId = $MyArray[$y]->MatchId;

            $GetMatchInfo = DB::table('matches')
                ->where('Id','=' , $MatchId)
                ->first();

            if ($GetMatchInfo != null)
                $Result[] = MatchesController::GetMatchReady($GetMatchInfo);
        }

        return $this->returnDate('Data', $Result);
    }

    public function GetUpcomingMatch(Request $request)
    {
        $GetLeagueId = $request->LeagueId;

        $GetDate = Carbon::now();

        $Match = DB::table('matches')->where('LeagueId','=' , $GetLeagueId)->whereDate('MatchDate','=' , $GetDate)->first();

        if(!$Match)
        {
            $Match = DB::table('matches')->where('LeagueId','=' , $GetLeagueId)->whereDate('MatchDate','>' , $GetDate)->first();
        }

        if($Match)
        {
            $Match = MatchesController::GetMatchReady($Match);
        }

        return $this->returnDate('Data', $Match);
    }

    public function EndTheMatch(Request $request)
    {
        $GetMatchId = $request->MatchId;
        $GetFirstTeamId = $request->FirstTeamId;
        $GetSecondTeamId = $request->SecondTeamId;

        $FirstTeamGoalCount = DB::table('matchdetails')
            ->where('matchId','=' , $GetMatchId)
            ->where('TeamId','=' , $GetFirstTeamId)
            ->where('Type','=' , 'Goal')
            ->count();

        $SecondTeamGoalCount = DB::table('matchdetails')
            ->where('matchId','=' , $GetMatchId)
            ->where('TeamId','=' , $GetSecondTeamId)
            ->where('Type','=' , 'Goal')
            ->count();

        $WinerTeamId = 0;

        if($FirstTeamGoalCount > $SecondTeamGoalCount)
            $WinerTeamId = $GetFirstTeamId;

        if($SecondTeamGoalCount > $FirstTeamGoalCount)
            $WinerTeamId = $GetSecondTeamId;

        DB::table('matches')
            ->where('Id', $GetMatchId)
            ->update(['WinerId' => $WinerTeamId, 'Status' => 2]);

        $GetMatchInfo = DB::table('matches')->where('Id', '=', $GetMatchId)->first();

        if ($GetMatchInfo) {
            $MatchLeagueId = $GetMatchInfo->LeagueId;
            if ($MatchLeagueId != null && $MatchLeagueId != 0) {
                FirebaseController::UpdateFirebase($MatchLeagueId, false, true, true, true, true, false, false, false);
            }
        }

        return $this->returnDate('ExecuteStatus', true);
    }
}
