<?php

namespace App\Http\Controllers\APIs\LeaguePageAPIs;
use App\Http\Controllers\APIs\LeaguesController;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LeaguePageController extends Controller
{
    public function clearRoute()  {
        return Artisan::call('route:clear');
    }

    public function SendQuestionnaire() {

        $GetSelectedLeague = DB::table('leaguepage')
            ->where('Id', '=', 1)
            ->first();

        $GetLeagueId = $GetSelectedLeague->LeagueId;

        $SliderValue = $_GET['SliderValue'];
        $selectAccount = $_GET['selectAccount'];

        $radioiPhone = $_GET['radioiPhone'];
        $radioiPad = $_GET['radioiPad'];
        $radioandroid = $_GET['radioandroid'];
        $radioWeb = $_GET['radioWeb'];

        $Name = $_GET['Name'];
        $Phone = $_GET['Phone'];

        $Excellent = $_GET['Excellent'];
        $VeryGood = $_GET['VeryGood'];
        $notBad = $_GET['notBad'];
        $Bad = $_GET['Bad'];

        $Note = $_GET['Note'];
        $Suggestions = $_GET['Suggestions'];


        $AllowToContact = $_GET['AllowToContact'];

        $PlatformType = '';

        if ($radioiPhone) {
            $PlatformType = 'iPhone';
        } else if ($radioiPad) {
            $PlatformType = 'iPad';
        } else if ($radioandroid) {
            $PlatformType = 'Android';
        } else if ($radioWeb) {
            $PlatformType = 'Web';
        }

        $FeaturesEvaluation = '';

        if ($Excellent) {
            $FeaturesEvaluation = 'Excellent';
        } else if ($VeryGood) {
            $FeaturesEvaluation = 'Good';
        } else if ($notBad) {
            $FeaturesEvaluation = 'Not bad';
        } else if ($Bad) {
            $FeaturesEvaluation = 'Bad';
        }

		if ($SliderValue == 50) {
		   $SliderValue = 100;
		}

        DB::table('leaguequestionnaire')->insert([
            'LeagueId' => $GetLeagueId,
            'AccountTypeId' => $selectAccount,
            'OverallEvaluation' => $SliderValue,
            'PlatformType' => $PlatformType,
            'Name' => $Name,
            'Phone' => $Phone,
            'FeaturesEvaluation' => $FeaturesEvaluation,
            'Note' => $Note,
            'Suggestions' => $Suggestions,
            'AllowToContact' => $AllowToContact
        ]);

        return true;
    }

    public function leagueQuestionnaire() {

        $GetSelectedLeague = DB::table('leaguepage')
            ->where('Id', '=', 1)
            ->first();

        $GetLeagueId = $GetSelectedLeague->LeagueId;

        $LeagueInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

        $LeagueTopic = $LeagueInfo->Topic;

        $AccountType = [];
        $AccountType[] = ['Id' => 14, 'Name' => 'منظم دوريات'];
        $AccountType[] = ['Id' => 12, 'Name' => 'منظم مباريات'];
        $AccountType[] = ['Id' => 3, 'Name' => 'حكم'];
        $AccountType[] = ['Id' => 11, 'Name' => 'مشرف'];
        $AccountType[] = ['Id' => 5, 'Name' => 'مصور'];
        $AccountType[] = ['Id' => 4, 'Name' => 'معلق'];
        $AccountType[] = ['Id' => 1, 'Name' => 'لاعب'];
        $AccountType[] = ['Id' => 13, 'Name' => 'قائد فريق'];
        $AccountType[] = ['Id' => 15, 'Name' => 'اداري فريق'];
        $AccountType[] = ['Id' => 2, 'Name' => 'مدرب'];
        $AccountType[] = ['Id' => 0, 'Name' => 'زائر بدون تسجيل'];

        return view('leagueQuestionnaire', ['AccountType' => $AccountType, 'LeagueTopic' => $LeagueTopic]);
    }

    public function StatisticLogin() {
        $GetSelectedLeague = DB::table('leaguepage')
            ->where('Id', '=', 1)
            ->first();

        $GetLeagueId = $GetSelectedLeague->LeagueId;

        $LeagueInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

        $LeagueTopic = $LeagueInfo->Topic;

        return view('leagueStatisticLogin', ['LeagueTopic' => $LeagueTopic]);
    }

    public function leagueStatistic() {

        $GetSelectedLeague = DB::table('leaguepage')
            ->where('Id', '=', 1)
            ->first();

        $GetLeagueId = $GetSelectedLeague->LeagueId;

        $LeagueInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

        $LeagueTopic = $LeagueInfo->Topic;

        if(!session()->has('leagueStatisticLogin'))
        {
            Session::put('LeagueTopic', $LeagueTopic);
            return redirect()->route('StatisticLogin');
        }

        $CountTitle = 'العدد';

        if (isset($_COOKIE['SortBy']))
        {
            $EventType = $_COOKIE['SortBy'];

            if ($EventType == 'Goal') {
                $CountTitle = 'الاهداف';
            } else if ($EventType == 'Assistant') {
                $CountTitle = 'العدد';
            } else if ($EventType == 'YellowCard') {
                $CountTitle = 'الكروت الصفر';
            } else if ($EventType == 'RedCard') {
                $CountTitle = 'الكروت الحمر';
            }
        } else {
            $EventType = 'Goal';
            $CountTitle = 'عدد الاهداف';
        }

        if ($EventType == 'Assistant') {
            $MyArray = DB::select('SELECT tem.Logo \'Logo\', tem.NameAr \'Team\', player.Id \'PlayerId\', player.PlayerNumber, CONCAT(player.FirstName , " ", player.LastName) as PlayerName, COUNT(det.Id) \'Count\' FROM matches mat LEFT JOIN matchdetails det ON det.MatchId = mat.Id AND det.Type = \'Goal\' And det.AssistantId IS NOT NULL And det.AssistantId != 0 LEFT JOIN players player ON player.Id = det.AssistantId LEFT JOIN teams tem ON tem.Id = det.TeamId WHERE mat.LeagueId = :LeagueId AND det.Id IS NOT NULL GROUP BY tem.Logo, tem.NameAr,  player.Id,player.PlayerNumber, player.FirstName, player.LastName ORDER BY `Count`  DESC;', [$GetLeagueId]);
        } else {
            if ($GetLeagueId == 6) {
                $MyArray = DB::select('SELECT tem.Logo \'Logo\', tem.NameAr \'Team\', player.Id \'PlayerId\', player.PlayerNumber, CONCAT(player.FirstName , " ", player.LastName) as PlayerName, COUNT(det.Id) \'Count\' FROM matches mat LEFT JOIN matchdetails det ON det.MatchId = mat.Id AND det.Type = :EventType LEFT JOIN players player ON player.Id = det.PlayerId LEFT JOIN teams tem ON tem.Id = det.TeamId WHERE mat.LeagueId = :LeagueId AND det.Id IS NOT NULL AND player.Id NOT IN (263,133,115) GROUP BY tem.Logo, tem.NameAr,  player.Id,player.PlayerNumber, player.FirstName, player.LastName ORDER BY `Count`  DESC', [$EventType, $GetLeagueId]);
            } else{
                $MyArray = DB::select('SELECT tem.Logo \'Logo\', tem.NameAr \'Team\', player.Id \'PlayerId\', player.PlayerNumber, CONCAT(player.FirstName , " ", player.LastName) as PlayerName, COUNT(det.Id) \'Count\' FROM matches mat LEFT JOIN matchdetails det ON det.MatchId = mat.Id AND det.Type = :EventType LEFT JOIN players player ON player.Id = det.PlayerId LEFT JOIN teams tem ON tem.Id = det.TeamId WHERE mat.LeagueId = :LeagueId AND det.Id IS NOT NULL GROUP BY tem.Logo, tem.NameAr,  player.Id,player.PlayerNumber, player.FirstName, player.LastName ORDER BY `Count`  DESC', [$EventType, $GetLeagueId]);
            }
		}

       return view('leagueStatistic', ['MyTable' => $this->MakeTableReady($MyArray, $CountTitle), 'LeagueTopic' => $LeagueTopic]);
    }

    public function MakeTableReady($MyArray, $CountTitle) {

        return (string)view('reusable/leagueStatisticTable', ['MyArray' => $MyArray, 'CountTitle' => $CountTitle]);
    }

    public function IncreaseVisitor()
    {
        $GetSelectedLeague = DB::table('leaguepage')
            ->where('Id', '=', 1)
            ->first();

        if ($GetSelectedLeague) {
            $VisitorCount = $GetSelectedLeague->Visitor;
            $VisitorCount += 1;

            DB::table('leaguepage')
                ->where('Id', '=', 1)
                ->update(['Visitor' => $VisitorCount]);
        }

        return null;
    }

    public function CheckForUpdate()
    {
        $clearing = $this->clearRoute();

        $AllEvents = DB::table('leaguepageevents')
            ->orderByRaw('Id DESC')
            ->get();

        return $AllEvents;
    }

    public function LeagueTopicAndTeams()
    {
        $GetLeagueId = $this->GetActiveLeagueId();

        $GetLeagueInfo = DB::table('leagues')
            ->where('Id', '=', $GetLeagueId)
            ->first();

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

        $DomainUrl = 'https://sporent.net/public/images/';

        for($n = 0; $n<count($AllTeams); $n++)
        {
            $TeamId = $AllTeams[$n]->UserId;

            $TeamInfo = DB::table('teams')
                ->where('Id', '=', $TeamId)
                ->first();
            $FinalResult[] = ['Name' => $TeamInfo->NameAr, 'Logo' => $DomainUrl . $TeamInfo->Logo];
        }

        return ['Topic' => $GetLeagueInfo->Topic, 'Teams' => $FinalResult];
    }

    public function LeagueSponsors()
    {
        $GetLeagueId = $this->GetActiveLeagueId();

        $FinalResult = [];

        $Result = DB::table('sponsors')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        for($n = 0; $n<count($Result); $n++) {
            $FinalResult[] = ['Logo' => $Result[$n]->Logo];
        }

        return ['Sponsors' => null];
    }

    public function LeagueChampionship()
    {
        $GetLeagueId = $this->GetActiveLeagueId();

        $FinalResult = [];

        $DomainUrl = 'https://sporent.net/public/images/';

        $Champoinship = DB::table('leaguechampionship')
            ->where('LeagueId', '=', $GetLeagueId)
            ->get();

        for($y = 0; $y<count($Champoinship); $y++) {

            $ChampionshipId = $Champoinship[$y]->Id;
            $ChampionshipType = $Champoinship[$y]->Type;
            $ChampionshipTopic = $Champoinship[$y]->Topic;

            $FinalResultSorted = LeaguesController::CalculateStandings($GetLeagueId, $ChampionshipId);

            $FinalTeams = [];

            if ($ChampionshipType == 'Group') {
                for($i = 0; $i<count($FinalResultSorted); $i++) {
                    $TeamId = $FinalResultSorted[$i]['TeamId'];

                    $GetTeamInfo = DB::table('teams')
                        ->where('Id', '=', $TeamId)
                        ->first();

                    if ($GetTeamInfo) {
                        $TeamName = $GetTeamInfo->NameAr;
                        $TeamLogo = $GetTeamInfo->Logo;

                        $FinalTeams[] = ['Name' => $TeamName, 'Logo' => $DomainUrl . $TeamLogo, 'Play' => $FinalResultSorted[$i]['Play'], 'Win' => $FinalResultSorted[$i]['Win'], 'Draw' => $FinalResultSorted[$i]['Draw']
                            , 'Lost' => $FinalResultSorted[$i]['Lost'], 'ForThem' => $FinalResultSorted[$i]['ForThem'], 'A' => $FinalResultSorted[$i]['A'], 'GD' => $FinalResultSorted[$i]['GD'], 'Pts' => $FinalResultSorted[$i]['Pts']];
                    }
                }
            } else {
                $FinalTeams = $this->GetAllTeamsByChampionshipId($ChampionshipId);
            }

            $FinalResult[] = ['Topic' => $ChampionshipTopic, 'Type' => $ChampionshipType, 'Teams' => $FinalTeams];
        }

        return ['Championship' => $FinalResult];
    }

    public function GetAllTeamsByChampionshipId($GetChampionshipId)
    {
        $FinalResult = [];
        $Matchs = [];

        $DomainUrl = 'https://sporent.net/public/images/';

        $Matchs = DB::table('matches')->where('ChampionshipId','=' , $GetChampionshipId)->orderByRaw('MatchDate ASC')->get();

        for($y = 0; $y<count($Matchs); $y++)
        {
            $FirstTeam = [];
            $SecondTeam = [];

            $FirstTeamId = $Matchs[$y]->FirstTeamId;
            if($FirstTeamId != 0)
            {
                $FirstTeamInfo = DB::table('teams')->where('Id', '=', $FirstTeamId)->first();
                $ContGol = DB::table('matchdetails')
                    ->where('MatchId', '=', $Matchs[$y]->Id)
                    ->where('TeamId', '=', $FirstTeamId)
                    ->where('Type', '=', 'Goal')
                    ->count();

                $FirstContPenaltyShootouts = DB::table('matchdetails')
                    ->where('matchId', '=', $Matchs[$y]->Id)
                    ->where('TeamId', '=', $FirstTeamId)
                    ->where('Type', '=', 'GoalPenaltyShootouts')
                    ->count();

                $FirstTeam = ['Name' => $FirstTeamInfo->NameAr, 'Logo' => $DomainUrl . $FirstTeamInfo->Logo, 'Gol' => $ContGol, 'PenaltyShootoutsGoal' => $FirstContPenaltyShootouts];
            }

            $SecondTeamId = $Matchs[$y]->SecondTeamId;
            if($SecondTeamId != 0)
            {
                $SecondTeamInfo = DB::table('teams')->where('Id', '=', $SecondTeamId)->first();
                $ContGol = DB::table('matchdetails')
                    ->where('MatchId', '=', $Matchs[$y]->Id)
                    ->where('TeamId', '=', $SecondTeamId)
                    ->where('Type', '=', 'Goal')
                    ->count();

                $SecondContPenaltyShootouts = DB::table('matchdetails')
                    ->where('matchId', '=', $Matchs[$y]->Id)
                    ->where('TeamId', '=', $SecondTeamId)
                    ->where('Type', '=', 'GoalPenaltyShootouts')
                    ->count();

                $SecondTeam = ['Name' => $SecondTeamInfo->NameAr, 'Logo' => $DomainUrl . $SecondTeamInfo->Logo, 'Gol' => $ContGol, 'PenaltyShootoutsGoal' => $SecondContPenaltyShootouts];
            }

            $FinalResult[] = ['MatchDate' => date('Y-m-d h:i A', strtotime($Matchs[$y]->MatchDate)), 'FirstTeam' => $FirstTeam, 'SecondTeam' => $SecondTeam];
        }

        return $FinalResult;
    }


    public function CurrentMatch()
    {
        $GetLeagueId = $this->GetActiveLeagueId();

        $GetMatchId = $this->GetCurrentMatchIdInLeague($GetLeagueId);

       // $GetMatchId = 13;

        $FinalResult = [];

        if ($GetMatchId != null) {
            $FinalResult = $this->GetMatchDetails($GetMatchId);
        }

        return $FinalResult;
    }


    public function CurrentMatchTest()
    {
        $GetLeagueId = $this->GetActiveLeagueId();

        $FinalResult = [];

        $GetMatchId = $this->GetCurrentMatchIdInLeague($GetLeagueId);


        if ($GetMatchId != null) {
            $FinalResult = $this->GetMatchDetails($GetMatchId);
        }

        return $FinalResult;
    }

    public function GetCurrentMatchIdInLeague($GetLeagueId) {

        $Matches = DB::table('matches')->where('LeagueId','=' , $GetLeagueId)->where('Status','!=' , 2)->get();

        $today = Carbon::now()->format('Y-m-d  h:i A');
        $GetNowDate = strtotime($today);

        for($y = 0; $y<count($Matches); $y++) {
            $GetMatchId = $Matches[$y]->Id;
            $GetMatchDate = $Matches[$y]->MatchDate;


            $MatchDatePlus = Carbon::parse($GetMatchDate)->addMinutes(142)->format('Y-m-d  h:i A');
            $GetMatchDatePlus = strtotime($MatchDatePlus);
            $FinalMatchDate = strtotime($GetMatchDate);

            if ($GetNowDate >= $FinalMatchDate && $GetMatchDatePlus >= $GetNowDate) {
//                $Details = ['NowDate' => $today, 'MatchDate' => date('Y-m-d h:i A', strtotime($GetMatchDate)), 'MatchDatePlus' => $MatchDatePlus];
//                $LastMatches = ['MatchId' => $GetMatchId, 'MatchDateTime' => $GetMatchDate];
//                return ['Details' => $Details, 'Result' => $LastMatches];
                return $GetMatchId;
            }
        }

        return null;
    }


    public function GetMatchDetails($GetMatchId) {

        $MatchInfo = DB::table('matches')->where('Id','=' , $GetMatchId)->first();

        $FirstTeamId = $MatchInfo->FirstTeamId;
        $SecondTeamId = $MatchInfo->SecondTeamId;

        $DomainUrl = 'https://sporent.net/public/images/';

        // Start First Team

        $FirstTeamInfo = DB::table('teams')->where('Id', '=', $FirstTeamId)->first();

        $FirstTeamGoals = DB::table('matchdetails')
            ->where('MatchId', '=', $MatchInfo->Id)
            ->where('TeamId', '=', $FirstTeamId)
            ->where('Type', '=', 'Goal')
            ->get();

        $FirstTeamGoalsDetails = [];

        for($y = 0; $y<count($FirstTeamGoals); $y++) {
            $GetPlayerId = $FirstTeamGoals[$y]->PlayerId;
            $GetMinutes = $FirstTeamGoals[$y]->Minutes;
            $GetSeconds = $FirstTeamGoals[$y]->Seconds;

            $PlayerInfo = DB::table('players')->where('Id', '=', $GetPlayerId)->first();
            $GetFirstName = $PlayerInfo->FirstName;
            $GetLastName = $PlayerInfo->LastName;
            $GetImage = $PlayerInfo->Image;

            $FirstTeamGoalsDetails[] = ['Name' => $GetFirstName . ' ' . $GetLastName, 'Image' => $DomainUrl . $GetImage, 'Time' => $GetMinutes . ':' . $GetSeconds];
        }

        $FirstContPenaltyShootouts = DB::table('matchdetails')
            ->where('matchId', '=', $MatchInfo->Id)
            ->where('TeamId', '=', $FirstTeamId)
            ->where('Type', '=', 'GoalPenaltyShootouts')
            ->count();

        $FirstTeam = ['Name' => $FirstTeamInfo->NameAr, 'Logo' => $DomainUrl . $FirstTeamInfo->Logo, 'Gol' => count($FirstTeamGoals), 'PenaltyShootoutsGoal' => $FirstContPenaltyShootouts, 'GoalsDetails' => $FirstTeamGoalsDetails];

        // End First Team

        // Start Second Team

        $SecondTeamInfo = DB::table('teams')->where('Id', '=', $SecondTeamId)->first();

        $SecondTeamGoals = DB::table('matchdetails')
            ->where('MatchId', '=', $MatchInfo->Id)
            ->where('TeamId', '=', $SecondTeamId)
            ->where('Type', '=', 'Goal')
            ->get();

        $SecondTeamGoalsDetails = [];

        for($y = 0; $y<count($SecondTeamGoals); $y++) {
            $GetPlayerId = $SecondTeamGoals[$y]->PlayerId;
            $GetMinutes = $SecondTeamGoals[$y]->Minutes;
            $GetSeconds = $SecondTeamGoals[$y]->Seconds;

            $PlayerInfo = DB::table('players')->where('Id', '=', $GetPlayerId)->first();
            $GetFirstName = $PlayerInfo->FirstName;
            $GetLastName = $PlayerInfo->LastName;
            $GetImage = $PlayerInfo->Image;

            $SecondTeamGoalsDetails[] = ['Name' => $GetFirstName . ' ' . $GetLastName, 'Image' => $DomainUrl . $GetImage, 'Time' => $GetMinutes . ':' . $GetSeconds];
        }

        $SecondContPenaltyShootouts = DB::table('matchdetails')
            ->where('matchId', '=', $MatchInfo->Id)
            ->where('TeamId', '=', $SecondTeamId)
            ->where('Type', '=', 'GoalPenaltyShootouts')
            ->count();

        $SecondTeam = ['Name' => $SecondTeamInfo->NameAr, 'Logo' => $DomainUrl . $SecondTeamInfo->Logo, 'Gol' => count($SecondTeamGoals), 'PenaltyShootoutsGoal' => $SecondContPenaltyShootouts, 'GoalsDetails' => $SecondTeamGoalsDetails];

        // End Second Team

        return  ['FirstTeam' => $FirstTeam, 'SecondTeam' => $SecondTeam];
    }


    public function UpcomingMatch()
    {
        $GetLeagueId = $this->GetActiveLeagueId();

        $GetUpcomingMatchId = $this->GetUpcomingMatchIdInLeague($GetLeagueId);

        $FinalResult = [];

        if ($GetUpcomingMatchId != null) {
            $MatchInfo = DB::table('matches')->where('Id', '=', $GetUpcomingMatchId)->first();

            $DomainUrl = 'https://sporent.net/public/images/';

            $FirstTeam = [];
            $SecondTeam = [];

            $FirstTeamId = $MatchInfo->FirstTeamId;

            if($FirstTeamId != 0) {
                $FirstTeamInfo = DB::table('teams')->where('Id', '=', $FirstTeamId)->first();
                $FirstTeam = ['Name' => $FirstTeamInfo->NameAr, 'Logo' => $DomainUrl . $FirstTeamInfo->Logo];
            }

            $SecondTeamId = $MatchInfo->SecondTeamId;
            if($SecondTeamId != 0) {
                $SecondTeamInfo = DB::table('teams')->where('Id', '=', $SecondTeamId)->first();
                $SecondTeam = ['Name' => $SecondTeamInfo->NameAr, 'Logo' => $DomainUrl . $SecondTeamInfo->Logo];
            }

            $DayCount = $this->GetDayCount($MatchInfo->MatchDate);

            $FinalResult = ['MatchDate' => $DayCount, 'FirstTeam' => $FirstTeam, 'SecondTeam' => $SecondTeam];
        }

        return $FinalResult;
    }

    public function GetDayCount($Date) {
        $FinalMatchDate = date('Y-m-d h:i A', strtotime($Date));
        $MatchDateArray = explode(" ", $FinalMatchDate);
        $GetDate = $MatchDateArray[0];
        $GetTime = $MatchDateArray[1];
        $GetAMPM = $MatchDateArray[2];

        $Location = explode("-", $GetDate);
        $GetYear = $Location[0];
        $GetMonth = $Location[1];
        $GetDay = $Location[2];

        $Diff = now()->diffInDays($FinalMatchDate);

        $DiffString = '';

        if ($Diff == 0) {
            $DiffString = 'اليوم';
        } else if ($Diff == 1) {
            $DiffString = 'غداً';
        } else  {
            $DiffString = $this->ConvertMonth($GetMonth) . ' ' . $GetDay;
        }

        return ['Date' => $DiffString, 'Time' => $GetTime . ' ' . $this->ConvertAMPMToArabic($GetAMPM)];
    }

    public function GetUpcomingMatchIdInLeague($GetLeagueId) {

        $GetLeagueId = $this->GetActiveLeagueId();

        $GetCurrentMatchId = $this->GetCurrentMatchIdInLeague($GetLeagueId);

        $Matches = DB::table('matches')->where('LeagueId', '=', $GetLeagueId)->where('Status', '!=', '2')->orderByRaw('DATE(MatchDate) ASC')->get();

        for ($y = 0; $y < count($Matches); $y++)
        {
            $GetMatchId = $Matches[$y]->Id;

            if ($GetCurrentMatchId != null && $GetMatchId == $GetCurrentMatchId) {
                continue;
            }

            return $GetMatchId;
        }

        return null;
    }

    public function OtherMatches()
    {
        $GetLeagueId = $this->GetActiveLeagueId();

        $GetCurrentMatchId = $this->GetCurrentMatchIdInLeague($GetLeagueId);

        $GetUpcomingMatchId = $this->GetUpcomingMatchIdInLeague($GetLeagueId);

        $Matches = DB::table('matches')->where('LeagueId','=' , $GetLeagueId)->orderByRaw('MatchDate ASC')->get();

        $DomainUrl = 'https://sporent.net/public/images/';

        $FinalResult = [];

        for($y = 0; $y<count($Matches); $y++)
        {
            $GetMatchId = $Matches[$y]->Id;

            if ($GetCurrentMatchId != null && $GetMatchId == $GetCurrentMatchId) {
                continue;
            }

            if ($GetUpcomingMatchId != null && $GetMatchId == $GetUpcomingMatchId) {
                continue;
            }

            $FirstTeam = [];
            $SecondTeam = [];

            $FirstTeamId = $Matches[$y]->FirstTeamId;
            if($FirstTeamId != 0)
            {
                $FirstTeamInfo = DB::table('teams')->where('Id', '=', $FirstTeamId)->first();
                $ContGol = DB::table('matchdetails')
                    ->where('MatchId', '=', $Matches[$y]->Id)
                    ->where('TeamId', '=', $FirstTeamId)
                    ->where('Type', '=', 'Goal')
                    ->count();

                $ContPenaltyShootouts = DB::table('matchdetails')
                    ->where('matchId', '=', $Matches[$y]->Id)
                    ->where('TeamId', '=', $FirstTeamId)
                    ->where('Type', '=', 'GoalPenaltyShootouts')
                    ->count();

                $FirstTeam = ['Name' => $FirstTeamInfo->NameAr, 'Logo' => $DomainUrl . $FirstTeamInfo->Logo, 'Gol' => $ContGol, 'PenaltyShootoutsGoal' => $ContPenaltyShootouts];
            }

            $SecondTeamId = $Matches[$y]->SecondTeamId;
            if($SecondTeamId != 0)
            {
                $SecondTeamInfo = DB::table('teams')->where('Id', '=', $SecondTeamId)->first();
                $ContGol = DB::table('matchdetails')
                    ->where('MatchId', '=', $Matches[$y]->Id)
                    ->where('TeamId', '=', $SecondTeamId)
                    ->where('Type', '=', 'Goal')
                    ->count();

                $ContPenaltyShootouts = DB::table('matchdetails')
                    ->where('matchId', '=', $Matches[$y]->Id)
                    ->where('TeamId', '=', $SecondTeamId)
                    ->where('Type', '=', 'GoalPenaltyShootouts')
                    ->count();

                $SecondTeam = ['Name' => $SecondTeamInfo->NameAr, 'Logo' => $DomainUrl . $SecondTeamInfo->Logo, 'Gol' => $ContGol, 'PenaltyShootoutsGoal' => $ContPenaltyShootouts];
            }

            $GetSplitDate = $this->SplitMatchDate($Matches[$y]->MatchDate);

            $FinalResult[] = ['MatchDate' => $GetSplitDate, 'FirstTeam' => $FirstTeam, 'SecondTeam' => $SecondTeam];
        }

        return $FinalResult;
    }

    public function SplitMatchDate($MatchDate) {
        $FinalMatchDate = date('Y-m-d h:i A', strtotime($MatchDate));
        $MatchDateArray = explode(" ", $FinalMatchDate);
        $GetDate = $MatchDateArray[0];
        $GetTime = $MatchDateArray[1];
        $GetAMPM = $MatchDateArray[2];

        $Location = explode("-", $GetDate);
        $GetYear = $Location[0];
        $GetMonth = $Location[1];
        $GetDay = $Location[2];

        return ['Month' => $this->ConvertMonth($GetMonth), 'Day' => $GetDay, 'Time' => $GetTime . ' ' . $this->ConvertAMPMToArabic($GetAMPM)];
    }

    public function ConvertMonth($Number) {
        if ($Number == 1) {
            return 'يناير';
        } else if ($Number == 2) {
            return 'فبراير';
        } else if ($Number == 3) {
            return 'مارس';
        } else if ($Number == 4) {
            return 'أبريل';
        } else if ($Number == 5) {
            return 'مايو';
        } else if ($Number == 6) {
            return 'يونيو';
        } else if ($Number == 7) {
            return 'يوليو';
        } else if ($Number == 8) {
            return 'أغسطس';
        } else if ($Number == 9) {
            return 'سبتمبر';
        } else if ($Number == 10) {
            return 'أكتوبر';
        } else if ($Number == 11) {
            return 'نوفمبر';
        } else if ($Number == 12) {
            return 'ديسمبر';
        }
        return '';
    }

    public function ConvertAMPMToArabic($String) {
        if ($String == 'AM') {
            return 'ص';
        } else if ($String == 'PM') {
            return 'م';
        }
        return '';
    }

    public function TheNews()
    {
        $GetLeagueId = $this->GetActiveLeagueId();

        $FinalResult = [];

        $Result = DB::table('news')
            ->where('LeagueId', '=', $GetLeagueId)
            ->where('Status', '=', '1')
            ->get();

        for($y = 0; $y<count($Result); $y++) {

            $FinalResult[] = ['Details' => $Result[$y]->Details];
        }

        return ['News' => $FinalResult];
    }

    public function LeagueVideo()
    {
        $FinalResult = [];

        $GetSelectedLeague = DB::table('leaguepage')
            ->where('Id', '=', 1)
            ->first();

        if ($GetSelectedLeague) {
            $FinalResult = ['VideoUrl' => $GetSelectedLeague->Video];
        }

        return $FinalResult;
    }

    public function GetActiveLeagueId() {

        $GetSelectedLeague = DB::table('leaguepage')
            ->where('Id', '=', 1)
            ->first();

        if ($GetSelectedLeague) {
            return $GetSelectedLeague->LeagueId;
        }

        return null;
    }

    static function UpdatePageEvent($Events) {

        if ($Events != null) {
            $LeagueTopicAndTeams = $Events['LeagueTopicAndTeams'];
            $LeagueSponsors = $Events['LeagueSponsors'];
            $LeagueChampionship = $Events['LeagueChampionship'];
            $TheNews = $Events['TheNews'];
            $CurrentMatch = $Events['CurrentMatch'];
            $UpcomingMatch = $Events['UpcomingMatch'];
            $OtherMatches = $Events['OtherMatches'];
            $LeagueVideo = $Events['LeagueVideo'];

            if ($LeagueTopicAndTeams == true) {
                LeaguePageController::UpdateTable(1);
            }

            if ($LeagueSponsors == true) {
                LeaguePageController::UpdateTable(2);
            }

            if ($LeagueChampionship == true) {
                LeaguePageController::UpdateTable(3);
            }

            if ($TheNews == true) {
                LeaguePageController::UpdateTable(4);
            }

            if ($CurrentMatch == true) {
                LeaguePageController::UpdateTable(5);
            }

            if ($UpcomingMatch == true) {
                LeaguePageController::UpdateTable(6);
            }

            if ($OtherMatches == true) {
                LeaguePageController::UpdateTable(7);
            }

            if ($LeagueVideo == true) {
                LeaguePageController::UpdateTable(8);
            }

            return 'Updated';
        }
        return 'null';
    }

    static function UpdateTable($Id) {
        $DateNow = now();
        DB::table('leaguepageevents')
            ->where('Id', $Id)
            ->update(['LastUpdate' => $DateNow]);
    }

}


