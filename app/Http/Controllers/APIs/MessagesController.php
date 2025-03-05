<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MessageObject;

class MessagesController extends Controller
{
    use GeneralTrait;

    public function GetMessagesSingle(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserTypeId = $request->UserTypeId;
        $GetTeamId = $request->TeamId;

        $ResultFinal = [];

        if ($GetTeamId != null && $GetTeamId != 0) {
            $One = [
                'FromUserId' => $GetUserId,
                'FromUserTypeId' => $GetUserTypeId,
                'FromTeamId' => $GetTeamId,
            ];
            $Tow = [
                'ToUserId' => $GetUserId,
                'ToUserTypeId' => $GetUserTypeId,
                'ToTeamId' => $GetTeamId,
            ];
        } else {
            $One = [
                'FromUserId' => $GetUserId,
                'FromUserTypeId' => $GetUserTypeId,
                'FromTeamId' => 0,
                'ToTeamId' => 0,
            ];
            $Tow = [
                'ToUserId' => $GetUserId,
                'ToUserTypeId' => $GetUserTypeId,
                'FromTeamId' => 0,
                'ToTeamId' => 0,
            ];
        }

        $ResultFrom = DB::table('messagesreplies')
            ->where($One)
            ->orderByRaw('Id DESC')
            ->get();

        $ResultTo = DB::table('messagesreplies')
            ->where($Tow)
            ->orderByRaw('Id DESC')
            ->get();

        for($n = 0; $n<count($ResultFrom); $n++)
        {
            $ResultTo[] = $ResultFrom[$n];
        }

        for($w = 0; $w<count($ResultTo); $w++)
        {
            $IsExist = false;

            $GetFromId = $ResultTo[$w]->FromUserId;
            $GetFromTypeId = $ResultTo[$w]->FromUserTypeId;
            $GetToId = $ResultTo[$w]->ToUserId;
            $GetToTypId = $ResultTo[$w]->ToUserTypeId;

            for($v = 0; $v<count($ResultFinal); $v++)
            {
                $GetFinalFromId = $ResultFinal[$v]->FromUserId;
                $GetFinalFromTypeId = $ResultFinal[$v]->FromUserTypeId;
                $GetFinalToId = $ResultFinal[$v]->ToUserId;
                $GetFinalToTypId = $ResultFinal[$v]->ToUserTypeId;

                if(($GetFromId == $GetFinalFromId && $GetFromTypeId == $GetFinalFromTypeId) && ($GetToId == $GetFinalToId && $GetToTypId == $GetFinalToTypId))
                {
                    $IsExist = true;
                }

                if(($GetFromId == $GetFinalToId && $GetFromTypeId == $GetFinalToTypId) && ($GetToId == $GetFinalFromId && $GetToTypId == $GetFinalFromTypeId))
                {
                    $IsExist = true;
                }
            }

            if(!$IsExist)
            {
                $ResultFinal[] = $ResultTo[$w];
            }
        }

        for($y = 0; $y<count($ResultFinal); $y++)
        {
            $ResultFinal[$y] = $this->GetMessagesReady($ResultFinal[$y]);
        }

        return $this->returnDate('Data', $ResultFinal);
    }

    public function GetMessagesReady($Message)
    {
        $FromUserId = $Message->FromUserId;
        $FromUserTypeId = $Message->FromUserTypeId;

        $TablesFrom = HelperAPIsFun::GetTableName($FromUserTypeId);

        $Message->FromUser = DB::table($TablesFrom->TableName)
            ->where('Id', '=', $FromUserId)
            ->first();

        if($Message->FromUser->AccountTypeId == 1 && $Message->FromUser->PositionId != 0)
        {
            $Message->FromUser->Position = DB::table('playerposition')
                ->where('Id', '=', $Message->FromUser->PositionId)
                ->first();
        } else
        {
            $Message->FromUser->Position = null;
        }

        $ToUserId = $Message->ToUserId;
        $ToUserTypeId = $Message->ToUserTypeId;

        $Tables = HelperAPIsFun::GetTableName($ToUserTypeId);

        $Message->ToUser = DB::table($Tables->TableName)
            ->where('Id', '=', $ToUserId)
            ->first();

        if($Message->ToUser->AccountTypeId == 1 && $Message->ToUser->PositionId != 0)
        {
            $Message->ToUser->Position = DB::table('playerposition')
                ->where('Id', '=', $Message->ToUser->PositionId)
                ->first();
        } else
        {
            $Message->ToUser->Position = null;
        }

     //   $Message->CreateDate = date('Y/m/d  h:i A', strtotime($Message->CreateDate));

        return $Message;
    }


