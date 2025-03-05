<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizerLeagueController extends Controller
{
    use GeneralTrait;

    public function GetOrganizerLeague(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $GetStatus = $request->Status;

        $FinalResult = [];
        $Result = [];

        if($GetStatus == "New")
        {
            $Result = DB::table('leagues')
                ->where('Status', '=', -1)
                ->where('CreateByUserTypeId', '=', $UserAccountTypeId)
                ->where('CreateById', '=', $UserId)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == "Active")
        {
            $Result = DB::table('leagues')
                ->where('Status', '=', 1)
                ->where('CreateByUserTypeId', '=', $UserAccountTypeId)
                ->where('CreateById', '=', $UserId)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == "Inactive")
        {
            $Result = DB::table('leagues')
                ->where('Status', '=', 0)
                ->where('CreateByUserTypeId', '=', $UserAccountTypeId)
                ->where('CreateById', '=', $UserId)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == "Ended")
        {
            $Result = DB::table('leagues')
                ->where('Status', '=', 2)
                ->where('CreateByUserTypeId', '=', $UserAccountTypeId)
                ->where('CreateById', '=', $UserId)
                ->orderByRaw('Id DESC')
                ->get();
        }

        for($y = 0; $y<count($Result); $y++)
        {
            $TotalTeams = 0;
            $TeamsIds = [];

            $GetId = $Result[$y]->Id;

            $Teams = DB::table('managejoin')
                ->where('LeagueId', '=', $GetId)
                ->where('AccountTypeId', '=', 13) // Team Account Type Id
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

            $FinalResult[] = $Result[$y];
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetOrganizerLeagueMangeJoin(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $LeagueId = $request->LeagueId;
        $ForAccountTypeId = $request->ForAccountTypeId;
        $Status = $request->Status;

        $FinalResult = array();

        $MyList = DB::table('managejoin')
            ->where('LeagueId', '=', $LeagueId)
            ->where('AccountTypeId', '=', $ForAccountTypeId)
            ->where('IsDeleted', '=', 0)
            ->get();


        for($n = 0; $n<count($MyList); $n++) {

            $Logo = "";
            $NameAr = "";
            $NameEn = "";

            $TablesFrom = HelperAPIsFun::GetTableName($ForAccountTypeId);

            $UserInfo = DB::table($TablesFrom->TableName)
                ->where('Id', '=', $MyList[$n]->UserId)
                ->first();

            if ($ForAccountTypeId == 13) { // Team Account Type Id
                $Logo = $UserInfo->Logo;
                $NameAr = $UserInfo->NameAr;
                $NameEn = $UserInfo->NameEn;
            } else if ($ForAccountTypeId == 6) { // Stadiums Account Type Id
                $Logo = $UserInfo->Logo;
                $NameAr = $UserInfo->Name;
                $NameEn = $UserInfo->Name;
            } else if ($ForAccountTypeId == 3) { // Referees Account Type Id
                $Logo = $UserInfo->Image;
                $NameAr = $UserInfo->FirstName . ' ' . $UserInfo->LastName;
                $NameEn = $UserInfo->FirstName . ' ' . $UserInfo->LastName;
            } else if ($ForAccountTypeId == 4) { // Commentators Account Type Id
                $Logo = $UserInfo->Image;
                $NameAr = $UserInfo->FirstName . ' ' . $UserInfo->LastName;
                $NameEn = $UserInfo->FirstName . ' ' . $UserInfo->LastName;
            } else if ($ForAccountTypeId == 5) { // Photographers Account Type Id
                $Logo = $UserInfo->Image;
                $NameAr = $UserInfo->FirstName . ' ' . $UserInfo->LastName;
                $NameEn = $UserInfo->FirstName . ' ' . $UserInfo->LastName;
            } else if ($ForAccountTypeId == 12) { // Organizers Account Type Id
                $Logo = $UserInfo->Image;
                $NameAr = $UserInfo->FirstName . ' ' . $UserInfo->LastName;
                $NameEn = $UserInfo->FirstName . ' ' . $UserInfo->LastName;
            }

            $InfoDate = array(
                'Id' => $MyList[$n]->Id,
                'UserId' => $MyList[$n]->UserId,
                'AccountTypeId' => $MyList[$n]->AccountTypeId,
                'IsAccepted' => $MyList[$n]->IsAccepted,
                'Fee' => $MyList[$n]->Fee,
                'PaymentId' => $MyList[$n]->PaymentId,
                'ResponseDate' => $MyList[$n]->ResponseDate,
                'CreateDate' => $MyList[$n]->CreateDate,
                'Status' => $MyList[$n]->Status,
                'Logo' => $Logo,
                'NameAr' => $NameAr,
                'NameEn' => $NameEn);

            $GetFee = $MyList[$n]->Fee;
            $GetIsAccepted = $MyList[$n]->IsAccepted;
            $GetPaymentId = $MyList[$n]->PaymentId;

            if ($Status == 'Ready') {

                if ($GetFee > 0) {
                    if ($GetIsAccepted == 1 && $GetPaymentId != 0) {
                        $FinalResult[] = $InfoDate;
                    }
                } else {
                    if ($GetIsAccepted == 1) {
                        $FinalResult[] = $InfoDate;
                    }
                }

            } else if ($Status == 'WaitingAccept') {
                if ($GetIsAccepted == 0 || $GetIsAccepted == 2) {
                    $FinalResult[] = $InfoDate;
                }
            } else if ($Status == 'WaitingPayment') {
                if ($GetFee > 0 && $GetIsAccepted == 1 && $GetPaymentId == 0) {
                    $FinalResult[] = $InfoDate;
                }
            }
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function PayForManageJoin(Request $request)
    {
        $ManageJoinId = $request->JoinId;
        $PaymentId = $request->PaymentId;

        DB::table('managejoin')
            ->where('Id', '=', $ManageJoinId)
            ->update(['PaymentId' => $PaymentId]);

        $JoinInfo = DB::table('managejoin')
            ->where('Id', '=', $ManageJoinId)
            ->first();

        if ($JoinInfo->AccountTypeId == 6) { // Stadium Booking
            $StadiumInfo = DB::table('stadiums')
                ->where('Id', '=', $JoinInfo->UserId)
                ->first();

            $TablesFrom = HelperAPIsFun::GetTableName($JoinInfo->CreateByAccountTypeId);

            $ByInfo = DB::table($TablesFrom->TableName)
                ->where('Id', '=', $JoinInfo->CreateByUserId)
                ->first();

            if ($StadiumInfo->TokenId != null && $StadiumInfo->TokenId != "" && $ByInfo->FirstName != null && $ByInfo->LastName != null) {
                $TokenId = [$StadiumInfo->TokenId];

                if ($StadiumInfo->Lang == "ar") {
                    $Message = "تم دفع رسوم حجز " . $ByInfo->FirstName . " " . $ByInfo->LastName;
                } else {
                    $Message = "The booking fee paid for " . $ByInfo->FirstName . " " . $ByInfo->LastName;
                }

                HelperController::SendNotifications($TokenId, $Message);
            }
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function AcceptRejectRemoveMangeJoin(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $ManageJoinId = $request->ManageJoinId;
        $Action = $request->Action;

        // Action = 3 : Remove

        $ManageJoinInfo = DB::table('managejoin')
            ->where('Id', '=', $ManageJoinId)
            ->first();

        $LeagueInfo = DB::table('leagues')
            ->where('Id', '=', $ManageJoinInfo->LeagueId)
            ->first();

        $TablesFrom = HelperAPIsFun::GetTableName($ManageJoinInfo->AccountTypeId);

        $UserInfo = DB::table($TablesFrom->TableName)
            ->where('Id', '=', $ManageJoinInfo->UserId)
            ->first();


            if ($Action == 3) { // Remove

                $GetDate = Carbon::now();

                DB::table('managejoin')
                    ->where('Id', '=', $ManageJoinId)
                    ->update(['Status' => 0, 'IsDeleted' => 1, 'DeletedDate' => $GetDate, 'DeletedBy' => $UserId, 'DeletedByAccountTypeId' => $UserAccountTypeId]);

                if ($ManageJoinInfo->AccountTypeId == 13) { // Team Account Type Id

                    if ($UserInfo != null) {
                        $PlayerInfo = DB::table('players')
                            ->where('Id', '=', $UserInfo->TeamLeaderId)
                            ->first();

                        if ($UserInfo != null && $PlayerInfo != null && $PlayerInfo->Lang != null && $PlayerInfo->TokenId != null && $PlayerInfo->TokenId != "") {
                            $Message = __('messages.RemoveTeamFromLeague', [], $PlayerInfo->Lang) . ' ' . $LeagueInfo->Topic;
                            $FinalResult = [$PlayerInfo->TokenId];
                            HelperController::SendNotifications($FinalResult, $Message);
                        }
                    }

                } else if ($ManageJoinInfo->AccountTypeId == 6) { // Stadium Account Type Id

                    if ($UserInfo != null) {
                        if ($UserInfo != null && $UserInfo->Lang != null && $UserInfo->TokenId != null && $UserInfo->TokenId != "") {
                            $Message = __('messages.StadiumReservationCancelByRequester', [], $UserInfo->Lang);
                            $FinalResult = [$UserInfo->TokenId];
                            HelperController::SendNotifications($FinalResult, $Message);
                        }
                    }
                }
            }
        return $this->returnDate('ExecuteStatus', true);
    }

    public function AcceptRejectInvitations(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $ManageJoinId = $request->ManageJoinId;
        $Action = $request->Action;

        // Action = 1 : Accept
        // Action = 2 : Reject

        $ManageJoinInfo = DB::table('managejoin')
            ->where('Id', '=', $ManageJoinId)
            ->first();

        $TablesFrom = HelperAPIsFun::GetTableName($ManageJoinInfo->CreateByAccountTypeId);

        $CreateByInfo = DB::table($TablesFrom->TableName)
            ->where('Id', '=', $ManageJoinInfo->CreateByUserId)
            ->first();

        $FullName = "";

        if ($ManageJoinInfo->AccountTypeId == 13) { // Team Account Type Id
            $TeamInfo = DB::table('teams')
                ->where('Id', '=', $ManageJoinInfo->UserId)
                ->first();
            if ($CreateByInfo->Lang == 'ar')
                $FullName = $TeamInfo->NameAr;
            else
                $FullName = $TeamInfo->NameEn;
        } else if ($ManageJoinInfo->AccountTypeId == 6) { // Stadiums Account Type Id
            $StadiumInfo = DB::table('stadiums')
                ->where('Id', '=', $ManageJoinInfo->UserId)
                ->first();
            $FullName = $StadiumInfo->Name;
        } else { // Referees, Commentators, Photographers, Organizers Account Type Id
            $OtherTablesFrom = HelperAPIsFun::GetTableName($ManageJoinInfo->AccountTypeId);
            $OtherInfo = DB::table($OtherTablesFrom->TableName)
                ->where('Id', '=', $ManageJoinInfo->UserId)
                ->first();
            $FullName = $OtherInfo->FirstName . ' ' . $OtherInfo->LastName;
        }

        if ($Action == 1) { // Accept

            $GetDate = Carbon::now();

            DB::table('managejoin')
                ->where('Id', '=', $ManageJoinId)
                ->update(['IsAccepted' => 1, 'ResponseDate' => $GetDate]);
        } else if ($Action == 2) { // Reject

            $GetDate = Carbon::now();

            DB::table('managejoin')
                ->where('Id', '=', $ManageJoinId)
                ->update(['IsAccepted' => 2, 'ResponseDate' => $GetDate]);
        }

        if ($CreateByInfo != null && $CreateByInfo->Lang != null && $CreateByInfo->TokenId != null && $CreateByInfo->TokenId != "") {
            $Message = __('messages.InvitationAccepted', [], $CreateByInfo->Lang) . ' ' . $FullName;
            $FinalResult = [$CreateByInfo->TokenId];
            HelperController::SendNotifications($FinalResult, $Message);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetStadiumsListOfLeague(Request $request)
    {
        $LeagueId = $request->LeagueId;

        $FinalResult = [];
        $FinalStadiums = [];

        $Result = DB::table('managejoin')
            ->where('LeagueId', '=', $LeagueId)
            ->where('AccountTypeId', '=', 6)
            ->where('IsAccepted', '=', 1)
            ->where('IsDeleted', '=', 0)
            ->get();

        for ($y = 0; $y < count($Result); $y++) {
            $GetFee = $Result[$y]->Fee;
            $GetPaymentId = $Result[$y]->PaymentId;

            if ($GetFee > 0) {
                if ($GetPaymentId != 0) {
                    $FinalResult[] = $Result[$y];
                }
            } else {
                $FinalResult[] = $Result[$y];
            }
        }

        for ($n = 0; $n < count($FinalResult); $n++) {

            $GetUserId = $FinalResult[$n]->UserId;

            $FinalStadiums[] = DB::table('stadiums')
                ->where('Id', '=', $GetUserId)
                ->first();
        }

        return $this->returnDate('Data', $FinalStadiums);
    }

    public function GetStaffList(Request $request)
    {
        $AccountTypeId = $request->AccountTypeId;
        $Latitude = $request->Latitude;
        $Longitude = $request->Longitude;

        $FinalResult = [];

        $TablesFrom = HelperAPIsFun::GetTableName($AccountTypeId);

        $MyList = DB::table($TablesFrom->TableName)
            ->where('Status', '=', 1)
            ->get();

        for ($n = 0; $n < count($MyList); $n++) {

            $GetStaffLocation = $MyList[$n]->Location;

            if ($Latitude != null && $Latitude != "" && $Longitude != null && $Longitude != "") {
                if ($GetStaffLocation != null && $GetStaffLocation != "") {
                    $Location = explode(",", $GetStaffLocation);
                    $StaffLatitude = $Location[0];
                    $StaffLongitude = $Location[1];

                    $Distance = HelperFun::distance(floatval($Latitude), floatval($Longitude), floatval($StaffLatitude), floatval($StaffLongitude), "K");

                    if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN") {
                        $FinalResult[] = $this->preparingStaff($MyList[$n], $AccountTypeId);
                    }
                }
            } else {
                $FinalResult[] = $this->preparingStaff($MyList[$n], $AccountTypeId);
            }
        }

        return $this->returnDate('Data', $FinalResult);

    }

    public function preparingStaff($StaffInfo, $AccountTypeId) {
        $GetId = $StaffInfo->Id;
        $GetAccountTypeId = $AccountTypeId;
        $GetFirstName = '';
        $GetLastName = '';
        $GetNameAr = '';
        $GetNameEn = '';
        $GetPhone = '';
        $GetImage = '';
        $GetFee = 0;

        if ($AccountTypeId == 13) {
            $GetNameAr = $StaffInfo->NameAr;
            $GetNameEn = $StaffInfo->NameEn;
            $GetImage = $StaffInfo->Logo;

            $TeamLeaderInfo = DB::table('players')
                ->where('Id', '=', $StaffInfo->TeamLeaderId)
                ->first();
            if ($TeamLeaderInfo != null) {
                $GetPhone = $TeamLeaderInfo->Phone;
            }
        }

        if ($AccountTypeId == 6) {
            $GetNameAr = $StaffInfo->Name;
            $GetNameEn = $StaffInfo->Name;
            $GetPhone = $StaffInfo->Phone;
            $GetImage = $StaffInfo->Logo;
            $GetFee = $StaffInfo->Fee;
        }

        if ($AccountTypeId == 3 || $AccountTypeId == 4 || $AccountTypeId == 5 || $AccountTypeId == 12) {
            $GetFirstName = $StaffInfo->FirstName;
            $GetLastName = $StaffInfo->LastName;
            $GetPhone = $StaffInfo->Phone;
            $GetImage = $StaffInfo->Image;
            $GetFee = $StaffInfo->Fee;
        }

        return ['Id' => $GetId, 'AccountTypeId' =>  $GetAccountTypeId, 'FirstName' =>  $GetFirstName, 'LastName' =>  $GetLastName, 'NameAr' =>  $GetNameAr, 'NameEn' =>  $GetNameEn, 'Phone' =>  $GetPhone, 'Image' =>  $GetImage, 'Fee' =>  $GetFee];
    }

    public function BookingStaff(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $LeagueId = $request->LeagueId;
        $StaffId = $request->StaffId;
        $StaffAccountTypeId = $request->StaffAccountTypeId;
        $Fee = $request->Fee;
        $MatchDate = $request->MatchDate;

        $Check = DB::table('managejoin')
            ->where('CreateByUserId', '=', $UserId)
            ->where('CreateByAccountTypeId', '=', $UserAccountTypeId)
            ->where('UserId', '=', $StaffId)
            ->where('AccountTypeId', '=', $StaffAccountTypeId)
            ->where('LeagueId', '=', $LeagueId)
            ->where('MatchDate', '=', $MatchDate)
            ->where('IsAccepted', '=', 0)
            ->where('IsDeleted', '=', 0)
            ->where('Status', '=', 1)
            ->first();

        if ($Check) {
            return $this->returnDate('ExecuteStatus', false);

        } else {
            $Data = [
                'CreateByUserId' => $UserId,
                'CreateByAccountTypeId' => $UserAccountTypeId,
                'UserId' => $StaffId,
                'Fee' => $Fee,
                'AccountTypeId' => $StaffAccountTypeId,
                'LeagueId' => $LeagueId,
                'MatchDate' => $MatchDate,
            ];

            DB::table('managejoin')->insert($Data);

            $TablesFrom = HelperAPIsFun::GetTableName($StaffAccountTypeId);

            $StaffInfo = DB::table($TablesFrom->TableName)
                ->where('Status', '=', 1)
                ->first();

            if ($StaffInfo != null && $StaffInfo->TokenId != null && $StaffInfo->TokenId != "") {
                $AllTokenIds = [$StaffInfo->TokenId];
                $message = __('messages.StadiumNewReservation', [], $StaffInfo->Lang);
                (new GeneralController)->SendManyNotifications($message, $AllTokenIds);
            }

            return $this->returnDate('ExecuteStatus', true);
        }
    }

    public function CheckNewInvitations(Request $request)
    {
        $UserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;

        $Count = 0;
        $GetIsLeaderOfTeam = false;
        $GetTeamId = 0;

        if ($GetUserAccountTypeId == 1) {
            $GetPlayerInfo = DB::table('players')
                ->where('Id', '=', $UserId)
                ->first();

            $GetTeamId = $GetPlayerInfo->TeamId;

            if($GetTeamId != null) {
                $GetTeamInfo = DB::table('teams')
                    ->where('Id', '=', $GetTeamId)
                    ->first();

                if ($GetTeamInfo->TeamLeaderId == $UserId)
                    $GetIsLeaderOfTeam = true;
            }
        }

        if ($GetIsLeaderOfTeam || $GetUserAccountTypeId == 13 || $GetUserAccountTypeId == 6 || $GetUserAccountTypeId == 3 || $GetUserAccountTypeId == 4 || $GetUserAccountTypeId == 5 || $GetUserAccountTypeId == 12) { // Organizers Account Type Id
            if ($GetIsLeaderOfTeam && $GetTeamId != 0) {
                $Count = DB::table('managejoin')
                    ->where('UserId', '=', $GetTeamId)
                    ->where('AccountTypeId', '=', 13) // Team Account Id
                    ->where('IsAccepted', '=', 0)

                    ->where('IsDeleted', '=', 0)
                    ->count();
            } else {
                $Count = DB::table('managejoin')
                    ->where('UserId', '=', $UserId)
                    ->where('AccountTypeId', '=', $GetUserAccountTypeId)
                    ->where('IsAccepted', '=', 0)
                    ->where('IsDeleted', '=', 0)
                    ->count();
            }
        }



        return $this->returnDate('Data', ['Count' => $Count, 'IsLeaderOfTeam' => $GetIsLeaderOfTeam]);
    }

    public function GetInvitations(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $Status = $request->Status;

        $FinalResult = array();

        $MyList = DB::table('managejoin')
            ->where('UserId', '=', $UserId)
            ->where('AccountTypeId', '=', $UserAccountTypeId)
            ->where('IsDeleted', '=', 0)
            ->get();

        for($n = 0; $n<count($MyList); $n++) {

            $Id = $MyList[$n]->Id;
            $ByUserId = "";
            $ByUserAccountTypeId = "";
            $UserId = $MyList[$n]->UserId;
            $UserAccountTypeId = $MyList[$n]->AccountTypeId;
            $ByUserFirstName = "";
            $ByUserLastName = "";
            $ByUserImage = "";
            $LeagueId = "";
            $LeagueTopic = "";
            $Fee = $MyList[$n]->Fee;
            $MatchDate = $MyList[$n]->MatchDate;

            $TablesFrom = HelperAPIsFun::GetTableName($MyList[$n]->CreateByAccountTypeId);

            $UserInfo = DB::table($TablesFrom->TableName)
                ->where('Id', '=', $MyList[$n]->CreateByUserId)
                ->first();

            if ($UserInfo != null) {
                $ByUserId = $UserInfo->Id;
                $ByUserAccountTypeId = $UserInfo->AccountTypeId;
                $ByUserFirstName = $UserInfo->FirstName;
                $ByUserLastName = $UserInfo->LastName;
                $ByUserImage = $UserInfo->Image;
            }

            if ($MyList[$n]->LeagueId != null && $MyList[$n]->LeagueId != 0) {
                $LeagueInfo = DB::table('leagues')
                    ->where('Id', '=', $MyList[$n]->LeagueId)
                    ->first();
                if ($LeagueInfo != null) {
                    $LeagueId = $LeagueInfo->Id;
                    $LeagueTopic = $LeagueInfo->Topic;
                }
            }

            $InfoDate = array(
                'Id' => $Id,
                'ByUserId' => $ByUserId,
                'ByUserAccountTypeId' => $ByUserAccountTypeId,
                'ByUserFirstName' => $ByUserFirstName,
                'ByUserLastName' => $ByUserLastName,
                'ByUserImage' => $ByUserImage,
                'UserId' => $UserId,
                'UserAccountTypeId' => $UserAccountTypeId,
                'LeagueId' => $LeagueId,
                'LeagueTopic' => $LeagueTopic,
                'Fee' => $Fee,
                'MatchDate' => $MatchDate
             );

            $GetFee = $MyList[$n]->Fee;
            $GetIsAccepted = $MyList[$n]->IsAccepted;
            $GetPaymentId = $MyList[$n]->PaymentId;

            if ($Status == 'New') {
                if ($GetIsAccepted == 0) {
                    $FinalResult[] = $InfoDate;
                }
            } else if ($Status == 'WaitingPayment') {
                if ($GetFee > 0 && $GetIsAccepted == 1 && $GetPaymentId == 0) {
                    $FinalResult[] = $InfoDate;
                }
            } else if ($Status == 'History') {

                if ($GetFee > 0) {
                    if ($GetIsAccepted == 1 && $GetPaymentId != 0) {
                        $FinalResult[] = $InfoDate;
                    }
                } else {
                    if ($GetIsAccepted == 1) {
                        $FinalResult[] = $InfoDate;
                    }
                }
            }
        }

        return $this->returnDate('Data', $FinalResult);
    }

}
