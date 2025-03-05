<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\APIs\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlayerController extends Controller
{
    use GeneralTrait;

    public function GetUserInfo(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $GetUserLoginId = $request->UserLoginId;
        $GetUserLoginAccountTypeId = $request->UserLoginAccountTypeId;

        $GetTableName = HelperAPIsFun::GetTableName($GetUserAccountTypeId);

        $User = DB::table($GetTableName->TableName)->where('Id', '=', $GetUserId)->first();

        if($GetUserAccountTypeId == AccountTypes::Player)
        {
            $User->Position = DB::table('playerposition')
                ->where('Id', '=', $User->PositionId)
                ->first();
        } else
        {
            $User->Position = null;
        }

        if($GetUserLoginId != 0)
        {
            $Follow = HelperAPIsFun::CheckFollowing($GetUserLoginId, $GetUserLoginAccountTypeId, $User->Id, $User->AccountTypeId);

            if($Follow != null && $Follow->IsFollow == '1')
            {
                $User->IsFollowing = true;

            } else
            {
                $User->IsFollowing = false;
            }
        } else
        {
            $User->IsFollowing = false;
        }

        return $this->returnDate('Data', $User);
    }


    public function GetRefereeMatchCount($RefereeId) {
        return DB::table('matchstaff')
            ->where('UserId', '=', $RefereeId)
            ->where('UserAccountTypeId', '=', 3) // Referee
            ->distinct() // Not duplicates
            ->count();
    }

    public function GetCommentatorMatchCount($UserId) {
        return DB::table('matchstaff')
            ->where('UserId', '=', $UserId)
            ->where('UserAccountTypeId', '=', 4) // Commentator
            ->distinct() // Not duplicates
            ->count();
    }

    public function GetPlayerCardsOrGoalList(Request $request)
    {
        $GetPlayerId = $request->PlayerId;
        $GetType = $request->Type;

        $MyList = DB::select('SELECT mat.Id MatchId, mat.MatchDate MatchDate, team1.Id FirstTeamId, team1.NameAr FirstTeamNameAr, team1.NameEn FirstTeamNameEn, team1.Logo FirstTeamLogo, team2.Id SecondTeamId, team2.NameAr SecondTeamNameAr, team2.NameEn SecondTeamNameEn, team2.Logo SecondTeamLogo, CONCAT(matDet.Minutes , ":", matDet.Seconds) as TheTime FROM matchdetails matDet LEFT JOIN matches mat ON mat.Id = matDet.MatchId LEFT JOIN teams team1 ON team1.Id = mat.FirstTeamId LEFT JOIN teams team2 ON team2.Id = mat.SecondTeamId WHERE matDet.PlayerId = :PlayerId AND matDet.Type = :ForType;' , ['PlayerId' => $GetPlayerId, 'ForType' => $GetType]);

        return $this->returnDate('Data', $MyList);
    }

    public function GetRefereeActionCount($RefereeId, $Type) {

        if ($Type == "Goal") { // Penalty
           // $MyList = DB::select('SELECT det.Id FROM matchdetails det INNER JOIN matchstaff sta ON det.MatchId = sta.MatchId WHERE det.Type = :PassType AND det.IsPenalty = 1 AND sta.UserAccountTypeId = 3 AND sta.UserId = :RefereeId' , ['PassType' => $Type, 'RefereeId' => $RefereeId]);
            $MyList = DB::select('SELECT det.Id FROM matchdetails det WHERE det.Type = :PassType AND det.IsPenalty = 1 AND det.AddByAccountTypeId = 3 AND det.AddById = :RefereeId' , ['PassType' => $Type, 'RefereeId' => $RefereeId]);
        } else {
           // $MyList = DB::select('SELECT det.Id FROM matchdetails det INNER JOIN matchstaff sta ON det.MatchId = sta.MatchId WHERE det.Type = :PassType AND sta.UserAccountTypeId = 3 AND sta.UserId = :RefereeId' , ['PassType' => $Type, 'RefereeId' => $RefereeId]);
            $MyList = DB::select('SELECT det.Id FROM matchdetails det WHERE det.Type = :PassType AND det.AddByAccountTypeId = 3 AND det.AddById = :RefereeId' , ['PassType' => $Type, 'RefereeId' => $RefereeId]);
        }

        return count($MyList);
    }

    public function GetStatistics(Request $request)
    {
       $UserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;

        $GetTeamId = 0;
        $GetIsLeaderOfTeam = false;
        $TeamFeePaymentStatus = null;
        $TeamIsApproved = null;
        $GetPosition = null;
        $Goal = 0;
        $RedCard = 0;
        $YellowCard = 0;
        $RefereeMatchCount = 0;
        $CommentatorMatchCount = 0;

        $Followers = DB::table('follow')
            ->where('FollowId', '=', $UserId)
            ->where('FollowUserAccountTypeId', '=', $GetUserAccountTypeId)
            ->where('IsFollow', '=', 1)
            ->count();

        $Following = DB::table('follow')
            ->where('FromUserId', '=', $UserId)
            ->where('FromUserAccountTypeId', '=', $GetUserAccountTypeId)
            ->where('IsFollow', '=', 1)
            ->count();

        if ($GetUserAccountTypeId == 3) { // Referee
            $RefereeMatchCount = $this->GetRefereeMatchCount($UserId);
            $Goal = $this->GetRefereeActionCount($UserId, 'Goal'); // Penalty
            $RedCard = $this->GetRefereeActionCount($UserId, 'RedCard');
            $YellowCard = $this->GetRefereeActionCount($UserId, 'YellowCard');

        } else if ($GetUserAccountTypeId == 4) { // Commentator
            $CommentatorMatchCount = $this->GetCommentatorMatchCount($UserId);

        } else {
           // $Goal = DB::table('matchdetails')
           //     ->where('PlayerId', '=', $UserId)
           //     ->where('Type', '=', 'Goal')
           //     ->count();

           // $RedCard = DB::table('matchdetails')
           //    ->where('PlayerId', '=', $UserId)
           //    ->where('Type', '=', 'RedCard')
           //    ->count();

           // $YellowCard = DB::table('matchdetails')
           //    ->where('PlayerId', '=', $UserId)
           //    ->where('Type', '=', 'YellowCard')
           //    ->count();
				
            $GetGoalCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "Goal" AND det.PlayerId = :PlayerId AND det.MatchId = mat.Id WHERE mat.LeagueId = 4 AND det.Id IS NOT NULL' , ['PlayerId' => $UserId]);
            $GetRedCardCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "RedCard" AND det.PlayerId = :PlayerId AND det.MatchId = mat.Id WHERE mat.LeagueId = 4 AND det.Id IS NOT NULL' , ['PlayerId' => $UserId]);
            $GetYellowCardCo = DB::select('SELECT det.Id FROM  matches mat LEFT JOIN matchdetails det ON det.Type = "YellowCard" AND det.PlayerId = :PlayerId AND det.MatchId = mat.Id WHERE mat.LeagueId = 4 AND det.Id IS NOT NULL' , ['PlayerId' => $UserId]);

            if ($GetGoalCo) {
                $Goal = count($GetGoalCo);
            }
            if ($GetRedCardCo) {
                $RedCard = count($GetRedCardCo);
            }
            if ($GetYellowCardCo) {
                $YellowCard = count($GetYellowCardCo);
            }
        }



        if($GetUserAccountTypeId == 1) {
            $GetPlayerInfo = DB::table('players')
                ->where('Id', '=', $UserId)
                ->first();

            $GetPositionId = $GetPlayerInfo->PositionId;

            if ($GetPositionId != null && $GetPositionId != 0) {
                $GetPosition = DB::table('playerposition')
                    ->where('Id', '=', $GetPositionId)
                    ->first();
            }

            $GetTeamId = $GetPlayerInfo->TeamId;

            if($GetTeamId != null) {
				$GetTeamInfo = DB::table('teams')
                ->where('Id', '=', $GetTeamId)
                ->first();


				if ($GetTeamInfo->TeamLeaderId == $UserId)
                    $GetIsLeaderOfTeam = true;

				$TeamIsApproved = $GetTeamInfo->IsApproved;

                $GetPaymentInfo = DB::table('payments')
                    ->where('PayForId', '=', $GetTeamId)
                    ->where('FeeId', '=', 1) // The Id of Team creation fee in table 'Fee'
                    ->where('PaymentStatus', '=', 1)
                    ->first();

                if($GetPaymentInfo) {
                    $TeamFeePaymentStatus = $GetPaymentInfo->PaymentStatus;
                }
            }
        }

        $TableName = HelperAPIsFun::GetTableName($GetUserAccountTypeId);

        $GetAccountInfo = DB::table($TableName->TableName)
            ->where('Id', '=', $UserId)
            ->first();

        $IsAccountApproved = 0;

        if(isset($GetAccountInfo->IsApproved)) {
            $IsAccountApproved = $GetAccountInfo->IsApproved;
        }

  // Start get cups count
        $CupCount = 0;

        if($GetUserAccountTypeId == 1 && $GetTeamId != null) {

            $CupCount = TeamController::CupCountByTeamId($GetTeamId);

        }

        // End get cups count


        $Statistics = ['IsAccountApproved' => $IsAccountApproved, 'AccountStatus' =>  $GetAccountInfo->Status, 'Followers' => $Followers, 'Following' =>  $Following,
            'TeamId' => $GetTeamId, 'TeamFeePaymentStatus' => $TeamFeePaymentStatus, 'TeamIsApproved' => $TeamIsApproved, 'CupCount' =>  (string)$CupCount, 'GoalCount' =>  (string)$Goal, 'YellowCard' =>  (string)$YellowCard, 'RedCard' =>  (string)$RedCard, 'RefereeMatchCount' =>  (string)$RefereeMatchCount, 'CommentatorMatchCount' => (string)$CommentatorMatchCount, 'Position' => $GetPosition, 'IsLeaderOfTeam' => $GetIsLeaderOfTeam];

        return $this->returnDate('Data', $Statistics);
    }

    public function FollowingUser(Request $request)
    {
        $FromUserId = $request->FromUserId;
        $FromUserAccountTypeId = $request->FromUserAccountTypeId;
        $FollowId = $request->FollowId;
        $FollowUserAccountTypeId = $request->FollowUserAccountTypeId;
        $IsFollow = $request->IsFollow;

        $Follow = HelperAPIsFun::CheckFollowing($FromUserId, $FromUserAccountTypeId, $FollowId, $FollowUserAccountTypeId);

        if($Follow == null)
        {
            $Data = [
                'FromUserId' => $FromUserId,
                'FromUserAccountTypeId' => $FromUserAccountTypeId,
                'FollowId' => $FollowId,
                'FollowUserAccountTypeId' => $FollowUserAccountTypeId,
                'IsFollow' => "1",
            ];

            DB::table('follow')->insert($Data);

        } else
        {
            DB::table('follow')
                ->where('Id', $Follow->Id)
                ->update(['IsFollow' => $IsFollow]);
        }

        $Follow = HelperAPIsFun::CheckFollowing($FromUserId, $FromUserAccountTypeId, $FollowId, $FollowUserAccountTypeId);

        if($Follow != null && $Follow->IsFollow == '1')
        {
            $ReturnDate = true;

        } else
        {
            $ReturnDate = false;
        }

        return $this->returnDate('Data', $ReturnDate);
    }

/*    function CheckFollowing($FromUserId, $FromUserAccountTypeId, $FollowId, $FollowUserAccountTypeId)
    {
        return DB::table('follow')
            ->where('FromUserId', '=', $FromUserId)
            ->where('FromUserAccountTypeId', '=', $FromUserAccountTypeId)
            ->where('FollowId', '=', $FollowId)
            ->where('FollowUserAccountTypeId', '=', $FollowUserAccountTypeId)
            ->first();
    }*/

    function PlayerSearch(Request $request)
    {
        $Search = $request->Search;
        $UserTypeId = $request->UserTypeId;
        $Result = null;

        $Phone = $Search;

        if(str_starts_with($Search, '05'))
        {
            $Phone = Str::replaceFirst("05", "009665", $Search);

        } else if(str_starts_with($Search, '5'))
        {
            $Phone = Str::replaceFirst("5", "009665", $Search);

        } else if(str_starts_with($Search, '+96605'))
        {
            $Phone = Str::replaceFirst("+96605", "009665", $Search);

        } else if(str_starts_with($Search, '+9665'))
        {
            $Phone = Str::replaceFirst("+9665", "009665", $Search);
        }

        if($UserTypeId != null)
        {
            $UserTypeInfo = DB::table('useraccountstype')
                ->where('Id', '=', $UserTypeId)
                ->first();

            if($UserTypeInfo != null)
            {
                $Result = DB::table($UserTypeInfo->TableName)
                    ->where('Id', '=', $Search)
                    ->orWhere('Phone', '=', $Phone)
                    ->orWhere('Email', '=', $Search)
                    ->first();
            }

        } else
        {
            $AllUserTypeInfo = DB::table('useraccountstype')->get();

            for($y = 0; $y<count($AllUserTypeInfo); $y++)
            {
                $TableName = $AllUserTypeInfo[$y]->TableName;

                if($Result == null)
                {
                    $Result = DB::table($TableName)
                        ->where('Id', '=', $Search)
                        ->orWhere('Phone', '=', $Phone)
                        ->orWhere('Email', '=', $Search)
                        ->first();
                }
            }
        }

        return $this->returnDate('Data', $Result);
    }

    public function GetUserInfoToEdit(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;

        $GetTableName = HelperAPIsFun::GetTableName($GetUserAccountTypeId);

        $User = DB::table($GetTableName->TableName)->where('Id', '=', $GetUserId)->first();

        if($GetUserAccountTypeId == AccountTypes::Player)
        {
            $User->Position = DB::table('playerposition')
                ->where('Id', '=', $User->PositionId)
                ->first();
        } else
        {
            $User->Position = null;
        }

        return $this->returnDate('Data', $User);
    }

    public function UpdateUserInfo(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $FirstName = $request->FirstName;
        $LastName = $request->LastName;
        $Phone = $request->Phone;
        $Email = $request->Email;
        $Fee = $request->Fee;
        $Nationality = $request->Nationality;
        $IdNumber = $request->IdNumber;

        $Data = [
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'Email' => $Email,
        ];

        if ($GetUserAccountTypeId == 3 || $GetUserAccountTypeId == 4 || $GetUserAccountTypeId == 5 || $GetUserAccountTypeId == 12) {
            $Data += [
                'Fee' => $Fee,
            ];
        }

        if ($GetUserAccountTypeId != 7) {
            $Data += [
                'Nationality' => $Nationality,
                'IdNumber' => $IdNumber,
            ];
        }

        $GetTableName = HelperAPIsFun::GetTableName($GetUserAccountTypeId);

        DB::table($GetTableName->TableName)
            ->where('Id', $GetUserId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetPlayersByStatus(Request $request)
    {
        $GetStatus = $request->Status;
        $AllTeams = [];

        if($GetStatus == "Active")
        {
            $AllTeams = DB::table('players')
                ->where('Status', '=', 1)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == "Inactive")
        {
            $AllTeams = DB::table('players')
                ->where('Status', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();
        }

        return $this->returnDate('Data', $AllTeams, "");
    }

    public function ChangePlayerStatus(Request $request)
    {
        $ByUserId = $request->ByUserId;
        $ByUserAccountTypeId = $request->ByUserAccountTypeId;
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $GetNewStatus = $request->NewStatus;

        $TableName = HelperController::GetTableNameByAccountTypeId($UserAccountTypeId);

        $UserInfo = DB::table($TableName)
            ->where('Id', '=', $UserId)
            ->first();

        $GetTokenId = $UserInfo->TokenId;
        $GetLang = $UserInfo->Lang;
        $message = '';

        $GetDate = now();
        $NowDateTime = $GetDate->toDateTimeString();

        if($GetNewStatus == "Approve")
        {
            $Data = [
                'ApprovedBy' => $ByUserId,
                'ApprovedByAccountTypeId' => $ByUserAccountTypeId,
                'ApprovedDate' => $NowDateTime,
                'IsApproved' => 1,
                'Status' => 1,
            ];

            if($GetLang == 'en')
            {
                $message = 'Congratulations, your account has been approved';

            } else
            {
                $message = 'مبروك, تم اعتماد حسابك';
            }

        } else if($GetNewStatus == "Active")
        {
            $Data = [
                'Status' => 1,
            ];

            if($GetLang == 'en')
            {
                $message = 'Congratulations, your account has been active';

            } else
            {
                $message = 'مبروك, تم تفعيل حسابك';
            }

        } else
        {
            $Data = [
                'Status' => 0,
            ];

            if($GetLang == 'en')
            {
                $message = 'Your account has been inactive';

            } else
            {
                $message = 'تم ايقاف حسابك';
            }
        }

        DB::table($TableName)
            ->where('Id', $UserId)
            ->update($Data);

        (new GeneralController)->SendSingleNotifications($message, $GetTokenId);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function UpdateUserImage(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $GetImage = $request->Image;

        $NewImageName = HelperAPIsFun::UploadImage($GetImage, 'png');

        $TableName = HelperController::GetTableNameByAccountTypeId($UserAccountTypeId);

        if ($UserAccountTypeId == 6 || $UserAccountTypeId == 10)  { // Stadium Or Clinic
            $Data = [
                'Logo' => $NewImageName,
            ];
        } else {
            $Data = [
                'Image' => $NewImageName,
            ];
        }

        DB::table($TableName)
            ->where('Id', $UserId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetPlayerPositions(Request $request)
    {
        $Result = DB::table('playerposition')
            ->orderByRaw('Id DESC')
            ->get();

        return $this->returnDate('Data', $Result, "");
    }

    public function ChangePlayerPosition(Request $request)
    {
        $PlayerId = $request->PlayerId;
        $PositionId = $request->PositionId;

        DB::table('players')
            ->where('Id', $PlayerId)
            ->update([ 'PositionId' => $PositionId ]);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function ChangePlayerNumber(Request $request)
    {
        $PlayerId = $request->PlayerId;
        $PlayerNumber = $request->PlayerNumber;

        DB::table('players')
            ->where('Id', $PlayerId)
            ->update([ 'PlayerNumber' => $PlayerNumber ]);

        return $this->returnDate('ExecuteStatus', true);
    }

}
