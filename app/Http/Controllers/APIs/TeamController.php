<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\String_;

class TeamController extends Controller
{
    use GeneralTrait;

    public function GetTeamInfo(Request $request)
    {
        $GetTeamId = $request->TeamId;
        $GetUserLoginId = $request->UserLoginId;
        $GetUserLoginAccountTypeId = $request->UserLoginAccountTypeId;

        $Result = DB::table('teams')
            ->where('Id', '=', $GetTeamId)
            ->first();

        $GetPaymentInfo = DB::table('payments')
            ->where('PayForId', '=', $GetTeamId)
            ->where('FeeId', '=', 1) // The Id of Team creation fee in table 'Fee'
            ->where('PaymentStatus', '=', 1)
            ->first();

        if ($Result->TeamLeaderId != null && $Result->TeamLeaderId != 0) {
            $Result->TeamLeader = DB::table('players')
                ->where('Id', '=', $Result->TeamLeaderId)
                ->first();
        } else {
            $Result->TeamLeader = null;
        }

        if ($GetUserLoginId != 0) {
            $Follow = HelperAPIsFun::CheckFollowing($GetUserLoginId, $GetUserLoginAccountTypeId, $GetTeamId, 13); // 13 The account type Id of Team

            if ($Follow != null && $Follow->IsFollow == '1') {
                $Result->IsFollowing = true;
            } else {
                $Result->IsFollowing = false;
            }
        } else {
            $Result->IsFollowing = false;
        }

        if ($GetPaymentInfo) {
            $Result->TeamFeePaymentStatus = $GetPaymentInfo->PaymentStatus;
        } else {
            $Result->TeamFeePaymentStatus = 0;
        }

        return $this->returnDate('Data', $Result);
    }

    public function GetTeamPlayers(Request $request)
    {
        $Result = DB::table('players')
            ->where('TeamId', '=', $request->TeamId)
            ->get();

        return $this->returnDate('Data', $Result);
    }

    public function GetTeamPlayersForAttendance(Request $request)
    {
        $TeamId = $request->TeamId;
        $MatchId = $request->MatchId;

        $FinalResult = [];

        $PlayersList = DB::table('players')
            ->where('TeamId', '=', $TeamId)
            ->get();

        for ($y = 0; $y < count($PlayersList); $y++) {
            $PlayerId = $PlayersList[$y]->Id;

            $CheckAttend = DB::table('playersattendance')
                ->where('TeamId', '=', $TeamId)
                ->where('MatchId', '=', $MatchId)
                ->where('PlayerId', '=', $PlayerId)
                ->first();

            $GetIsAttend = 0;

            if ($CheckAttend) {
                $GetIsAttend = $CheckAttend->IsAttend;
            }

            $GetPosition = null;

            if ($PlayersList[$y]->PositionId != null && $PlayersList[$y]->PositionId != 0) {
                $GetPosition = DB::table('playerposition')
                    ->where('Id', '=', $PlayersList[$y]->PositionId)
                    ->first();
            }

            $FinalResult[] = ['Id' => $PlayersList[$y]->Id, 'TeamId' => $TeamId, 'PlayerNumber' => $PlayersList[$y]->PlayerNumber, 'Name' => $PlayersList[$y]->FirstName . ' ' . $PlayersList[$y]->LastName,
                'Image' => $PlayersList[$y]->Image, 'Position' => $GetPosition, 'IsAttend' => $GetIsAttend];
        }

        $ResultSorted = collect($FinalResult)->sortBy('PlayerNumber')->sortBy('IsAttend')->toArray();

        $FinalResultSorted = [];

        foreach($ResultSorted as $Object) {
            $FinalResultSorted[] = $Object;
        }

        return $this->returnDate('Data', $FinalResultSorted);
    }

