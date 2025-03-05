<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\APIs\LeaguePageAPIs\FirebaseController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Http\Controllers\Helpers\MatchDetailsType;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Faker\Extension\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Collection;
use phpDocumentor\Reflection\DocBlock\Tags\Example;
use Ramsey\Uuid\Type\Integer;
use function PHPUnit\Framework\exactly;

class LeaguesController extends Controller
{
    use GeneralTrait;

    public function GetLeagues(Request $request)
    {
        $FinalResult = [];

        if($request->AccountTypeId == 11)
        {
            $GetStatus = $request->Status;
            $Result = [];

            if($GetStatus == "New")
            {
                $Result = DB::table('leagues')
                    ->where('CreateByUserTypeId', '=', $request->AccountTypeId)
                    ->where('Status', '=', -1)
                    ->orderByRaw('Id DESC')
                    ->get();

            } else if($GetStatus == "Active")
            {
                $Result = DB::table('leagues')
                    ->where('CreateByUserTypeId', '=', $request->AccountTypeId)
                    ->where('Status', '=', 1)
                    ->orderByRaw('Id DESC')
                    ->get();

            } else if($GetStatus == "Inactive")
            {
                $Result = DB::table('leagues')
                    ->where('CreateByUserTypeId', '=', $request->AccountTypeId)
                    ->where('Status', '=', 0)
                    ->orderByRaw('Id DESC')
                    ->get();

            } else if($GetStatus == "Ended")
            {
                $Result = DB::table('leagues')
                    ->where('CreateByUserTypeId', '=', $request->AccountTypeId)
                    ->where('Status', '=', 2)
                    ->orderByRaw('Id DESC')
                    ->get();
            }

            for($y = 0; $y<count($Result); $y++)
            {
                $TotalTeams = 0;
                $TeamsIds = [];

                $GetId = $Result[$y]->Id;
                $GetStadiumLocation = $Result[$y]->Location;

                $GetStadiumId = $Result[$y]->StadiumId;

                $Teams = DB::table('managejoin')
                    ->where('LeagueId', '=', $GetId)
                    ->where('AccountTypeId', '=', 13) // Team Account Type Id
                    ->where('IsAccepted', '=', 1)
                    ->where('IsDeleted', '=', 0)
                    ->get();

                for($n = 0; $n<count($Teams); $n++)
                {
                    $Team = $Teams[$n]->UserId;

                    if(count($TeamsIds) == 0 || count($TeamsIds) == null)
                    {
                        $TotalTeams += 1;
                        $TeamsIds[] = [$Team];

                    } else
                    {
                        $IsExsit = false;

                        for($p = 0; $p<count($TeamsIds); $p++)
                        {
                            if($TeamsIds[$p] == $Team)
                            {
                                $IsExsit = true;
                            }
                        }

                        if(!$IsExsit)
                        {
                            $TotalTeams += 1;
                            $TeamsIds[] = [$Team];
                        }
                    }

                }

                $Result[$y]->TeamCount = $TotalTeams;

                $Stadium = DB::table('stadiums')
                    ->where('Id', '=', $GetStadiumId)
                    ->first();

                $Result[$y]->Stadium = $Stadium;

                $FinalResult[] = $Result[$y];
            }

        } else
        {
            $Result = DB::select('SELECT * FROM leagues leg WHERE leg.isApproved = 1 AND (leg.Status = 1 OR leg.Status = 2) ORDER BY leg.Id DESC');

            for($y = 0; $y<count($Result); $y++)
            {
                $TotalTeams = 0;
                $TeamsIds = [];
                $TeamsLogo = [];

                $GetId = $Result[$y]->Id;
                $GetStadiumLocation = $Result[$y]->Location;

                $Location = explode(",", $GetStadiumLocation);
                $StadiumLatitude = $Location[0];
                $StadiumLongitude = $Location[1];

                $Distance =  HelperFun::distance(floatval($request->Latitude), floatval($request->Longitude), floatval($StadiumLatitude), floatval($StadiumLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN")
                {
                    $Result[$y]->Distance = $Distance;
                    $GetStadiumId = $Result[$y]->StadiumId;

                    $Teams = DB::table('managejoin')
                        ->where('LeagueId', '=', $GetId)
                        ->where('AccountTypeId', '=', 13) // Team Account Type Id
                        ->where('IsAccepted', '=', 1)
                        ->where('IsDeleted', '=', 0)
                        ->get();

                    for($n = 0; $n<count($Teams); $n++)
                    {
                        $Team = $Teams[$n]->UserId;

                        if(count($TeamsIds) == 0 || count($TeamsIds) == null)
                        {
                            $TotalTeams += 1;
                            $TeamsIds[] = [$Team];

                            $GetTeamLogo = DB::table('teams')
                                ->where('Id', '=', $Team)
                                ->first();
                            if ($GetTeamLogo->Logo != null && $GetTeamLogo->Logo != "") {
                                $TeamsLogo[] = $GetTeamLogo->Logo;
                            }

                        } else
                        {
                            $IsExsit = false;

                            for($p = 0; $p<count($TeamsIds); $p++)
                            {
                                if($TeamsIds[$p] == $Team)
                                {
                                    $IsExsit = true;
                                }
                            }

                            if(!$IsExsit)
                            {
                                $TotalTeams += 1;
                                $TeamsIds[] = [$Team];

                                $GetTeamLogo = DB::table('teams')
                                    ->where('Id', '=', $Team)
                                    ->first();
                                if ($GetTeamLogo->Logo != null && $GetTeamLogo->Logo != "") {
                                    $TeamsLogo[] = $GetTeamLogo->Logo;
                                }

                            }
                        }
                    }

                    $Result[$y]->TeamCount = $TotalTeams;
                    $Result[$y]->TeamsLogo = $TeamsLogo;

                    $Stadium = DB::table('stadiums')
                        ->where('Id', '=', $GetStadiumId)
                        ->first();

                    $Result[$y]->Stadium = $Stadium;

                    $FinalResult[] = $Result[$y];
                }
            }
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetAllLeagues(Request $request)
    {
        $FinalResult = [];
        $Result = DB::table('leagues')
            ->where('isApproved', '=', 1)
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $TotalTeams = 0;
            $TeamsIds = [];

            $GetId = $Result[$y]->Id;
            $GetStadiumLocation = $Result[$y]->Location;

            $Location = explode(",", $GetStadiumLocation);
            $StadiumLatitude = $Location[0];
            $StadiumLongitude = $Location[1];

            $GetStadiumId = $Result[$y]->StadiumId;

            $Teams = DB::table('managejoin')
                ->where('LeagueId', '=', $GetId)
                ->where('AccountTypeId', '=', 13) // Team Account Type Id
                ->where('IsAccepted', '=', 1)
                ->where('IsDeleted', '=', 0)
                ->get();

            for($n = 0; $n<count($Teams); $n++)
            {
                $Team = $Teams[$n]->UserId;

                if(count($TeamsIds) == 0 || count($TeamsIds) == null)
                {
                    $TotalTeams += 1;
                    $TeamsIds[] = [$Team];

                } else
                {
                    $IsExsit = false;

                    for($p = 0; $p<count($TeamsIds); $p++)
                    {
                        if($TeamsIds[$p] == $Team)
                        {
                            $IsExsit = true;
                        }
                    }

                    if(!$IsExsit)
                    {
                        $TotalTeams += 1;
                        $TeamsIds[] = [$Team];
                    }
                }

            }

            $Result[$y]->TeamCount = $TotalTeams;

            $Stadium = DB::table('stadiums')
                ->where('Id', '=', $GetStadiumId)
                ->first();

            $Result[$y]->Stadium = $Stadium;

            $FinalResult[] = $Result[$y];
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetLeagueInfo(Request $request)
    {
        $Result = DB::table('leagues')
            ->where('Id', '=', $request->LeagueId)
            ->first();

        $TotalTeams = 0;
        $TeamsIds = [];

        $GetId = $Result->Id;
        $GetStadiumLocation = $Result->Location;

        $Location = explode(",", $GetStadiumLocation);
        $StadiumLatitude = $Location[0];
        $StadiumLongitude = $Location[1];

        $GetStadiumId = $Result->StadiumId;

        $Teams = DB::table('managejoin')
            ->where('LeagueId', '=', $GetId)
            ->where('AccountTypeId', '=', 13) // Team Account Type Id
            ->where('IsAccepted', '=', 1)
            ->where('IsDeleted', '=', 0)
            ->get();

        for($n = 0; $n<count($Teams); $n++)
        {
            $Team = $Teams[$n]->UserId;

            if(count($TeamsIds) == 0 || count($TeamsIds) == null)
            {
                $TotalTeams += 1;
                $TeamsIds[] = [$Team];

            } else
            {
                $IsExsit = false;

                for($p = 0; $p<count($TeamsIds); $p++)
                {
                    if($TeamsIds[$p] == $Team)
                    {
                        $IsExsit = true;
                    }
                }

                if(!$IsExsit)
                {
                    $TotalTeams += 1;
                    $TeamsIds[] = [$Team];
                }
            }

        }

        $Result->TeamCount = $TotalTeams;

        $Stadium = DB::table('stadiums')
            ->where('Id', '=', $GetStadiumId)
            ->first();

        $Result->Stadium = $Stadium;

        return $this->returnDate('Data', $Result);
    }

    public function GetLeagueTeams(Request $request)
    {
        $GetLeagueId = $request->LeagueId;

        $Result = DB::table('matches')
            ->where('LeagueId', '=', $GetLeagueId)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $FirstTeamId = $Result[$y]->FirstTeamId;
            $SecondTeamId = $Result[$y]->SecondTeamId;
            $StadiumId = $Result[$y]->StadiumId;

            $FirstTeam = DB::table('teams')
                ->where('Id', '=', $FirstTeamId)
                ->first();

            $FirstTeamPlayers = DB::table('players')
                ->where('TeamId', '=', $FirstTeamId)
                ->orderByRaw('Id DESC')
                ->get();

            for($f = 0; $f<count($FirstTeamPlayers); $f++)
            {
                $FirstTeamPlayers[$f]->Postion = DB::table('playerposition')
                    ->where('Id', '=', $FirstTeamPlayers[$f]->PositionId)
                    ->first();
            }

            $FirstTeam->Players = $FirstTeamPlayers;

            $Result[$y]->FirstTeam = $FirstTeam;

            $SecondTeam = DB::table('teams')
                ->where('Id', '=', $SecondTeamId)
                ->first();

            $SecondTeamPlayers = DB::table('players')
                ->where('TeamId', '=', $FirstTeamId)
                ->orderByRaw('Id DESC')
                ->get();

            for($s = 0; $s<count($SecondTeamPlayers); $s++)
            {
                $SecondTeamPlayers[$s]->Postion = DB::table('playerposition')
                    ->where('Id', '=', $SecondTeamPlayers[$s]->PositionId)
                    ->first();
            }

            $SecondTeam->Players = $SecondTeamPlayers;

            $Result[$y]->SecondTeam = $SecondTeam;

            $Stadium = DB::table('stadiums')
                ->where('Id', '=', $StadiumId)
                ->first();

            $Result[$y]->Stadium = $Stadium;
        }

        return $this->returnDate('Data', $Result);

    }

//    public function GetLeagueListTeams(Request $request)
//    {
//        $GetLeagueId = $request->LeagueId;
//
//        $FinalResult = [];
//
//        $Result = DB::table('leagueteams')
//            ->where('LeagueId', '=', $GetLeagueId)
//            ->orderByRaw('Id DESC')
//            ->get();
//
//        for($y = 0; $y<count($Result); $y++)
//        {
//            $TeamId = $Result[$y]->TeamId;
//
//            $TeamInfo = DB::table('teams')
//                ->where('Id', '=', $TeamId)
//                ->first();
//
//            $FinalResult[] = $TeamInfo;
//        }
//
//        return $this->returnDate('Data', $Result);
//
//    }

    public function GetLeagueScorers(Request $request) {

        $LeagueScorers = DB::select('SELECT team.Id TeamId, team.NameAr TeamAr, team.NameEn TeamEn, mat_det.PlayerId PlayerId, CONCAT(player.FirstName , " ", player.LastName) as PlayerName , COUNT(mat_det.PlayerId) GoalCount FROM leagues leg LEFT JOIN matches mat ON mat.LeagueId = leg.Id LEFT JOIN matchdetails mat_det ON mat_det.MatchId = mat.Id AND mat_det.Type = \'Goal\' LEFT JOIN players player ON player.Id = mat_det.PlayerId Left JOIN teams team On team.Id = player.TeamId WHERE leg.Id = '. $request->LeagueId .' GROUP BY mat_det.PlayerId ORDER BY GoalCount  DESC;');

        return $this->returnDate('Data', $LeagueScorers);
    }

    public function GetSponsorsByLeagueId(Request $request) {

        $Result = DB::table('sponsors')
            ->where('LeagueId', '=', $request->LeagueId)
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        return $this->returnDate('Data', $Result);
    }


    public function GetLeagueStandings(Request $request)
    {
        $FinalResultSorted = LeaguesController::CalculateStandings($request->LeagueId, $request->ChampionshipId);

        return $this->returnDate('Data', $FinalResultSorted);
    }

    static function CalculateStandings($GetLeagueId, $GetChampionshipId) {

        $FinalResult = [];

        if ($GetChampionshipId != 0) {

            $ChampionshipInfo = DB::table('leaguechampionship')
                ->where('Id', '=', $GetChampionshipId)
                ->first();
            $GetLeagueId = $ChampionshipInfo->LeagueId;

            $Result = DB::table('championshipteams')
                ->where('ChampionshipId', '=', $GetChampionshipId)
                ->orderByRaw('Id DESC')
                ->get();
        } else {
            $Result = DB::table('managejoin')
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('AccountTypeId', '=', 13) // Team Account Type Id
                ->where('IsAccepted', '=', 1)
                ->where('IsDeleted', '=', 0)
                ->get();
        }

        for($y = 0; $y<count($Result); $y++)
        {
            if ($GetChampionshipId != 0) {
                $TeamId = $Result[$y]->TeamId;
            } else {
                $TeamId = $Result[$y]->UserId;
            }

            $TeamPlay = 0; // Play
            $TeamWinCount = 0; // Win
            $TeamDrawCount = 0; // تعادل
            $TeamLostCount = 0; // Lost
            $TeamForThem = 0; // له
            $TeamOnThem = 0; // عليه
            $TeamGD = 0; // Goal difference
            $TeamPts = 0; // النقاط

            $FrMatch = -1;
            $SMatch =-1;
            $ThMatch = -1;
            $FoMatch = -1;
            $FiMatch = -1;
            $FairPlay = 0; // اللعب النظيف

            $TeamInfo = DB::table('teams')
                ->where('Id', '=', $TeamId)
                ->first();

            //   $TeamInfo->Logo = "";

            $TeamMatches = DB::select('select * from matches where LeagueId = '. $GetLeagueId .' AND ChampionshipId = '. $GetChampionshipId .' AND Status = 2 AND(FirstTeamId = '. $TeamId .' || SecondTeamId = '. $TeamId .')');

            $TeamPlay = count($TeamMatches);

            for($n = 0; $n<count($TeamMatches); $n++)
            {

                $FairPlay = LeaguesController::CalculateCardsPoints($TeamMatches[$n]->Id, $TeamId);

//                $PlayedTotalCards += DB::table('matchdetails')
//                    ->where('matchId', '=', $TeamMatches[$n]->Id)
//                    ->where('TeamId', '=', $TeamId)
//                    ->where('Type', '=', MatchDetailsType::RedCard)
//                    ->count();
//
//                $PlayedTotalCards += DB::table('matchdetails')
//                    ->where('matchId', '=', $TeamMatches[$n]->Id)
//                    ->where('TeamId', '=', $TeamId)
//                    ->where('Type', '=', MatchDetailsType::YellowCard)
//                    ->count();

                $TeamForThem += DB::table('matchdetails')
                    ->where('matchId', '=', $TeamMatches[$n]->Id)
                    ->where('TeamId', '=', $TeamId)
                    ->where('Type', '=', MatchDetailsType::Goal)
                    ->count();

                $OtherTeamId = 0;
                if($TeamMatches[$n]->FirstTeamId == $TeamId)
                {
                    $OtherTeamId = $TeamMatches[$n]->SecondTeamId;
                } else
                {
                    $OtherTeamId = $TeamMatches[$n]->FirstTeamId;
                }

                $TeamOnThem += DB::table('matchdetails')
                    ->where('matchId', '=', $TeamMatches[$n]->Id)
                    ->where('TeamId', '=', $OtherTeamId)
                    ->where('Type', '=', MatchDetailsType::Goal)
                    ->count();
            }

            // ====> Double Check
            // How many win
            $TeamWinCount = DB::table('matches')
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('WinerId', '=', $TeamId)
                ->where('ChampionshipId', '=', $GetChampionshipId)
                ->where('Status', '=', 2)
                ->count();

            $TeamPts = $TeamWinCount * 3;

            // تعادل
            $TeamDrawCount = DB::select('select * from matches where LeagueId = '. $GetLeagueId .' AND ChampionshipId = '. $GetChampionshipId .' AND WinerId = 0 AND Status = 2 AND(FirstTeamId = '. $TeamId .' || SecondTeamId = '. $TeamId .')');

            $TeamPts += count($TeamDrawCount);

            // How many Lost
            $TeamLostCount = DB::select('select * from matches where LeagueId = '. $GetLeagueId .' AND ChampionshipId = '. $GetChampionshipId .' AND WinerId != '. $TeamId .' AND WinerId != 0 AND Status = 2 AND(FirstTeamId = '. $TeamId .' || SecondTeamId = '. $TeamId .')');

            // Last Five Matches
            $AllMatches = DB::select('select * from matches where LeagueId = '. $GetLeagueId .' AND ChampionshipId = '. $GetChampionshipId .' AND Status = 2 AND (FirstTeamId = '. $TeamId .' || SecondTeamId = '. $TeamId .') ORDER BY Id ASC');

            for($x = 0; $x<count($AllMatches); $x++)
            {
                if($x == 0)
                {
                    if($AllMatches[$x]->WinerId == 0)
                    {
                        $FrMatch = 0;
                    } else if($AllMatches[$x]->WinerId == $TeamId)
                    {
                        $FrMatch = 1;
                    } else
                    {
                        $FrMatch = 2;
                    }
                }

                if($x == 1)
                {
                    if($AllMatches[$x]->WinerId == 0)
                    {
                        $SMatch = 0;
                    } else if($AllMatches[$x]->WinerId == $TeamId)
                    {
                        $SMatch = 1;
                    } else
                    {
                        $SMatch = 2;
                    }
                }

                if($x == 2)
                {
                    if($AllMatches[$x]->WinerId == 0)
                    {
                        $ThMatch = 0;
                    } else if($AllMatches[$x]->WinerId == $TeamId)
                    {
                        $ThMatch = 1;
                    } else
                    {
                        $ThMatch = 2;
                    }
                }

                if($x == 3)
                {
                    if($AllMatches[$x]->WinerId == 0)
                    {
                        $FoMatch = 0;
                    } else if($AllMatches[$x]->WinerId == $TeamId)
                    {
                        $FoMatch = 1;
                    } else
                    {
                        $FoMatch = 2;
                    }
                }

                if($x == 4)
                {
                    if($AllMatches[$x]->WinerId == 0)
                    {
                        $FiMatch = 0;
                    } else if($AllMatches[$x]->WinerId == $TeamId)
                    {
                        $FiMatch = 1;
                    } else
                    {
                        $FiMatch = 2;
                    }
                }
            }

            $TeamGD = $TeamForThem - $TeamOnThem;

            $FinalResult[] = ['TeamId' => $TeamInfo->Id, 'TeamNameAr' => $TeamInfo->NameAr, 'TeamNameEn' => $TeamInfo->NameEn, 'Logo' => $TeamInfo->Logo
                , 'Play' => $TeamPlay, 'Win' => $TeamWinCount, 'Draw' => count($TeamDrawCount), 'Lost' => count($TeamLostCount), 'ForThem' => $TeamForThem
                , 'A' => $TeamOnThem, 'GD' => $TeamGD, 'Pts' => $TeamPts, 'FrMatch' => $FrMatch, 'SMatch' => $SMatch, 'ThMatch' => $ThMatch, 'FoMatch' => $FoMatch, 'FiMatch' => $FiMatch, 'PlayedPts' => 0, 'PlayedGD' => 0,
                'PlayedTotalGoals' => 0, 'PlayedTotalCards' => $FairPlay];
        }

        // Sort
        // $ResultSorted = collect($FinalResult)->sortBy('Lost')->sortBy('Pts')->reverse()->toArray();

        $FinalResult2 = [];
 //        for($b = 0; $b<count($FinalResult); $b++) {
//            $GetOneTeamId = $FinalResult[$b]['TeamId'];
//            $GetOneTeamNameAr = $FinalResult[$b]['TeamNameAr'];
//            $GetOnePts = $FinalResult[$b]['Pts'];
//            $GetOneGD = $FinalResult[$b]['GD'];
//            $GetOneForThem = $FinalResult[$b]['ForThem'];
//
//            for($z = 0; $z<count($FinalResult); $z++) {
//                $GetTowTeamId = $FinalResult[$z]['TeamId'];
//                $GetTowTeamNameAr = $FinalResult[$z]['TeamNameAr'];
//                $GetTowPts = $FinalResult[$z]['Pts'];
//                $GetTowGD = $FinalResult[$z]['GD'];
//                $GetTowForThem = $FinalResult[$z]['ForThem'];
//
//                if ($GetOneTeamId != $GetTowTeamId && $GetOnePts == $GetTowPts && $GetOneGD == $GetTowGD && $GetOneForThem == $GetTowForThem) {
//
//                    $IsExesit = false;
//                    for($m = 0; $m<count($FinalResult2); $m++) {
//                        if ($FinalResult2[$m]['OneTeam'] === $GetOneTeamNameAr AND $FinalResult2[$m]['TowTeam'] === $GetTowTeamNameAr) {
//                            $IsExesit = true;
//                        }
//                        if ($FinalResult2[$m]['TowTeam'] === $GetOneTeamNameAr AND $FinalResult2[$m]['OneTeam'] === $GetTowTeamNameAr) {
//                            $IsExesit = true;
//                        }
//                    }
//
//                    if ($IsExesit === false) {
//                        $FinalResult2[] = LeaguesController::GetTogetherPlay($GetLeagueId, $GetChampionshipId, $GetOneTeamId, $GetOneTeamNameAr, $GetTowTeamId, $GetTowTeamNameAr);
//                        $Size = count($FinalResult2) - 1;
//                        if ($FinalResult2[$Size]['OneTeamId'] == $GetOneTeamId) {
//                            $FinalResult[$b]['PlayedPts'] = $FinalResult2[$Size]['TeamOnePts'];
//                            $FinalResult[$b]['PlayedGD'] = $FinalResult2[$Size]['TeamOneGD'];
//                            $FinalResult[$b]['PlayedTotalGoals'] = $FinalResult2[$Size]['TeamOneTotalGoals'];
//                            $FinalResult[$b]['PlayedTotalCards'] = $FinalResult2[$Size]['TeamOneTotalCards'];
//                        }
//                        if ($FinalResult2[$Size]['TowTeamId'] == $GetTowTeamId) {
//                            $FinalResult[$z]['PlayedPts'] = $FinalResult2[$Size]['TeamTowPts'];
//                            $FinalResult[$z]['PlayedGD'] = $FinalResult2[$Size]['TeamTowGD'];
//                            $FinalResult[$z]['PlayedTotalGoals'] = $FinalResult2[$Size]['TeamTowTotalGoals'];
//                            $FinalResult[$z]['PlayedTotalCards'] = $FinalResult2[$Size]['TeamTowTotalCards'];
//                        }
//                    }
//                }
//            }
//        }

        $ResultSorted = collect($FinalResult)->sortBy('PlayedTotalCards')->reverse()->sortBy('PlayedTotalGoals')->sortBy('PlayedGD')->sortBy('PlayedPts')->sortBy('ForThem')->sortBy('GD')->sortBy('Pts')->reverse()->toArray();

        $FinalResultSorted = [];

        foreach($ResultSorted as $Object) {
            $FinalResultSorted[] = $Object;
        }

        return $FinalResultSorted;
    }

    static function GetTogetherPlay($GetLeagueId, $GetChampionshipId, $TeamOneId, $GetOneTeamNameAr, $TeamTowId, $GetTowTeamNameAr)
    {
        $TeamOneWinCount = DB::select('select * from matches where LeagueId = '. $GetLeagueId .' AND ChampionshipId = '. $GetChampionshipId .' AND WinerId = '. $TeamOneId .'
        AND ((FirstTeamId = '. $TeamOneId .' AND SecondTeamId = '. $TeamTowId .') OR (FirstTeamId = '. $TeamTowId .' AND SecondTeamId = '. $TeamOneId .'))');

        $TeamOnePts = count($TeamOneWinCount) * 3;

        $TeamDrawCount = DB::select('select * from matches where LeagueId = '. $GetLeagueId .' AND ChampionshipId = '. $GetChampionshipId .' AND WinerId = 0
        AND ((FirstTeamId = '. $TeamOneId .' AND SecondTeamId = '. $TeamTowId .') OR (FirstTeamId = '. $TeamTowId .' AND SecondTeamId = '. $TeamOneId .'))');

        $TeamOnePts += count($TeamDrawCount);

        // Team Tow
        $TeamTowPts = 0;
        $TeamTowWinCount = DB::select('select * from matches where LeagueId = '. $GetLeagueId .' AND ChampionshipId = '. $GetChampionshipId .' AND WinerId = '. $TeamTowId .'
        AND ((FirstTeamId = '. $TeamOneId .' AND SecondTeamId = '. $TeamTowId .') OR (FirstTeamId = '. $TeamTowId .' AND SecondTeamId = '. $TeamOneId .'))');

        $TeamTowPts = count($TeamTowWinCount) * 3;

        $TeamTowPts += count($TeamDrawCount);

        // GD
        $TeamOneTotalGoals = DB::select('SELECT MatDet.Id FROM matches Mat INNER JOIN matchdetails MatDet ON MatDet.MatchId = Mat.Id AND MatDet.Type = \'Goal\' WHERE Mat.LeagueId = '. $GetLeagueId .' AND Mat.ChampionshipId = '. $GetChampionshipId .' AND ((Mat.FirstTeamId = '. $TeamOneId .' AND Mat.SecondTeamId = '. $TeamTowId .') OR (Mat.FirstTeamId = '. $TeamTowId .' AND Mat.SecondTeamId = '. $TeamOneId .')) AND MatDet.TeamId = '. $TeamOneId .';');
        $TeamTowTotalGoals = DB::select('SELECT MatDet.Id FROM matches Mat INNER JOIN matchdetails MatDet ON MatDet.MatchId = Mat.Id AND MatDet.Type = \'Goal\' WHERE Mat.LeagueId = '. $GetLeagueId .' AND Mat.ChampionshipId = '. $GetChampionshipId .' AND ((Mat.FirstTeamId = '. $TeamOneId .' AND Mat.SecondTeamId = '. $TeamTowId .') OR (Mat.FirstTeamId = '. $TeamTowId .' AND Mat.SecondTeamId = '. $TeamOneId .')) AND MatDet.TeamId = '. $TeamTowId .';');

        $TeamOneGD = count($TeamOneTotalGoals) - count($TeamTowTotalGoals);
        $TeamTowGD = count($TeamTowTotalGoals) - count($TeamOneTotalGoals);

        // End GD

        $TeamOneCards = DB::select('SELECT MatDet.Id FROM matches Mat INNER JOIN matchdetails MatDet ON MatDet.MatchId = Mat.Id AND (MatDet.Type = \'YellowCard\' OR MatDet.Type = \'RedCard\') WHERE Mat.LeagueId = '. $GetLeagueId .' AND Mat.ChampionshipId = '. $GetChampionshipId .' AND ((Mat.FirstTeamId = '. $TeamOneId .' AND Mat.SecondTeamId = '. $TeamTowId .') OR (Mat.FirstTeamId = '. $TeamTowId .' AND Mat.SecondTeamId = '. $TeamOneId .')) AND MatDet.TeamId = '. $TeamOneId .';');
        $TeamTowCards = DB::select('SELECT MatDet.Id FROM matches Mat INNER JOIN matchdetails MatDet ON MatDet.MatchId = Mat.Id AND (MatDet.Type = \'YellowCard\' OR MatDet.Type = \'RedCard\') WHERE Mat.LeagueId = '. $GetLeagueId .' AND Mat.ChampionshipId = '. $GetChampionshipId .' AND ((Mat.FirstTeamId = '. $TeamOneId .' AND Mat.SecondTeamId = '. $TeamTowId .') OR (Mat.FirstTeamId = '. $TeamTowId .' AND Mat.SecondTeamId = '. $TeamOneId .')) AND MatDet.TeamId = '. $TeamTowId .';');

        $TeamOneTotalCards = -1 * abs(count($TeamOneCards)); // To get negative number
        $TeamTowTotalCards = -1 * abs(count($TeamTowCards)); // To get negative number

        $FinalResult = ['OneTeamId' => $TeamOneId, 'OneTeam' => $GetOneTeamNameAr, 'TeamOnePts' => $TeamOnePts, 'TeamOneGD' => $TeamOneGD, 'TeamOneTotalGoals' => count($TeamOneTotalGoals), 'TeamOneTotalCards' => $TeamOneTotalCards, 'TowTeamId' => $TeamTowId, 'TowTeam' => $GetTowTeamNameAr, 'TeamTowPts' => $TeamTowPts, 'TeamTowGD' => $TeamTowGD, 'TeamTowTotalGoals' => count($TeamTowTotalGoals), 'TeamTowTotalCards' => $TeamTowTotalCards];

        return $FinalResult;
    }

    static function CalculateCardsPoints($MatchId, $TeamId) {

        $matchDetails = DB::table('matchdetails')
            ->where('MatchId', $MatchId)
            ->where('TeamId', $TeamId)
            ->selectRaw("
            PlayerId,
            SUM(CASE WHEN Type = 'YellowCard' THEN 1 ELSE 0 END) AS YellowCardCount,
            SUM(CASE WHEN Type = 'RedCard' THEN 1 ELSE 0 END) AS RedCardCount
        ")
            ->groupBy('PlayerId')
            ->get();

        $points = 0;

        foreach ($matchDetails as $detail) {
            if ($detail->YellowCardCount >= 2) {
                $points += 2; // Two yellow cards = 2 points
            } elseif ($detail->YellowCardCount == 1) {
                $points += 1; // One yellow card = 1 point
            }

            $points += $detail->RedCardCount * 3; // Each red card = 3 points
        }

        return $points;
    }
	
    public function GetNews(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetMatchId = $request->MatchId;

        $Result = [];

        if ($GetMatchId != 0) {
            $Result = DB::table('news')
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('MatchId', '=', $GetMatchId)
                ->where('Status', '=', '1')
                ->get();
        } else {
            $Result = DB::table('news')
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('Status', '=', '1')
                ->get();
        }

        return $this->returnDate('Data', $Result);
    }

    public function AddNews(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetNewsAr = $request->NewsAr;
        $GetNewsEn = $request->NewsEn;

        $Data = [
            'LeagueId' => $GetLeagueId,
            'Details' => $GetNewsAr,
            'DetailsEn' => $GetNewsEn,
        ];

        DB::table('news')->insert($Data);

        if ($GetLeagueId != null && $GetLeagueId != 0) {
            FirebaseController::UpdateFirebase($GetLeagueId, false, false, false, false, false, false, false, true);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function RemoveNews(Request $request)
    {
        $GetNewsId = $request->NewsId;

        DB::table('news')
            ->where('Id', '=', $GetNewsId)
            ->delete();

        $GetNewInfo = DB::table('news')->where('Id', '=', $GetNewsId)->first();

        if ($GetNewInfo) {
            $GetLeagueId = $GetNewInfo->LeagueId;
            if ($GetLeagueId != null && $GetLeagueId != 0) {
                FirebaseController::UpdateFirebase($GetLeagueId, false, false, false, false, false, false, false, true);
            }
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetLeagueCommentators(Request $request)
    {
        $GetLeagueId = $request->LeagueId;

        $FinalResult = [];

        $Matches = DB::table('matches')
            ->where('LeagueId', '=', $GetLeagueId)
            ->get();

        for($y = 0; $y<count($Matches); $y++)
        {
            $CommentatorInfo = DB::table('commentators')
                ->where('Id', '=', $Matches[$y]->CommentatorId	)
                ->first();

            $FinalResult[] = ['Id' => $CommentatorInfo->Id, 'AccountTypeId' => $CommentatorInfo->AccountTypeId, 'FirstName' => $CommentatorInfo->FirstName, 'LastName' => $CommentatorInfo->LastName, 'Nationality' => $CommentatorInfo->Nationality
                , 'QRcode' => $CommentatorInfo->QRcode, 'Phone' => $CommentatorInfo->Phone];
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function CreateNewLeague(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $Topic = $request->Topic;
        $Location = $request->Location;
        $NumberOfTeams = $request->NumberOfTeams;
        $Fee = $request->Fee;
        $LeagueType = $request->LeagueType;

        $Data = [
            'CreateById' => $UserId,
            'CreateByUserTypeId' => $UserAccountTypeId,
            'Topic' => $Topic,
            'LeagueType' => $LeagueType,
            'NumberOfTeams' => $NumberOfTeams,
            'Location' => $Location,
            'Fee' => $Fee,
        ];

        $LeaguesId = DB::table('leagues')->insertGetId($Data);

        if($request->TeamsMap != null && $request->TeamsMap != "")
        {
            $GetTeams = explode(',', $request->TeamsMap);

            for($y = 0; $y<count($GetTeams); $y++)
            {
                if((Integer)$GetTeams[$y] != 0) {
                    $ManageJoinData = [
                        'CreateByUserId' => $UserId,
                        'CreateByAccountTypeId' => $UserAccountTypeId,
                        'UserId' => (Integer)$GetTeams[$y],
                        'AccountTypeId' => 13, // Team Account Type Id
                        'LeagueId' => $LeaguesId,
                        'Fee' => $Fee,
                    ];

                    DB::table('managejoin')->insert($ManageJoinData);

                    $LeagueInfo = DB::table('leagues')
                        ->where('Id', '=', $LeaguesId)
                        ->first();

                    if ($LeagueInfo != null) {
                        $TeamInfo = DB::table('teams')
                            ->where('Id', '=', (Integer)$GetTeams[$y])
                            ->first();

                        if ($TeamInfo != null) {
                            $PlayerInfo = DB::table('players')
                                ->where('Id', '=', $TeamInfo->TeamLeaderId)
                                ->first();

                            if ($LeagueInfo != null && $TeamInfo != null && $PlayerInfo != null && $PlayerInfo->TokenId != null && $PlayerInfo->TokenId != "") {
                                $Message = __('messages.AddTeamToLeague', [], $PlayerInfo->Lang) . ' ' . $LeagueInfo->Topic;
                                $FinalResult = [$PlayerInfo->TokenId];
                                HelperController::SendNotifications($FinalResult, $Message);
                            }
                        }
                    }
                }
            }
        }

        return $this->returnDate('InsertStatus', true);
    }

    public function GetLeagueChampoinship(Request $request)
    {
        $Champoinship = DB::table('leaguechampionship')
            ->where('LeagueId', '=', $request->LeagueId)
            ->get();

        for($y = 0; $y<count($Champoinship); $y++) {

            $ChampionshipId = $Champoinship[$y]->Id;

            $Teams = DB::table('championshipteams')
                ->where('ChampionshipId', '=', $ChampionshipId)
                ->get();

            $TotalTeams = 0;
            $TeamsLogo = [];

            for($n = 0; $n<count($Teams); $n++) {
                $Team = $Teams[$n]->TeamId;

                $TotalTeams += 1;

                $GetTeamLogo = DB::table('teams')
                    ->where('Id', '=', $Team)
                    ->first();

                if ($GetTeamLogo->Logo != null) {
                    $TeamsLogo[] = $GetTeamLogo->Logo;
                } else {
                    $TeamsLogo[] = "";
                }
            }

            $Champoinship[$y]->TeamCount = $TotalTeams;
            $Champoinship[$y]->TeamsLogo = $TeamsLogo;
        }

        return $this->returnDate('Data', $Champoinship);
    }

    public function GetChampionshipTeams(Request $request)
    {
        $Teams = DB::select('SELECT T.* FROM championshipteams champ INNER JOIN teams T ON champ.TeamId = T.Id WHERE champ.ChampionshipId = :ChampionshipId' , ['ChampionshipId' => $request->ChampionshipId]);

        return $this->returnDate('Data', $Teams);
    }

    public function CreateNewLeagueChampoinship(Request $request)
    {
        $LeagueId = $request->LeagueId;
        $Type = $request->Type;
        $Topic = $request->Topic;

        $Data = [
            'LeagueId' => $LeagueId,
            'Type' => $Type,
            'Topic' => $Topic
        ];

        DB::table('leaguechampionship')->insertGetId($Data);

        return $this->returnDate('InsertStatus', true);
    }

	public function UpdateLeagueChampoinshipName(Request $request)
    {
        $ChampionshipId= $request->ChampionshipId;
        $Type = $request->Type;
        $Topic = $request->Topic;

        $Data = [
            'Topic' => $Topic
        ];

        DB::table('leaguechampionship')
            ->where('Id', $ChampionshipId)
            ->update($Data);

        return $this->returnDate('InsertStatus', true);
    }


    public function AddRemoveTeamToChampionship(Request $request)
    {
        $GetChampionshipId = $request->ChampionshipId;
        $GetLeagueId = $request->LeagueId;
        $GetTeamId = $request->TeamId;
        $GetIsRemove = $request->IsRemove;

        if ($GetIsRemove == 0) { // Add

            $Check = DB::table('championshipteams')
                ->where('ChampionshipId', '=', $GetChampionshipId)
                ->where('TeamId', '=', $GetTeamId)
                ->first();

            if (!$Check) {
                $Data = [
                    'ChampionshipId' => $GetChampionshipId,
                    'TeamId' => $GetTeamId,
                ];

                DB::table('championshipteams')->insertGetId($Data);
            }

        } else { // Remove

            // Check if there match for the team
            $CheckIfThereMatch = DB::select('SELECT mat.Id FROM matches mat WHERE mat.LeagueId = '. $GetLeagueId . ' AND  mat.ChampionshipId = '. $GetChampionshipId . ' AND (mat.FirstTeamId = ' . $GetTeamId . ' OR mat.SecondTeamId = ' . $GetTeamId . ')');

            if (count($CheckIfThereMatch) > 0) {
                return $this->returnMessage(false, 2, "");
            } else {
                DB::table('championshipteams')
                    ->where('ChampionshipId', '=', $GetChampionshipId)
                    ->where('TeamId', '=', $GetTeamId)
                    ->delete();
            }
        }

        return $this->returnMessage(true, 0, "");
    }

    public function GetLeagueListTeams(Request $request)
    {
        $FinalResult = [];

        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $GetLeagueId = $request->LeagueId;

        /*        $AllTeams = DB::table('leagueteams')
                    ->where('LeagueId', '=', $GetLeagueId)
                    ->orderByRaw('Id DESC')
                    ->get();*/

        $LeagueInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

        if ($LeagueInfo != null && $LeagueInfo->Fee > 0.0) {
            $AllTeams = DB::table('managejoin')
                ->where('CreateByUserId', '=', $GetUserId)
                ->where('CreateByAccountTypeId', '=', $GetUserAccountTypeId)
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('AccountTypeId', '=', 13) // Team Account Type
                ->where('IsAccepted', '=', 1)
                ->where('PaymentId', '!=', null)
                ->where('IsDeleted', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();
        } else {
            $AllTeams = DB::table('managejoin')
                ->where('CreateByUserId', '=', $GetUserId)
                ->where('CreateByAccountTypeId', '=', $GetUserAccountTypeId)
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('AccountTypeId', '=', 13) // Team Account Type
                ->where('IsAccepted', '=', 1)
                ->where('IsDeleted', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();
        }

        for($n = 0; $n<count($AllTeams); $n++)
        {
            $TeamId = $AllTeams[$n]->UserId;

            $TeamInfo = DB::table('teams')
                ->where('Id', '=', $TeamId)
                ->first();
            $FinalResult[] = $TeamInfo;
        }

        return $this->returnDate('Data', $FinalResult, "");
    }

    public function GetLeagueTeamsListForUser(Request $request)
    {
        $GetLeagueId = $request->LeagueId;

        $FinalResult = [];

        $LeagueInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

        if ($LeagueInfo != null && $LeagueInfo->Fee > 0.0) {
            $AllTeams = DB::table('managejoin')
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('AccountTypeId', '=', 13) // Team Account Type
                ->where('IsAccepted', '=', 1)
                ->where('PaymentId', '!=', null)
                ->where('IsDeleted', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();
        } else {
            $AllTeams = DB::table('managejoin')
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('AccountTypeId', '=', 13) // Team Account Type
                ->where('IsAccepted', '=', 1)
                ->where('IsDeleted', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();
        }

        for($n = 0; $n<count($AllTeams); $n++)
        {
            $TeamId = $AllTeams[$n]->UserId;

            $TeamInfo = DB::table('teams')
                ->where('Id', '=', $TeamId)
                ->first();
            $FinalResult[] = $TeamInfo;
        }

        return $this->returnDate('Data', $FinalResult, "");
    }

    public function UpdateLeague(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $Topic = $request->Topic;
        $Location = $request->Location;
        $NumberOfTeams = $request->NumberOfTeams;
        $Fee = $request->Fee;
        $LeagueType = $request->LeagueType;

        $Data = [
            'LeagueType' => $LeagueType,
            'Topic' => $Topic,
            'NumberOfTeams' => $NumberOfTeams,
            'Location' => $Location,
            'Fee' => $Fee,
        ];

        DB::table('leagues')
            ->where('Id', $GetLeagueId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function ChangeLeagueStatus(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetNewStatus = $request->NewStatus;

        if($GetNewStatus == "Active")
        {
            $Data = [
                'Status' => 1,
                'isApproved' => 1,
            ];

        } else if($GetNewStatus == "Inactive")
        {
            $Data = [
                'Status' => 0,
            ];
        } else if($GetNewStatus == "End")
        {
            $Data = [
                'Status' => 2,
            ];
        }

        DB::table('leagues')
            ->where('Id', $GetLeagueId)
            ->update($Data);

        if($GetNewStatus == "Active")
        {
            DB::table('matches')
                ->where('LeagueId', $GetLeagueId)
                ->update([
                    'Status' => 1,
                ]);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetLeagueTeamsForAdmin(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $Result = [];

        $MyArray = DB::table('managejoin')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('AccountTypeId', '=', 13) // Team Account Type
            ->where('IsDeleted', '=', 0)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($MyArray); $y++)
        {
            $TeamId = $MyArray[$y]->UserId;

            $TeamInfo = DB::table('teams')
                ->where('Id', '=', $TeamId)
                ->first();

            $MyArray[$y]->TeamInfo = $TeamInfo;

            $Result[] = $TeamInfo;
        }

        return $this->returnDate('Data', $Result);

    }

    public function RemoveTeamFromLeague(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $GetLeagueId = $request->LeagueId;
        $GetTeamId = $request->TeamId;

        $GetDate = Carbon::now();

        DB::table('managejoin')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('UserId', '=', $GetTeamId)
            ->where('AccountTypeId', '=', 13) // Team Account Type Id
            ->where('IsDeleted', '=', 0)
            ->update(['Status' => 0, 'IsDeleted' => 1, 'DeletedDate' => $GetDate, 'DeletedBy' => $UserId, 'DeletedByAccountTypeId' => $UserAccountTypeId]);

        $LeagueInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

        $TeamInfo = DB::table('teams')
            ->where('Id', '=', $GetTeamId)
            ->first();

        if ($LeagueInfo != null && $TeamInfo != null) {
            $PlayerInfo = DB::table('players')
                ->where('Id', '=', $TeamInfo->TeamLeaderId)
                ->first();

            if ($PlayerInfo != null && $PlayerInfo->Lang != null && $PlayerInfo->TokenId != null && $PlayerInfo->TokenId != "") {
                $Message = __('messages.RemoveTeamFromLeague', [], $PlayerInfo->Lang) . ' ' . $LeagueInfo->Topic;
                $FinalResult = [$PlayerInfo->TokenId];
                HelperController::SendNotifications($FinalResult, $Message);
            }
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function AddTeamToLeague(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $GetLeagueId = $request->LeagueId;
        $GetTeamId = $request->TeamId;

        $LeagueInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

        $ManageJoinData = [
            'CreateByUserId' => $UserId,
            'CreateByAccountTypeId' => $UserAccountTypeId,
            'UserId' => $GetTeamId,
            'AccountTypeId' => 13, // Team Account Type Id
            'LeagueId' => $GetLeagueId,
            'Fee' => $LeagueInfo->Fee,
        ];

        DB::table('managejoin')->insert($ManageJoinData);

        $TeamInfo = DB::table('teams')
            ->where('Id', '=', $GetTeamId)
            ->first();

        $PlayerInfo = DB::table('players')
            ->where('Id', '=', $TeamInfo->TeamLeaderId)
            ->first();

        if ($LeagueInfo != null && $TeamInfo != null && $PlayerInfo != null && $PlayerInfo->TokenId != null && $PlayerInfo->TokenId != "") {
            $Message = __('messages.AddTeamToLeague', [], $PlayerInfo->Lang) . ' ' . $LeagueInfo->Topic;
            $FinalResult = [$PlayerInfo->TokenId];
            HelperController::SendNotifications($FinalResult, $Message);
        }

        return $this->returnDate('ExecuteStatus', true);

    }

    public function GetLeagueMatchesForAdmin(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetChampionshipId = $request->ChampionshipId;
        $Result = [];

        if ($GetChampionshipId == null || $GetChampionshipId == 0) {
            $MyArray = DB::table('matches')
                ->where('LeagueId', '=', $GetLeagueId)
                ->get();
        } else {
           $MyArray = DB::table('matches')
                ->where('LeagueId', '=', $GetLeagueId)
                ->where('ChampionshipId', '=', $GetChampionshipId)
                ->get();
        }


        for($y = 0; $y<count($MyArray); $y++)
        {
            $Result[] = (new MatchesController)->GetMatchReady($MyArray[$y]);
        }

        return $this->returnDate('Data', $Result);

    }

    public function AddMatchToLeague(Request $request)
    {
        $GetMatchId = $request->MatchId;
        $GetLeagueId = $request->LeagueId;
        $GetChampionshipId = $request->ChampionshipId;
        $GetMatchDate = $request->MatchDate;
        $GetFirstTeamId = $request->FirstTeamId;
        $GetSecondTeamId = $request->SecondTeamId;
        $GetStadiumId = $request->StadiumId;
        $GetMatchLocation = $request->MatchLocation;

        // convert string date to Timestamp
        $GetMatchDate = date('Y-m-d H:i:s', strtotime($GetMatchDate));

        $MatchStatus = -1;

        $LeaguesInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

        if($LeaguesInfo)
            $MatchStatus = $LeaguesInfo->Status;

        $Data = [
            'LeagueId' => $GetLeagueId,
            'ChampionshipId' => $GetChampionshipId,
            'MatchDate' => $GetMatchDate,
            'FirstTeamId' => $GetFirstTeamId,
            'SecondTeamId' => $GetSecondTeamId,
            'StadiumId' => $GetStadiumId,
            'Location' => $GetMatchLocation,
            'Status' => $MatchStatus,
        ];

        if($GetMatchId != 0)
        {
            DB::table('matches')
            ->where('Id', $GetMatchId)
            ->update($Data);

        } else
        {
            DB::table('matches')->insert($Data);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetMatchStaff(Request $request)
    {
        $GetAccountTypeId = $request->AccountTypeId;
        $GetMatchId = $request->MatchId;

        $FinalResult = [];

        $StaffList = DB::table('matchstaff')
            ->where('UserAccountTypeId', '=', $GetAccountTypeId)
            ->where('MatchId', '=', $GetMatchId)
            ->get();

        for($y = 0; $y<count($StaffList); $y++)
        {
            $TableName = HelperController::GetTableNameByAccountTypeId($GetAccountTypeId);

            $Info = DB::table($TableName)
                ->where('Id', '=', $StaffList[$y]->UserId)
                ->first();

            $FinalResult[] = $Info;
        }

        return $this->returnDate('Data', $FinalResult);
    }


//    public function GetMatchStaffForLeague(Request $request)
//    {
//        $GetAccountTypeId = $request->AccountTypeId;
//        $GetLeagueId = $request->LeagueId;
//
//        $FinalResult = [];
//
//        $StaffList = DB::table('matchstaff')
//            ->where('UserAccountTypeId', '=', $GetAccountTypeId)
//            ->where('MatchId', '=', $GetMatchId)
//            ->get();
//
//        for($y = 0; $y<count($StaffList); $y++)
//        {
//            $TableName = HelperController::GetTableNameByAccountTypeId($GetAccountTypeId);
//
//            $Info = DB::table($TableName)
//                ->where('Id', '=', $StaffList[$y]->UserId)
//                ->first();
//
//            $FinalResult[] = $Info;
//        }
//
//        return $this->returnDate('Data', $FinalResult);
//    }

    public function GetMatchStaffForLeague(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetAccountTypeId = $request->AccountTypeId;

        $FinalResult = [];

        $Matches = DB::table('matches')
            ->where('LeagueId', '=', $GetLeagueId)
            ->get();

        for($n = 0; $n<count($Matches); $n++)
        {
            $StaffList = DB::table('matchstaff')
                ->where('MatchId', '=', $Matches[$n]->Id)
                ->where('UserAccountTypeId', '=', $GetAccountTypeId)
                ->get();

            for($y = 0; $y<count($StaffList); $y++)
            {
                $TableName = HelperController::GetTableNameByAccountTypeId($GetAccountTypeId);

                $Info = DB::table($TableName)
                    ->where('Id', '=', $StaffList[$y]->UserId)
                    ->first();

                $FinalResult[] = $Info;
            }
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function RemoveMatchStaff(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $GetMatchId = $request->MatchId;


        DB::table('matchstaff')
            ->where('MatchId', '=', $GetMatchId)
            ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
            ->where('UserId', '=', $GetUserId)
            ->delete();

        return $this->returnDate('ExecuteStatus', true);
    }

    public function AddMatchStaff(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $GetMatchId = $request->MatchId;

        $Data = [
            'MatchId' => $GetMatchId,
            'UserAccountTypeId' => $GetUserAccountTypeId,
            'UserId' => $GetUserId,
        ];

        DB::table('matchstaff')->insertGetId($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetStaffByLocation(Request $request)
    {
        $FinalResult = [];

        $GetStaffAccountTypeId = $request->StaffAccountTypeId;
        $IsFromAllCities = $request->IsFromAllCities;
        $Latitude = $request->Latitude;
        $Longitude = $request->Longitude;

        $TableName = HelperController::GetTableNameByAccountTypeId($GetStaffAccountTypeId);

        $AllStaff = DB::table($TableName)
            ->where('Status', '=', '1')
            ->where('IsApproved', '=', '1')
            ->orderByRaw('Id DESC')
            ->get();

        if($IsFromAllCities == 0) {
            for ($n = 0; $n < count($AllStaff); $n++) {
                if ($AllStaff[$n]->Location != null) {
                    $StaffLocation = explode(",", $AllStaff[$n]->Location);
                    $StaffLatitude = $StaffLocation[0];
                    $StaffLongitude = $StaffLocation[1];

                    $Distance = HelperFun::distance(floatval($Latitude), floatval($Longitude), floatval($StaffLatitude), floatval($StaffLongitude), "K");

                    if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN") {
                        $FinalResult[] = $AllStaff[$n];
                    }
                }
            }
        } else {
            $FinalResult = $AllStaff;
        }

        return $this->returnDate('Data', $FinalResult, "");
    }

    public function GetLeagueAndMatchCommentators(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetMatchId = $request->MatchId;

        $FinalResult = [];

        $CommentatorsList = DB::table('commentatorslist')
            ->where('MatchId', '=', $GetMatchId)
            ->get();

        for($y = 0; $y<count($CommentatorsList); $y++)
        {
            $CommentatorsInfo = DB::table('commentators')
                ->where('Id', '=', $CommentatorsList[$y]->CommentatorId)
                ->first();

            $FinalResult[] = $CommentatorsInfo;
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function RemoveCommentatorFromMatch(Request $request)
    {
        $GetCommentatorId = $request->CommentatorId;
        $GetMatchId = $request->MatchId;


        DB::table('commentatorslist')
            ->where('MatchId', '=', $GetMatchId)
            ->where('CommentatorId', '=', $GetCommentatorId)
            ->delete();

        return $this->returnDate('ExecuteStatus', true);
    }

    public function AddCommentatorToMatch(Request $request)
    {
        $GetCommentatorId = $request->CommentatorId;
        $GetMatchId = $request->MatchId;

        $Data = [
            'CommentatorId' => $GetCommentatorId,
            'MatchId' => $GetMatchId,
        ];

        DB::table('commentatorslist')->insertGetId($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetCommentatorsByLocation(Request $request)
    {
        $FinalResult = [];

        $Latitude = $request->Latitude;
        $Longitude = $request->Longitude;

        $AllList = DB::table('commentators')
            ->where('Status', '=', '1')
            ->orderByRaw('Id DESC')
            ->get();

        for($n = 0; $n<count($AllList); $n++)
        {
            $GetLocation = explode(",", $AllList[$n]->Location);
            $GetLatitude = $GetLocation[0];
            $GeLongitude = $GetLocation[1];

            $Distance =  HelperFun::distance(floatval($Latitude), floatval($Longitude), floatval($GetLatitude), floatval($GeLongitude), "K");

            if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN")
            {
                $FinalResult[] = $AllList[$n];
            }
        }


        return $this->returnDate('Data', $FinalResult, "");
    }

    public function GetLeagueAndMatchPhotographers(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetMatchId = $request->MatchId;

        $FinalResult = [];

        $AllList = DB::table('photographerslist')
            ->where('MatchId', '=', $GetMatchId)
            ->get();

        for($y = 0; $y<count($AllList); $y++)
        {
            $Info = DB::table('photographers')
                ->where('Id', '=', $AllList[$y]->PhotographerId)
                ->first();

            $FinalResult[] = $Info;
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function AddBestPlayersToVote(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetMatchId = $request->MatchId;
        $GetFirstTeamId = $request->FirstTeamId;
        $GetSecondTeamId = $request->SecondTeamId;
        $GetFirstTeamFirstPlayerId = $request->FirstTeamFirstPlayerId;
        $GetFirstTeamSecondPlayerId = $request->FirstTeamSecondPlayerId;
        $GetSecondTeamFirstPlayerId = $request->SecondTeamFirstPlayerId;
        $GetSecondTeamSecondPlayerId = $request->SecondTeamSecondPlayerId;


        $FirstTeamPlayers = DB::table('bestplayersvote')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('MatchId', '=', $GetMatchId)
            ->where('TeamId', '=', $GetFirstTeamId)
            ->where('Status', '=', 1)
            ->first();

        if($FirstTeamPlayers)
        {
            if($FirstTeamPlayers->FirstPlayerId != $GetFirstTeamFirstPlayerId && $FirstTeamPlayers->FirstPlayerId != $GetFirstTeamSecondPlayerId ||
                $FirstTeamPlayers->SecondPlayerId != $GetFirstTeamSecondPlayerId && $FirstTeamPlayers->SecondPlayerId != $GetFirstTeamFirstPlayerId)
            {
                DB::table('bestplayersvote')
                    ->where('Id', $FirstTeamPlayers->Id)
                    ->update(['Status' => 0]);
            }
        }


        $SecondTeamPlayers = DB::table('bestplayersvote')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('MatchId', '=', $GetMatchId)
            ->where('TeamId', '=', $GetSecondTeamId)
            ->where('Status', '=', 1)
            ->first();

        if($SecondTeamPlayers)
        {
            if($SecondTeamPlayers->FirstPlayerId != $GetSecondTeamFirstPlayerId && $SecondTeamPlayers->FirstPlayerId != $GetSecondTeamSecondPlayerId ||
                $SecondTeamPlayers->SecondPlayerId != $GetSecondTeamSecondPlayerId && $SecondTeamPlayers->SecondPlayerId != $GetSecondTeamFirstPlayerId)
            {
                DB::table('bestplayersvote')
                    ->where('Id', $SecondTeamPlayers->Id)
                    ->update(['Status' => 0]);
            }
        }

        $First = DB::table('bestplayersvote')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('MatchId', '=', $GetMatchId)
            ->where('TeamId', '=', $GetFirstTeamId)
            ->where('FirstPlayerId', '=', $GetFirstTeamFirstPlayerId)
            ->where('SecondPlayerId', '=', $GetFirstTeamSecondPlayerId)
            ->where('Status', '=', 1)
            ->first();

        if(!$First)
        {
            $Data = [
                'LeagueId' => $GetLeagueId,
                'MatchId' => $GetMatchId,
                'TeamId' => $GetFirstTeamId,
                'FirstPlayerId' => $GetFirstTeamFirstPlayerId,
                'SecondPlayerId' => $GetFirstTeamSecondPlayerId,
            ];

            DB::table('bestplayersvote')->insertGetId($Data);
        }

        $Second = DB::table('bestplayersvote')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('MatchId', '=', $GetMatchId)
            ->where('TeamId', '=', $GetSecondTeamId)
            ->where('FirstPlayerId', '=', $GetSecondTeamFirstPlayerId)
            ->where('SecondPlayerId', '=', $GetSecondTeamSecondPlayerId)
            ->where('Status', '=', 1)
            ->first();

        if(!$Second)
        {
            $Data = [
                'LeagueId' => $GetLeagueId,
                'MatchId' => $GetMatchId,
                'TeamId' => $GetSecondTeamId,
                'FirstPlayerId' => $GetSecondTeamFirstPlayerId,
                'SecondPlayerId' => $GetSecondTeamSecondPlayerId,
            ];

            DB::table('bestplayersvote')->insertGetId($Data);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetAdminBestPlayersToVote(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetMatchId = $request->MatchId;
        $GetFirstTeamId = $request->FirstTeamId;
        $GetSecondTeamId = $request->SecondTeamId;

        $FinalResult = [];

        $AllList = DB::table('bestplayersvote')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('MatchId', '=', $GetMatchId)
            ->where('TeamId', '=', $GetFirstTeamId)
            ->orWhere('TeamId', '=', $GetSecondTeamId)
            ->where('Status', '=', 1)
            ->get();

        for($y = 0; $y<count($AllList); $y++)
        {
            $FirstPlayer = DB::table('players')
                ->where('Id', '=', $AllList[$y]->FirstPlayerId)
                ->first();

            $FinalResult[] = $FirstPlayer;

            $SecondPlayer = DB::table('players')
                ->where('Id', '=', $AllList[$y]->SecondPlayerId)
                ->first();

            $FinalResult[] = $SecondPlayer;
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetPlayerTeamIdDuringMatchId($PlayerId, $MatchId)
    {
        $MatchInfo = DB::table('matches')
            ->where('Id', '=', $MatchId)
            ->first();
        $MatchDate = $MatchInfo->MatchDate;

        $GetTeamPeriod = DB::select('SELECT hist.* FROM playerteamhistory hist WHERE hist.PlayerId = ? AND hist.StartDate <= ? AND (hist.EndDate IS NULL || hist.EndDate >= ?)', [$PlayerId, $MatchDate, $MatchDate]);

        if ($GetTeamPeriod) {
            return $GetTeamPeriod[0]->TeamId;
        }
        return null;
    }

    public function GetBestPlayersToVote(Request $request)
    {
        $GetLeagueId = $request->LeagueId;
        $GetMatchId = $request->MatchId;
        $GetFirstTeamId = $request->FirstTeamId;
        $GetSecondTeamId = $request->SecondTeamId;
        $GetUserId = $request->UserId;
        $GetAccountTypeId = $request->AccountTypeId;

        $FinalResult = [];

        $AllList = DB::table('bestplayersvote')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('MatchId', '=', $GetMatchId)
            ->where('Status', '=', 1)
            ->get();

        for($y = 0; $y<count($AllList); $y++)
        {
            $FirstPlayer = DB::table('players')
                ->where('Id', '=', $AllList[$y]->FirstPlayerId)
                ->first();

            $FirstPlayer->TeamId = $this->GetPlayerTeamIdDuringMatchId($AllList[$y]->FirstPlayerId, $GetMatchId);

            $SecondPlayer = DB::table('players')
                ->where('Id', '=', $AllList[$y]->SecondPlayerId)
                ->first();

            $SecondPlayer->TeamId = $this->GetPlayerTeamIdDuringMatchId($AllList[$y]->SecondPlayerId, $GetMatchId);

            $TotalVote = DB::table('voteing')
                ->where('BestPlayersVoteId', '=', $AllList[$y]->Id)
                ->count();

            $FirstPlayerTotalVote = DB::table('voteing')
                ->where('BestPlayersVoteId', '=', $AllList[$y]->Id)
                ->where('PlayerId', '=', $AllList[$y]->FirstPlayerId)
                ->count();

            $SecondPlayerTotalVote = DB::table('voteing')
                ->where('BestPlayersVoteId', '=', $AllList[$y]->Id)
                ->where('PlayerId', '=', $AllList[$y]->SecondPlayerId)
                ->count();

            $FirstPlayer->BestPlayersVoteId = $AllList[$y]->Id;
            $SecondPlayer->BestPlayersVoteId = $AllList[$y]->Id;

            $FirstPlayer->VotePercentages = "0";
            $SecondPlayer->VotePercentages = "0";

            if($FirstPlayerTotalVote)
                $FirstPlayer->VotePercentages = number_format((float)$FirstPlayerTotalVote / $TotalVote * 100, 0, '.', '');
            if($SecondPlayerTotalVote)
                $SecondPlayer->VotePercentages = number_format((float)$SecondPlayerTotalVote / $TotalVote * 100, 0, '.', '');

            $FirstPlayer->IsVoteTo = false;
            $SecondPlayer->IsVoteTo = false;

            if($GetUserId != 0)
            {
                $CheckIfUserVote = DB::table('voteing')
                    ->where('BestPlayersVoteId', '=', $AllList[$y]->Id)
                    ->where('UserId', '=', $GetUserId)
                    ->where('UserTypeId', '=', $GetAccountTypeId)
                    ->first();

                if($CheckIfUserVote)
                {
                    if($CheckIfUserVote->PlayerId == $FirstPlayer->Id)
                        $FirstPlayer->IsVoteTo = true;

                    if($CheckIfUserVote->PlayerId == $SecondPlayer->Id)
                        $SecondPlayer->IsVoteTo = true;
                }

            }

            $FinalResult[] = $FirstPlayer;

            $FinalResult[] = $SecondPlayer;

        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function VoteToPlayer(Request $request)
    {
        $GetBestPlayersVoteId = $request->BestPlayersVoteId;
        $GetPlayerId = $request->PlayerId;
        $GetUserId = $request->UserId;
        $GetUserTypeId = $request->UserTypeId;

        $Check = DB::table('voteing')
            ->where('BestPlayersVoteId', '=', $GetBestPlayersVoteId)
            ->where('PlayerId', '=', $GetPlayerId)
            ->where('UserId', '=', $GetUserId)
            ->where('UserTypeId', '=', $GetUserTypeId)
            ->first();

        if(!$Check)
        {
            $Data = [
                'BestPlayersVoteId' => $GetBestPlayersVoteId,
                'PlayerId' => $GetPlayerId,
                'UserId' => $GetUserId,
                'UserTypeId' => $GetUserTypeId,
            ];

            DB::table('voteing')->insert($Data);
        }

        return $this->returnDate('ExecuteStatus', true);

    }

    public function GetPlayersCard(Request $request)
    {
        $GetMatchId = $request->MatchId;
        $GetTeamId = $request->TeamId;
        $GetType = $request->Type;

        $FinalResult = [];

        $AllList = DB::table('matchdetails')
            ->where('MatchId', '=', $GetMatchId)
            ->where('TeamId', '=', $GetTeamId)
            ->where('Type', '=', $GetType)
            ->get();

        for($y = 0; $y<count($AllList); $y++)
        {
            $GetPlayer = DB::table('players')
                ->where('Id', '=', $AllList[$y]->PlayerId)
                ->first();

            $GetPlayer->Minutes = $AllList[$y]->Minutes;

            $FinalResult[] = $GetPlayer;
        }

        return $this->returnDate('Data', $FinalResult);
    }




}
