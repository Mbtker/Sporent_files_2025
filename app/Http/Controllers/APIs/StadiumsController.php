<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StadiumsController extends Controller
{
    use GeneralTrait;

    public function GetStadiums(Request $request)
    {
        $FinalResult = [];
        $Result = DB::table('stadiums')
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        for ($y = 0; $y < count($Result); $y++) {
            $GetStadiumLocation = $Result[$y]->Location;

            if ($GetStadiumLocation != null && $GetStadiumLocation != "") {
                $Location = explode(",", $GetStadiumLocation);
                $StadiumLatitude = $Location[0];
                $StadiumLongitude = $Location[1];

                $Distance = HelperFun::distance(floatval($request->Latitude), floatval($request->Longitude), floatval($StadiumLatitude), floatval($StadiumLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN") {
                    $Result[$y]->Distance = (string) number_format((float)$Distance
                        , 2, '.', '');

                    $FinalResult[] = $Result[$y];
                }
            }

        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetStadiumDetails(Request $request)
    {
        $Result = DB::table('stadiums')
            ->where('Id', '=', $request->Id)
            ->first();

        if ($Result) {
            $Result->Distance = "0.0";
        }

        return $this->returnDate('Data', $Result);
    }



    public function UpdateStadiumInfo(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $FirstName = $request->FirstName;
        $LastName = $request->LastName;
        $Name = $request->Name;
        $CR = $request->CR;
        $Email = $request->Email;
        $Location = $request->Location;
        $Fee = $request->Fee;

        $Data = [
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'Name' => $Name,
            'CR' => $CR,
            'Email' => $Email,
            'Location' => $Location,
            'Fee' => $Fee,
        ];

        $GetTableName = HelperAPIsFun::GetTableName($GetUserAccountTypeId);

        DB::table($GetTableName->TableName)
            ->where('Id', $GetUserId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function BookingStadium(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;
        $LeagueId = $request->LeagueId;
        $StadiumId = $request->StadiumId;
        $Fee = $request->Fee;
        $MatchDate = $request->MatchDate;
        $FirstTeamId = $request->FirstTeamId;
        $SecondTeamId = $request->SecondTeamId;

        $Check = DB::table('managejoin')
            ->where('CreateByUserId', '=', $UserId)
            ->where('CreateByAccountTypeId', '=', $UserAccountTypeId)
            ->where('UserId', '=', $StadiumId)
            ->where('AccountTypeId', '=', 6) // Stadium Account Type Id
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
                'UserId' => $StadiumId,
                'Fee' => $Fee,
                'AccountTypeId' => 6, // Stadium Account Type Id
                'LeagueId' => $LeagueId,
                'MatchDate' => $MatchDate,
            ];

            DB::table('managejoin')->insert($Data);

            $GetStadiumInfo = DB::table('stadiums')
                ->where('Id', '=', $StadiumId)
                ->first();

            if ($GetStadiumInfo != null && $GetStadiumInfo->TokenId != null && $GetStadiumInfo->TokenId != "")
            {
                $AllTokenIds = [$GetStadiumInfo->TokenId];
                $message = __('messages.StadiumNewReservation', [], $GetStadiumInfo->Lang);
                (new GeneralController)->SendManyNotifications($message, $AllTokenIds);
            }

            return $this->returnDate('ExecuteStatus', true);
        }
    }

    public function CancelBookingStadium(Request $request)
    {
        $ManageJoinId = $request->ManageJoinId;
        $StadiumId = $request->StadiumId;

        DB::table('managejoin')
            ->where('Id', $ManageJoinId)
            ->update(['Status' => 2]);

        $GetStadiumInfo = DB::table('stadiums')
            ->where('Id', '=', $StadiumId)
            ->first();

        $GetTokenId = $GetStadiumInfo->TokenId;
        $GetLang = $GetStadiumInfo->Lang;

        if ($GetTokenId != "" && $GetLang != "") {
            $Message = __('messages.StadiumReservationCancelByRequester', [], $GetLang);
            $FinalResult = [$GetTokenId];
            HelperController::SendNotifications($FinalResult, $Message);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function StadiumMyBooking(Request $request)
    {
//        $Result = DB::table('stadiumsbooking')
//            ->where('ByUserId', '=', $request->UserId)
//            ->where('ByUserAccountTypeId', '=', $request->UserAccountTypeId)
//            ->orderByRaw('Id DESC')
//            ->get();
//
//
//        for ($y = 0; $y < count($Result); $y++) {
//
//            $GetStadiumId = $Result[$y]->StadiumId;
//            $GetFirstTeamId = $Result[$y]->FirstTeamId;
//            $SecondTeamId = $Result[$y]->SecondTeamId;
//
//            $Result[$y]->Stadium = DB::table('stadiums')
//                ->where('Id', '=', $GetStadiumId)
//                ->first();
//
////            $Result[$y]->FirstTeam = DB::table('teams')
////                ->where('Id', '=', $GetFirstTeamId)
////                ->first();
////
////            $Result[$y]->SecondTeam = DB::table('teams')
////                ->where('Id', '=', $SecondTeamId)
////                ->first();
//        }

        $FinalResult = [];

        $Result = DB::table('managejoin')
            ->where('CreateByUserId', '=', $request->UserId)
            ->where('CreateByAccountTypeId', '=', $request->UserAccountTypeId)
            ->where('IsDeleted', '=', 0)
            ->orderByRaw('Id DESC')
            ->get();

        for ($y = 0; $y < count($Result); $y++) {

            $GetUserId = $Result[$y]->UserId;
            $GetAccountTypeId = $Result[$y]->AccountTypeId;

            if ($GetAccountTypeId == 6) { // Stadium Account Type Id

                $GetStadiumInfo = DB::table('stadiums')
                    ->where('Id', '=', $GetUserId)
                    ->first();

                $InfoDate = array(
                    'Id' => $Result[$y]->Id,
                    'UserId' => $Result[$y]->UserId,
                    'AccountTypeId' => $Result[$y]->AccountTypeId,
                    'IsAccepted' => $Result[$y]->IsAccepted,
                    'PaymentId' => $Result[$y]->PaymentId,
                    'ResponseDate' => $Result[$y]->ResponseDate,
                    'CreateDate' => $Result[$y]->CreateDate,
                    'MatchDate' => $Result[$y]->MatchDate,
                    'Fee' => $Result[$y]->Fee,
                    'Status' => $Result[$y]->Status,
                    'Logo' => $GetStadiumInfo->Logo,
                    'NameAr' => $GetStadiumInfo->Name,
                    'NameEn' => $GetStadiumInfo->Name);

                $FinalResult[] = $InfoDate;
            }
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetAllStadiumServices(Request $request)
    {
        $Result = DB::table('stadiumsservices')
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        return $this->returnDate('Data', $Result);
    }

    public function GetStadiumServices(Request $request)
    {
        $Result = DB::table('stadiumsserviceslist')
            ->where('StadiumId', '=', $request->StadiumId)
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        for ($y = 0; $y < count($Result); $y++) {

            $GetServicesId = $Result[$y]->ServicesId;

            $Result[$y]->Service = DB::table('stadiumsservices')
                ->where('Id', '=', $GetServicesId)
                ->first();
        }

        return $this->returnDate('Data', $Result);
    }

    public function AddNewServiceToStadium(Request $request)
    {
        $StadiumId = $request->StadiumId;
        $ServiceId = $request->ServiceId;
        $Value = $request->Value;

        $Check = DB::table('stadiumsserviceslist')
            ->where('StadiumId', '=', $StadiumId)
            ->where('ServicesId', '=', $ServiceId)
            ->where('Status', '=', 1)
            ->first();

        if (!$Check) {
            $Data = [
                'StadiumId' => $StadiumId,
                'ServicesId' => $ServiceId,
                'Value' => $Value,
            ];

            DB::table('stadiumsserviceslist')->insertGetId($Data);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function RemoveServiceFromStadium(Request $request)
    {
        $Id = $request->Id;

        DB::table('stadiumsserviceslist')
            ->where('Id', '=', $Id)
            ->delete();

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetMangeStadiums(Request $request)
    {
        $GetStatus = $request->Status;

        $AllUsers = [];

        if($GetStatus == "WaitingApproval")
        {
            $AllUsers = DB::table('stadiums')
                ->where('IsApproved', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == "Active")
        {
            $AllUsers = DB::table('stadiums')
                ->where('IsApproved', '=', 1)
                ->where('Status', '=', 1)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == "Inactive")
        {
            $AllUsers = DB::table('stadiums')
                ->where('IsApproved', '=', 1)
                ->where('Status', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();
        }

        for ($y = 0; $y < count($AllUsers); $y++) {

            $GetStadiumId = $AllUsers[$y]->Id;

            $AllUsers[$y]->ServiceCount = DB::table('stadiumsserviceslist')
                ->where('StadiumId', '=', $GetStadiumId)
                ->where('Status', '=', 1)
                ->count();
        }

        return $this->returnDate('Data', $AllUsers, "");
    }

    public function StadiumBookingList(Request $request)
    {
        $StadiumId = $request->StadiumId;
        $Status = $request->Status;

        $Result = [];
        $FinalResult = [];

        $Result = DB::table('managejoin')
            ->where('UserId', '=', $StadiumId)
            ->where('AccountTypeId', '=', 6) // Stadium Account Type Id
            ->orderBy('Id', 'DESC')
            ->get();

        for ($y = 0; $y < count($Result); $y++) {

            $GetByUserId = $Result[$y]->CreateByUserId;
            $GetByUserAccountTypeId = $Result[$y]->CreateByAccountTypeId;

            $GetTableName = HelperAPIsFun::GetTableName($GetByUserAccountTypeId);

            $Result[$y]->ByUser = DB::table($GetTableName->TableName)
                ->where('Id', '=', $GetByUserId)
                ->first();

            if($GetByUserAccountTypeId == 1 && $Result[$y]->ByUser != null && $Result[$y]->ByUser->PositionId != null && $Result[$y]->ByUser->PositionId != 0)
            {
                $Result[$y]->ByUser->Position = DB::table('playerposition')
                    ->where('Id', '=', $Result[$y]->ByUser->PositionId)
                    ->first();
            }

            $IsAccepted = $Result[$y]->IsAccepted;
            $Fee = $Result[$y]->Fee;
            $PaymentId = $Result[$y]->PaymentId;
            $IsDeleted = $Result[$y]->IsDeleted;
            $BookingStatus = $Result[$y]->Status;

            if ($request->Status == 'New' || $request->Status == 'Accepted') {

                if ($BookingStatus == 1 && $IsDeleted == 0 && ($IsAccepted == 0 || ($Fee > 0 && $IsAccepted == 1 && $PaymentId == 0))) {
                    $FinalResult[] = $Result[$y];
                }

            } else if ($request->Status == 'All') {

                if ($IsAccepted == 1 && $Fee == 0 && $IsDeleted == 0) {
                    $FinalResult[] = $Result[$y];
                } else if ($IsAccepted == 1 && $Fee > 0 && $PaymentId != 0 && $IsDeleted == 0) {
                    $FinalResult[] = $Result[$y];
                } else if ($IsAccepted == 2 && $IsDeleted == 0) {
                    $FinalResult[] = $Result[$y];
                } else if ($BookingStatus == 2) {
                    $FinalResult[] = $Result[$y];
                }
            }

        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function ChangeStadiumStatus(Request $request) {
        $GetStadiumId = $request->StadiumId;
        $GetNewStatus = $request->NewStatus;

        $StadiumInfo = DB::table('stadiums')
            ->where('Id', '=', $GetStadiumId)
            ->first();

        $GetTokenId = $StadiumInfo->TokenId;
        $GetLang = $StadiumInfo->Lang;
        $message = '';

        if($GetNewStatus == "Approve") {
            $Data = [
                'IsApproved' => 1,
                'Status' => 1,
            ];

            if($GetLang == 'en') {
                $message = 'Congratulations, your account has been approved';

            } else {
                $message = 'مبروك, تم اعتماد حسابك';
            }

        } else if($GetNewStatus == "Active")
        {
            $Data = [
                'Status' => 1,
            ];

            if($GetLang == 'en') {
                $message = 'Congratulations, your account has been active';

            } else {
                $message = 'مبروك, تم تفعيل حسابك';
            }

        } else
        {
            $Data = [
                'Status' => 0,
            ];

            if($GetLang == 'en') {
                $message = 'Your account has been inactive';

            } else {
                $message = 'تم ايقاف حسابك';
            }
        }

        DB::table('stadiums')
            ->where('Id', $GetStadiumId)
            ->update($Data);

        (new GeneralController)->SendSingleNotifications($message, $GetTokenId);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function AcceptOrRejectBooking(Request $request)
    {
        $GetBookingId = $request->BookingId;
        $GetIsAccepted = $request->IsAccepted;

        $GetDate = now();
        $NowDateTime = $GetDate->toDateTimeString();

        $Data = [
            'ResponseDate' => $NowDateTime,
            'IsAccepted' => $GetIsAccepted,
        ];

        DB::table('managejoin')
            ->where('Id', $GetBookingId)
            ->update($Data);

        $GetBookingInfo = DB::table('managejoin')
            ->where('Id', '=', $GetBookingId)
            ->first();

        $GetTableName = HelperAPIsFun::GetTableName($GetBookingInfo->CreateByAccountTypeId);

        $GetUser = DB::table($GetTableName->TableName)
            ->where('Id', '=', $GetBookingInfo->CreateByUserId)
            ->first();

        $GetStadium = DB::table('stadiums')
            ->where('Id', '=', $GetBookingInfo->UserId)
            ->first();

        $GetTokenId = $GetUser->TokenId;

        $AllTokenIds = [$GetTokenId];

        if ($GetIsAccepted == 1) {
            // Accept
            $message = __('messages.StadiumReservationConfirmed', [], $GetUser->Lang) . ' ' . $GetStadium->Name;
            (new GeneralController)->SendManyNotifications($message, $AllTokenIds);

        } else if ($GetIsAccepted == 2)  {
            // Reject
            $message = __('messages.StadiumReservationNotConfirm', [], $GetUser->Lang) . ' ' . $GetStadium->Name;
            (new GeneralController)->SendManyNotifications($message, $AllTokenIds);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetBookingTimes(Request $request) {

        // Stadiums Working hours from 4:00 PM To 12:00 AM
        // Reservation time are: For 6.6 is one hour, And for other is one hour and a half

        $GetStadiumId = $request->StadiumId;
        $GetSelectedDate = $request->SelectedDate;

        $Service = DB::table('stadiumsserviceslist')
            ->where('StadiumId', '=', $GetStadiumId)
            ->where('ServicesId', '=', 1) // For Stadium size
            ->first();

        if ($Service) {
            if ($Service->Value == '6.6') {
                $DurationOfReservation = 'OneHour';
            } else {
                $DurationOfReservation = 'OneHourAndHalf';
            }
        } else {
            $DurationOfReservation = 'OneHourAndHalf';
        }

        $AllTimes = $this->getAllTimes($DurationOfReservation);

        date_default_timezone_set('Asia/Riyadh');
        $today = Carbon::now();

       // return date_format($today,"Y/m/d h:i A");
//        $today->setHour(17);
//        $today->setMinute(29);
        $TimeNow = date_format($today,"h:i A");
        $TimeNow_MS = Carbon::parse($TimeNow)->getPreciseTimestamp(3);

        $FinalResult = [];

        $Result = DB::table('managejoin')
            ->where('UserId', '=', $GetStadiumId)
            ->where('AccountTypeId', '=', 6)
            ->where('IsAccepted', '!=', 2)
            ->where('Status', '=', 1)
            ->whereDate('MatchDate', '=', $GetSelectedDate)
            ->get();

        for ($y = 0; $y < count($Result); $y++) {
            $GetMatchDate = $Result[$y]->MatchDate;
            if (date($GetMatchDate) >= date('Y-m-d', strtotime($GetSelectedDate))) {
                $GetDateTime = explode(' ',$GetMatchDate);
                $GetDate = $GetDateTime[0];
                $GetTime = $GetDateTime[1];
                $GetAmPm = $GetDateTime[2];

                $FinalResult[] = ['Date' => $GetDate, 'Time' => $GetTime . ' ' . $GetAmPm];
            }
        }

        for ($n = 0; $n < count($AllTimes); $n++) {
            $FullTime = $AllTimes[$n];

            $FullTimeArray = explode("-", $FullTime);
            $First = $FullTimeArray[0];
            $Second = $FullTimeArray[1];

            $First_MS = Carbon::parse($First)->getPreciseTimestamp(3);

            $Available = true;

            if (date('Y-m-d', strtotime($today)) > date('Y-m-d', strtotime($GetSelectedDate))) {
                $Available = false;
            } else if (date('Y-m-d', strtotime($today)) == date('Y-m-d', strtotime($GetSelectedDate)) && $TimeNow_MS >= $First_MS) {
                $Available = false;
            } else {
                for ($b = 0; $b < count($FinalResult); $b++) {
                    if(str_replace(' ', '', $FinalResult[$b]['Time']) == str_replace(' ', '', $First)) {
                        $Available = false;
                    }
                }
            }
            unset($AllTimes[$n]);

            $AllTimes[$n] = ['Time' => $FullTime, 'Available' => $Available];
        }

        return $this->returnDate('Data', $AllTimes, "");
    }

    public function getAllTimes($DurationOfReservation) {

        if ($DurationOfReservation == 'OneHour') {
            return [
                '04:00 PM - 05:00 PM',
                '05:00 PM - 06:00 PM',
                '06:00 PM - 07:00 PM',
                '07:00 PM - 08:00 PM',
                '08:00 PM - 09:00 PM',
                '09:00 PM - 10:00 PM',
                '10:00 PM - 11:00 PM',
                '11:00 PM - 12:00 AM',
            ];
        } else { // one hour and a half
            return [
                '04:00 PM - 05:30 PM',
                '05:30 PM - 07:00 PM',
                '07:00 PM - 08:30 PM',
                '08:30 PM - 10:00 PM',
                '10:00 PM - 11:30 PM',
            ];
        }

    }

}