    public function ActionOfTeamPlayerAttendance(Request $request)
    {
        $PlayerId = $request->PlayerId;
        $TeamId = $request->TeamId;
        $MatchId = $request->MatchId;
        $IsAttend = $request->IsAttend;
        $ById = $request->ById;
        $ByAccountTypeId = $request->ByAccountTypeId;

        $CheckAttend = DB::table('playersattendance')
            ->where('TeamId', '=', $TeamId)
            ->where('MatchId', '=', $MatchId)
            ->where('PlayerId', '=', $PlayerId)
            ->first();

        if ($CheckAttend) {
            DB::table('playersattendance')
                ->where('Id', $CheckAttend->Id)
                ->update(['IsAttend' => $IsAttend]);
        } else {
            $Data = [
                'MatchId' => $MatchId,
                'TeamId' => $TeamId,
                'PlayerId' => $PlayerId,
                'IsAttend' => $IsAttend,
                'ById' => $ById,
                'ByAccountTypeId' => $ByAccountTypeId
            ];

            DB::table('playersattendance')->insertGetId($Data);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetTeamPlayersWithInfo(Request $request)
    {
        $TeamId = $request->TeamId;
        $MatchId = $request->MatchId;
        $EventType = $request->EventType;

        $GetMatchInfo = DB::table('matches')
            ->where('Id', '=', $MatchId)
            ->first();

        if ($GetMatchInfo->LeagueId != null) {
            $LeagueId = $GetMatchInfo->LeagueId;
            $Result = DB::select('SELECT p.Id, p.PlayerNumber, CONCAT(p.FirstName, \' \', p.LastName) AS PlayerName, md.Type AS EventType, COUNT(md.Type) AS EventCount FROM players p LEFT JOIN matchdetails md ON md.TeamId = ? AND md.PlayerId = p.Id AND md.Type = ? AND md.MatchId IN (SELECT m.Id FROM matches m WHERE m.LeagueId = ?) WHERE p.TeamId = ? GROUP BY p.Id, p.PlayerNumber, p.FirstName, p.LastName, md.Type ORDER BY p.PlayerNumber ASC', [$TeamId, $EventType, $LeagueId, $TeamId]);
        } else {
            $Result = DB::select('SELECT DISTINCT p.Id, p.PlayerNumber, CONCAT(p.FirstName, \' \', p.LastName) AS PlayerName, md.Type AS EventType, COUNT(md.Type) AS EventCount FROM players p LEFT JOIN matchdetails md ON md.TeamId = ? AND md.PlayerId = p.Id AND md.Type = ? AND md.MatchId = ? WHERE p.TeamId = ? GROUP BY p.Id, p.PlayerNumber, p.FirstName, p.LastName, md.Type ORDER BY p.PlayerNumber ASC', [$TeamId, $EventType, $MatchId, $TeamId]);
        }

        return $this->returnDate('Data', $Result);
    }

    public function GetTeamPlayersForMange(Request $request)
    {
        $FinalResult = [];

        $Result = DB::table('players')
            ->where('TeamId', '=', $request->TeamId)
            ->get();

        for ($y = 0; $y < count($Result); $y++) {

            $Result[$y]->IsWaitingToAcceptJoinRequest = 0;

            if ($Result[$y]->PositionId != null && $Result[$y]->PositionId != 0) {
                $Result[$y]->Position = DB::table('playerposition')
                    ->where('Id', '=', $Result[$y]->PositionId)
                    ->first();
            }

            $FinalResult[] = $Result[$y];
        }

        $ResultJoinRequest = DB::table('teamjoinrequest')
            ->where('TeamId', '=', $request->TeamId)
            ->where('IsAccepted', '=', null)
            ->where('Status', '=', 1)
            ->get();

        for ($n = 0; $n < count($ResultJoinRequest); $n++) {
            $GetPlayer = DB::table('players')
                ->where('Id', '=', $ResultJoinRequest[$n]->UserId)
                ->first();

            if ($GetPlayer->PositionId != null && $GetPlayer->PositionId != 0) {
                $GetPlayer->Position = DB::table('playerposition')
                    ->where('Id', '=', $GetPlayer->PositionId)
                    ->first();
            }

            $GetPlayer->IsWaitingToAcceptJoinRequest = 1;

            $FinalResult[] = $GetPlayer;

        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetTeamMedia(Request $request)
    {
        $FinalResult = [];

        $Result = DB::table('players')
            ->where('TeamId', '=', $request->TeamId)
            ->get();

        for ($y = 0; $y < count($Result); $y++) {
            $PlayerId = $Result[$y]->Id;

            $GetAttachments = DB::table('attachments')
                ->where('UserId', '=', $PlayerId)
                ->where('UserAccountTypeId', '=', '1')
                ->where('ShareWithTeam', '=', '1')
                ->where('IsDeleted', '=', '0')
                ->orderByRaw('Id DESC')
                ->get();

            for ($n = 0; $n < count($GetAttachments); $n++) {
                $FinalResult[] = $GetAttachments[$n];
            }
        }

        return $this->returnDate('Data', $FinalResult, "");
    }

    public function GetAllTeamsByLocation(Request $request)
    {
        $FinalResult = [];

        $Latitude = $request->Latitude;
        $Longitude = $request->Longitude;
        $IsFromAllCities = $request->IsFromAllCities;

        $AllTeams = DB::table('teams')
            ->where('Status', '=', '1')
            ->orderByRaw('Id DESC')
            ->get();

        if ($IsFromAllCities == 0) {
            for ($n = 0; $n < count($AllTeams); $n++) {
                $TeamLocation = explode(",", $AllTeams[$n]->Location);
                $TeamLatitude = $TeamLocation[0];
                $TeamLongitude = $TeamLocation[1];

                $Distance = HelperFun::distance(floatval($Latitude), floatval($Longitude), floatval($TeamLatitude), floatval($TeamLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN") {
                    $FinalResult[] = $AllTeams[$n];
                }
            }

        } else {
            $FinalResult = $AllTeams;
        }

        return $this->returnDate('Data', $FinalResult, "");
    }


    public function GetAllTeams(Request $request)
    {
        $AllTeams = DB::table('teams')
            ->where('Status', '=', '1')
            ->orderByRaw('Id DESC')
            ->get();

        return $this->returnDate('Data', $AllTeams, "");
    }

    public function GetAllTeamsByStatus(Request $request)
    {
        $GetStatus = $request->Status;
        $AllTeams = [];

        if ($GetStatus == "WaitingApproval") {
            $AllTeams = DB::table('teams')
                ->where('IsApproved', '=', '0')
                ->orderByRaw('Id DESC')
                ->get();

        } else if ($GetStatus == "Active") {
            $AllTeams = DB::table('teams')
                ->where('IsApproved', '=', '1')
                ->where('Status', '=', '1')
                ->orderByRaw('Id DESC')
                ->get();

        } else if ($GetStatus == "Inactive") {
            $AllTeams = DB::table('teams')
                ->where('IsApproved', '=', '1')
                ->where('Status', '=', '0')
                ->orderByRaw('Id DESC')
                ->get();
        }


        return $this->returnDate('Data', $AllTeams, "");
    }

    public function ApproveTeam(Request $request)
    {
        $GetTeamId = $request->TeamId;
        $GetNewStatus = $request->NewStatus;
        $GetApproveById = $request->ApproveById;
        $GetApproveByAccountTypeId = $request->ApproveByAccountTypeId;

        $GetDate = Carbon::now();

        $LeaderTokenId = "";
        $message = "";
        $Lang = 'ar';

        $GetTeamInfo = DB::table('teams')
            ->where('Id', '=', $GetTeamId)
            ->first();

        if ($GetTeamInfo) {
            $GetTeamLeaderInfo = DB::table('players')
                ->where('Id', '=', $GetTeamInfo->TeamLeaderId)
                ->first();
            if ($GetTeamLeaderInfo) {
                if ($GetTeamLeaderInfo->Lang == 'en') {
                    $Lang = 'en';
                }

                $LeaderTokenId = $GetTeamLeaderInfo->TokenId;
            }
        }

        if ($GetNewStatus == "Approve") {
            $Data = [
                'IsApproved' => 1,
                'Status' => 1,
                'ApprovedBy' => $GetApproveById,
                'ApprovedByAccountTypeId' => $GetApproveByAccountTypeId,
                'ApprovedDate' => $GetDate,
            ];

            // Active Joins request to team players
            DB::table('teamjoinrequest')
                ->where('TeamId', $GetTeamId)
                ->where('Status', -1)
                ->update(['Status' => 1]);
				
			// History
            $HistoryData = [
                'TeamId' => $GetTeamId,
                'PlayerId' => $GetTeamInfo->TeamLeaderId,
                'StartDate' => $GetDate
            ];

            DB::table('playerteamhistory')->insertGetId($HistoryData);
            // End History

            if ($Lang == 'en') {
                $message = "Congratulations, your team has been approved and activated";
            } else {
                $message = "تهانينا, تم اعتماد وتفعيل فريقكم";
            }

        } else if ($GetNewStatus == "Active") {
            $Data = [
                'Status' => 1,
            ];

            if ($Lang == 'en') {
                $message = "Your team has been activated";
            } else {
                $message = "تم تفعيل فريقكم";
            }

        } else {
            $Data = [
                'Status' => 0,
            ];

            if ($Lang == 'en') {
                $message = "Your team has been deactivated";
            } else {
                $message = "تم تعليق فريقكم";
            }
        }

        DB::table('teams')
            ->where('Id', $GetTeamId)
            ->update($Data);

        if ($LeaderTokenId != null && $LeaderTokenId != "") {
            (new GeneralController)->SendSingleNotifications($message, $LeaderTokenId);
        }

        if ($GetNewStatus == "Approve") {
            // Send Invitation
            $GetTeamJoinRequest = DB::table('teamjoinrequest')
                ->where('TeamId', '=', $GetTeamId)
                ->where('IsAccepted', '=', null)
                ->where('Status', '=', 1)
                ->get();

            for ($b = 0; $b < count($GetTeamJoinRequest); $b++) {
                $GetPlayerId = $GetTeamJoinRequest[$b]->UserId;
                $GetPlayerInfo = DB::table('players')
                    ->where('Id', '=', $GetPlayerId)
                    ->first();

                $PlayerTokenId = $GetPlayerInfo->TokenId;
                if ($GetPlayerInfo->Lang == 'en') {
                    $SingleMessage = 'You have invite to join ' . $GetTeamInfo->NameEn;
                } else {
                    $SingleMessage = 'لديك دعوة للانظمام لـ ' . $GetTeamInfo->NameAr;
                }

                if ($PlayerTokenId != null && $PlayerTokenId != "") {
                    (new GeneralController)->SendSingleNotifications($SingleMessage, $PlayerTokenId);
                }
            }
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetJoinToTeamRequest(Request $request)
    {
        $MyArray = DB::table('teamjoinrequest')
            ->where('UserId', '=', $request->UserId)
            ->where('AccountTypeId', '=', $request->UserAccountTypeId)
            ->where('IsAccepted', '=', null)
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->first();

        if ($MyArray != null) {
            $GetTeamId = $MyArray->TeamId;

            $MyArray->TeamInfo = DB::table('teams')
                ->where('Id', '=', $GetTeamId)
                ->first();
        }

        return $this->returnDate('Data', $MyArray, "");
    }

    public function ResponseOfRequestJoinToTeam(Request $request)
    {
        $GetRequestId = $request->RequestId;
        $GetUserId = $request->UserId;
        $GetAccountTypeId = $request->AccountTypeId;
        $GetIsAccepted = $request->IsAccepted;
        $GetTeamId = $request->TeamId;

        $GetDate = Carbon::now();

        if ($GetIsAccepted == 1) {
            DB::table('players')
                ->where('Id', $GetUserId)
                ->update(['TeamId' => $GetTeamId]);
        }

        // History
        $CheckIfHasTeamBefore = DB::table('playerteamhistory')
            ->where('PlayerId', '=', $GetUserId)
            ->where('EndDate', '=', null)
            ->first();

        if ($CheckIfHasTeamBefore) {
            DB::table('playerteamhistory')
                ->where('Id', $CheckIfHasTeamBefore->Id)
                ->update(['EndDate' => $GetDate]);
        }

        $HistoryData = [
            'TeamId' => $GetTeamId,
            'PlayerId' => $GetUserId,
            'StartDate' => $GetDate
        ];

        DB::table('playerteamhistory')->insertGetId($HistoryData);
        // End History

        $Data = [
            'IsAccepted' => $GetIsAccepted,
            'Status' => 0,
            'ResponseDate' => $GetDate,
        ];

        DB::table('teamjoinrequest')
            ->where('Id', $GetRequestId)
            ->update($Data);

        $TeamInfo = DB::table('teams')
            ->where('Id', '=', $GetTeamId)
            ->first();

        $TeamLeaderInfo = DB::table('players')
            ->where('Id', '=', $TeamInfo->TeamLeaderId)
            ->first();

        $RequestedPlayerInfo = DB::table('players')
            ->where('Id', '=', $GetUserId)
            ->first();

        $GetTokenId = $TeamLeaderInfo->TokenId;

        if ($TeamLeaderInfo->Lang == 'en') {
            if ($GetIsAccepted == 1) {
                $message = $RequestedPlayerInfo->FirstName . ' ' . $RequestedPlayerInfo->LastName . ' is joined to your team';
            } else {
                $message = $RequestedPlayerInfo->FirstName . ' ' . $RequestedPlayerInfo->LastName . ' is rejected to join your team';
            }
        } else {
            if ($GetIsAccepted == 1) {
                $message = $RequestedPlayerInfo->FirstName . ' ' . $RequestedPlayerInfo->LastName . ' قبل الانظمام لفريقك';
            } else {
                $message = $RequestedPlayerInfo->FirstName . ' ' . $RequestedPlayerInfo->LastName . ' رفض الانظمام لفريقك';
            }
        }

        (new GeneralController)->SendSingleNotifications($message, $GetTokenId);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function CreateNewTeam(Request $request)
    {
        $ArName = $request->ArName;
        $EnName = $request->EnName;
        $Since = $request->Since;
        $Location = $request->Location;
        $Logo = $request->Logo;
        $TeamLeaderId = $request->TeamLeaderId;
        $TeamLeaderAccountTypeId = $request->TeamLeaderAccountTypeId;

        $CheckName = DB::table('teams')
            ->where('NameAr', '=', $ArName)
            ->orWhere('NameEn', '=', $EnName)
            ->first();

        if ($CheckName) {
            return $this->returnDate('ExecuteStatus', false);
        }

        $GetDate = Carbon::now();

        $NewImageName = HelperAPIsFun::UploadImage($Logo, 'png');

        $Data = [
            'Logo' => $NewImageName,
            'NameAr' => $ArName,
            'NameEn' => $EnName,
            'Since' => $Since,
            'TeamLeaderId' => $TeamLeaderId,
            'Location' => $Location,
            'Status' => -1,
        ];

        $TeamId = DB::table('teams')->insertGetId($Data);

        $TeamLeaderData = [
            'TeamId' => $TeamId,
            'UserId' => $TeamLeaderId,
            'AccountTypeId' => 1,
            'IsAccepted' => 1,
            'ResponseDate' => $GetDate,
            'Status' => 0,
        ];

        DB::table('teamjoinrequest')->insertGetId($TeamLeaderData);

        DB::table('players')
            ->where('Id', $TeamLeaderId)
            ->update(['TeamId' => $TeamId]);

        if ($request->PlayersMap != null && $request->PlayersMap != "") {
            $GetPlayers = explode(',', $request->PlayersMap);

            for ($y = 0; $y < count($GetPlayers); $y++) {
                if ((Integer)$GetPlayers[$y] != $TeamLeaderId) {
                    $PlayerData = [
                        'TeamId' => $TeamId,
                        'UserId' => (Integer)$GetPlayers[$y],
                        'AccountTypeId' => 1,
                        'Status' => -1,
                    ];


                    if ((Integer)$GetPlayers[$y] != 0) {
                        DB::table('teamjoinrequest')->insertGetId($PlayerData);
                    }
                }
            }
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function UpdateTeamLogo(Request $request)
    {
        $TeamId = $request->TeamId;
        $GetImage = $request->Image;

        $NewImageName = HelperAPIsFun::UploadImage($GetImage, 'png');

        if ($NewImageName != '') {
            $Data = [
                'Logo' => $NewImageName,
            ];

            DB::table('teams')
                ->where('Id', $TeamId)
                ->update($Data);
        }

        return $this->returnDate('ExecuteStatus', true);
    }


    public function RemovePlayerFromTeam(Request $request)
    {
        $TeamId = $request->TeamId;
        $PlayerId = $request->PlayerId;

        $GetDate = Carbon::now();
		
        DB::table('players')
            ->where('Id', $PlayerId)
            ->update(['TeamId' => null, 'PositionId' => null]);

        DB::table('teamjoinrequest')
            ->where('UserId', $PlayerId)
            ->where('TeamId', $TeamId)
            ->update(['Status' => 0]);  

		// History
        $CheckHasTeam = DB::table('playerteamhistory')
            ->where('TeamId', '=', $TeamId)
            ->where('PlayerId', '=', $PlayerId)
            ->where('EndDate', '=', null)
            ->first();

        if ($CheckHasTeam) {
            DB::table('playerteamhistory')
                ->where('Id', $CheckHasTeam->Id)
                ->update(['EndDate' => $GetDate]);
        }
        // End History


        return $this->returnDate('ExecuteStatus', true);
    }

    public function AddChatGroup(Request $request)
    {
        $Id = $request->Id;
        $ChatUrl = $request->ChatUrl;
        $IsTeam = $request->IsTeam;

        if ($IsTeam) {
            DB::table('teams')
                ->where('Id', $Id)
                ->update(['ChatUrl' => $ChatUrl]);
        } else {
            DB::table('exercises')
                ->where('Id', $Id)
                ->update(['ChatUrl' => $ChatUrl]);

        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function AddPlayerToTeam(Request $request)
    {
        $TeamId = $request->TeamId;
        $PlayerId = $request->PlayerId;

        return $this->returnDate('ExecuteStatus', false);
        
//        $PlayerData = [
//            'TeamId' => $TeamId,
//            'UserId' => $PlayerId,
//            'AccountTypeId' => 1,
//            'Status' => 1,
//        ];
//
//        $PlayerInfo = DB::table('players')
//            ->where('Id', '=', $PlayerId)
//            ->first();
//
//        $TeamInfo = DB::table('teams')
//            ->where('Id', '=', $TeamId)
//            ->first();
//
//        $GetTokenId = $PlayerInfo->TokenId;
//
//        if ($PlayerInfo->Lang == 'en') {
//            $message = 'You have invite to join ' . $TeamInfo->NameEn;
//        } else {
//            $message = 'لديك دعوة للانظمام لـ ' . $TeamInfo->NameAr;
//        }
//
//        (new GeneralController)->SendSingleNotifications($message, $GetTokenId);
//
//        DB::table('teamjoinrequest')->insertGetId($PlayerData);
//
//        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetTeamStatistics(Request $request)
    {
        $TeamId = $request->TeamId;
        $GetUserAccountTypeId = 13; // Team Account Type Id

        $Followers = DB::table('follow')
            ->where('FollowId', '=', $TeamId)
            ->where('FollowUserAccountTypeId', '=', $GetUserAccountTypeId)
            ->where('IsFollow', '=', 1)
            ->count();

        $Following = DB::table('follow')
            ->where('FromUserId', '=', $TeamId)
            ->where('FromUserAccountTypeId', '=', $GetUserAccountTypeId)
            ->where('IsFollow', '=', 1)
            ->count();

//        $Goal = DB::table('matchdetails')
//            ->where('TeamId', '=', $TeamId)
//            ->where('Type', '=', 'Goal')
//            ->count();
//
//        $RedCard = DB::table('matchdetails')
//            ->where('TeamId', '=', $TeamId)
//            ->where('Type', '=', 'RedCard')
//            ->count();
//
//        $YellowCard = DB::table('matchdetails')
//            ->where('TeamId', '=', $TeamId)
//            ->where('Type', '=', 'YellowCard')
//            ->count();

		$Goal = 0;
		$RedCard = 0;
		$YellowCard = 0;
						
//        $GetGoalCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "Goal" AND det.TeamId = :TeamId AND det.MatchId = mat.Id WHERE mat.LeagueId = 4 AND det.Id IS NOT NULL' , ['TeamId' => $TeamId]);
//        $GetRedCardCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "RedCard" AND det.TeamId = :TeamId AND det.MatchId = mat.Id WHERE mat.LeagueId = 4 AND det.Id IS NOT NULL' , ['TeamId' => $TeamId]);
//        $GetYellowCardCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "YellowCard" AND det.TeamId = :TeamId AND det.MatchId = mat.Id WHERE mat.LeagueId = 4 AND det.Id IS NOT NULL' , ['TeamId' => $TeamId]);

        $GetGoalCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "Goal" AND det.TeamId = :TeamId AND det.MatchId = mat.Id WHERE det.Id IS NOT NULL' , ['TeamId' => $TeamId]);
        $GetRedCardCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "RedCard" AND det.TeamId = :TeamId AND det.MatchId = mat.Id WHERE det.Id IS NOT NULL' , ['TeamId' => $TeamId]);
        $GetYellowCardCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "YellowCard" AND det.TeamId = :TeamId AND det.MatchId = mat.Id WHERE det.Id IS NOT NULL' , ['TeamId' => $TeamId]);

        if ($GetGoalCo) {
            $Goal = count($GetGoalCo);
        }
        if ($GetRedCardCo) {
            $RedCard = count($GetRedCardCo);
        }
        if ($GetYellowCardCo) {
            $YellowCard = count($GetYellowCardCo);
        }

        $LeaguesCount = DB::table('managejoin')
            ->where('UserId', '=', $TeamId)
            ->where('AccountTypeId', '=', 13) // Team Account Type Id
            ->where('IsAccepted', '=', 1)
            ->where('IsDeleted', '=', 0)
            ->count();

        // Start get cups count
        $CupCount = $this->CupCountByTeamId($TeamId);

       

        // End get cups count


        $Statistics = ['Followers' => $Followers, 'Following' =>  $Following, 'CupCount' =>  (string) $CupCount, 'GoalCount' =>  (string)$Goal, 'YellowCard' =>  (string)$YellowCard, 'RedCard' =>  (string)$RedCard, 'LeaguesCount' =>  (string)$LeaguesCount];

        return $this->returnDate('Data', $Statistics);
    }
	
	 static function CupCountByTeamId($TeamId) {
        $CupCount = 0;

        $GetAllLegsEndedAndTeamOnItList = DB::select('SELECT lEG.LeagId LeagId FROM (SELECT LEG_TEAM.LeagueId LeagId FROM managejoin LEG_TEAM WHERE LEG_TEAM.AccountTypeId = 13 AND LEG_TEAM.UserId = :PassTeamId) lEG RIGHT JOIN leagues leg ON leg.Id = lEG.LeagId AND leg.Status = 2 WHERE lEG.LeagId IS NOT NULL;', ['PassTeamId' => $TeamId]);

        for($y = 0; $y<count($GetAllLegsEndedAndTeamOnItList); $y++)
        {
            $GetLegId = $GetAllLegsEndedAndTeamOnItList[$y]->LeagId;

            $LegInfo = DB::table('leagues')
                ->where('Id', '=', $GetLegId)
                ->first();

            if ($LegInfo->LeagueType = 'Championship') {

                $GetTeamsOnLastMatch = DB::select('SELECT mat.Id MatcheId, mat.FirstTeamId FirstTeamId, mat.SecondTeamId SecondTeamId FROM matches mat WHERE mat.LeagueId = :PassLeagueId ORDER BY mat.MatchDate DESC LIMIT 1;', ['PassLeagueId' => $GetLegId]);

                $GetMatchId = $GetTeamsOnLastMatch[0]->MatcheId;
                $GetFirstTeamId = $GetTeamsOnLastMatch[0]->FirstTeamId;
                $GetSecondTeamId = $GetTeamsOnLastMatch[0]->SecondTeamId;

                $FirstTeamGoalCount = DB::table('matchdetails')
                    ->where('MatchId', '=', $GetMatchId)
                    ->where('TeamId', '=', $GetFirstTeamId)
                    ->where('Type', '=', 'Goal')
                    ->count();

                $SecondGoalCount = DB::table('matchdetails')
                    ->where('MatchId', '=', $GetMatchId)
                    ->where('TeamId', '=', $GetSecondTeamId)
                    ->where('Type', '=', 'Goal')
                    ->count();

                if ($GetFirstTeamId == $TeamId) {
                    if ($FirstTeamGoalCount > $SecondGoalCount) {
                        $CupCount += 1;
                    }
                } else if ($GetSecondTeamId == $TeamId) {
                    if ($SecondGoalCount > $FirstTeamGoalCount) {
                        $CupCount += 1;
                    }
                }

            } else if ($LegInfo->LeagueType = 'League') {

                $FinalResultSorted = LeaguesController::CalculateStandings($GetLegId, 0);

                if ($FinalResultSorted != null && count($FinalResultSorted) > 0) {
                    if ($FinalResultSorted[0]->TeamId == $TeamId) {
                        $CupCount +=1;
                    }
                }
            }
        }

        return $CupCount;
    }
}