    public function GetMessagesDetails(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserTypeId = $request->UserTypeId;
        $UserTeamId = $request->TeamId;
        $GetOtherUserId = $request->OtherUserId;
        $GetOtherUserTypeId = $request->OtherUserTypeId;
        $OtherTeamId = $request->OtherTeamId;

        if ($OtherTeamId != null && $OtherTeamId != 0) {
            $One = [
                'FromUserId' => $GetUserId,
                'FromUserTypeId' => $GetUserTypeId,
                'ToUserId' => $GetOtherUserId,
                'ToUserTypeId' => $GetOtherUserTypeId,
                'ToTeamId' => $OtherTeamId,
                'FromTeamId' => 0,
            ];
            $Tow = [
                'FromUserId' => $GetOtherUserId,
                'FromUserTypeId' => $GetOtherUserTypeId,
                'ToUserId' => $GetUserId,
                'ToUserTypeId' => $GetUserTypeId,
                'ToTeamId' => 0,
                'FromTeamId' => $OtherTeamId,
            ];

        } else if ($UserTeamId != null && $UserTeamId != 0) {
            $One = [
                'FromUserId' => $GetUserId,
                'FromUserTypeId' => $GetUserTypeId,
                'ToUserId' => $GetOtherUserId,
                'ToUserTypeId' => $GetOtherUserTypeId,
                'ToTeamId' => 0,
                'FromTeamId' => $UserTeamId,
            ];
            $Tow = [
                'FromUserId' => $GetOtherUserId,
                'FromUserTypeId' => $GetOtherUserTypeId,
                'ToUserId' => $GetUserId,
                'ToUserTypeId' => $GetUserTypeId,
                'ToTeamId' => $UserTeamId,
                'FromTeamId' => 0,
            ];
        } else {
            $One = [
                'FromUserId' => $GetUserId,
                'FromUserTypeId' => $GetUserTypeId,
                'ToUserId' => $GetOtherUserId,
                'ToUserTypeId' => $GetOtherUserTypeId,
                'FromTeamId' => 0,
                'ToTeamId' => 0,
            ];
            $Tow = [
                'FromUserId' => $GetOtherUserId,
                'FromUserTypeId' => $GetOtherUserTypeId,
                'ToUserId' => $GetUserId,
                'ToUserTypeId' => $GetUserTypeId,
                'FromTeamId' => 0,
                'ToTeamId' => 0,
            ];
        }

        $ResultThree = DB::table('messagesreplies')
            ->where($One)
            ->orderByRaw('Id ASC')
            ->get();

        $FinalResult = DB::table('messagesreplies')
            ->where($Tow)
            ->orderByRaw('Id ASC')
            ->get();


        for($x = 0; $x<count($ResultThree); $x++)
        {
            $FinalResult[] = $ResultThree[$x];
        }

        for($y = 0; $y<count($FinalResult); $y++)
        {
            $FinalResult[$y] = $this->GetMessagesReady($FinalResult[$y]);
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function SendMessage(Request $request)
    {
        $UserId = $request->UserId;
        $UserTypeId = $request->UserTypeId;
        $UserTeamId = $request->TeamId;
        $OtherUserId = $request->OtherUserId;
        $OtherUserTypeId = $request->OtherUserTypeId;
        $OtherTeamId = $request->OtherTeamId;
        $Message = $request->Message;

        $GetDateTeam = Carbon::now()->format('Y/m/d  h:i A');

        $Data = [
            'FromUserId' => $UserId,
            'FromUserTypeId' => $UserTypeId,
            'FromTeamId' => $UserTeamId,
            'ToUserId' => $OtherUserId,
            'ToUserTypeId' => $OtherUserTypeId,
            'ToTeamId' => $OtherTeamId,
            'Message' => $Message,
            'CreateDate' => $GetDateTeam,
        ];

        DB::table('messagesreplies')->insert($Data);

        $ToUserTable = HelperAPIsFun::GetTableName($OtherUserTypeId);

        $ToUserUserInfo = DB::table($ToUserTable->TableName)
            ->where('Id', '=', $OtherUserId)
            ->first();

        $NotificationMessage = "";

        if ($UserTeamId != null && $UserTeamId != 0) {
            $TeamInfo = DB::table('teams')
                ->where('Id', '=', $UserId)
                ->first();

            if ($ToUserUserInfo->Lang == 'en') {
                $NotificationMessage = $TeamInfo->NameEn . ': ' . $Message;
            } else {
                $NotificationMessage = $TeamInfo->NameAr . ': ' . $Message;
            }
        } else {
            $FromUserTable = HelperAPIsFun::GetTableName($UserTypeId);

            $FromUserInfo = DB::table($FromUserTable->TableName)
                ->where('Id', '=', $UserId)
                ->first();

            $NotificationMessage = $FromUserInfo->FirstName . ' ' . $FromUserInfo->LastName . ': ' . $Message;
        }

        (new GeneralController)->SendSingleNotifications($NotificationMessage, $ToUserUserInfo->TokenId);

//        $CheckResult = DB::table('messages')
//            ->where([
//                'FromUserId' => $request->UserId,
//                'FromUserTypeId' => $request->UserTypeId,
//                'ToUserId' => $request->OtherUserId,
//                'ToUserTypeId' => $request->OtherUserTypeId,
//            ])
//            ->orderByRaw('Id DESC')
//            ->get();
//
//        if($CheckResult)
//        {
//            DB::table('messagesreplies')->insert($Data);
//
//        } else
//        {
//            $CheckResult = DB::table('messages')
//                ->where([
//                    'FromUserId' => $request->OtherUserId,
//                    'FromUserTypeId' => $request->OtherUserTypeId,
//                    'ToUserId' => $request->UserId,
//                    'ToUserTypeId' => $request->UserTypeId,
//                ])
//                ->orderByRaw('Id DESC')
//                ->get();
//
//            if($CheckResult)
//            {
//                DB::table('messagesreplies')->insert($Data);
//
//            } else {
//                DB::table('messages')->insert($Data);
//            }
//        }

        return $this->returnDate('ExecuteStatus', true);
    }

    function MessageSeen(Request $request)
    {
        $UserId = $request->UserId;
        $UserTypeId = $request->UserTypeId;
        $UserTeamId = $request->TeamId;
        $OtherUserId = $request->OtherUserId;
        $OtherUserTypeId = $request->OtherUserTypeId;
        $OtherTeamId = $request->OtherTeamId;

        $Result = DB::table('messagesreplies')
            ->where([
                'ToUserId' => $UserId,
                'ToUserTypeId' => $UserTypeId,
                'ToTeamId' => $UserTeamId,
                'FromUserId' => $OtherUserId,
                'FromUserTypeId' => $OtherUserTypeId,
                'FromTeamId' => $OtherTeamId,
            ])
            ->update(['IsSeen'=> "1"]);

        return $this->returnDate('ExecuteStatus', true);
    }

    function CheckNewMessages(Request $request)
    {
        $UserId = $request->UserId;
        $UserTypeId = $request->UserTypeId;

        $TeamNewMessageCount = 0;

        if ($UserTypeId == 1) {
            $UserInfo = DB::table('players')
                ->where('Id', '=', $UserId)
                ->first();

            $GetTeamId = $UserInfo->TeamId;

            if ($GetTeamId != null && $GetTeamId != 0) {
                $TeamNewMessageCount += DB::table('messagesreplies')
                    ->where([
                        'ToUserId' => $UserId,
                        'ToUserTypeId' => $UserTypeId,
                        'FromTeamId' => $GetTeamId,
                        'IsSeen'=> "0",
                    ])
                    ->count();
                $TeamNewMessageCount += DB::table('messagesreplies')
                    ->where([
                        'ToUserId' => $UserId,
                        'ToUserTypeId' => $UserTypeId,
                        'ToTeamId' => $GetTeamId,
                        'IsSeen'=> "0",
                    ])
                    ->count();
            }
        }

        $UserNewMessageCount = DB::table('messagesreplies')
            ->where([
                'ToUserId' => $UserId,
                'ToUserTypeId' => $UserTypeId,
                'FromTeamId' => 0,
                'ToTeamId' => 0,
                'IsSeen'=> "0",
            ])
            ->count();

        $Counts = ['UserNewMessageCount' => $UserNewMessageCount, 'TeamNewMessageCount' =>  $TeamNewMessageCount];

        return $this->returnDate('Data', $Counts);
    }


}
