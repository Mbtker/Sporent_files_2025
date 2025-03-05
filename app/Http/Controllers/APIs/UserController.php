<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Http\Controllers\HelpersController;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Alkoumi\LaravelHijriDate\Hijri;

class UserController extends Controller
{
    use GeneralTrait;

    public function Login(Request $request)
    {
        $Code = (new HelperController)->generateRandomCode();

        $Phone = $request->Phone;
        $Language = $request->Lang;

        $Result = DB::table('players')->where('Phone', '=', $Phone)->first();

        if($Result)
        {
            if($Result->PositionId != null && $Result->PositionId != 0)
            {
                $Result->Position = DB::table('playerposition')->where('Id', '=', $Result->PositionId)->first();
            } else
            {
                $Result->Position = null;
            }
        }

        if(!$Result || $Result === null)
            $Result = DB::table('coaches')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('referees')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('commentators')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('photographers')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('stadiums')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('advertisingagencies')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('stores')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('scoutsofclubs')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('physiotherapyclinics')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('supervisors')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('organizers')->where('Phone', '=', $Phone)->first();

        if(!$Result || $Result === null)
            $Result = DB::table('leagueorganizers')->where('Phone', '=', $Phone)->first();

        $Message = 'رمز التحقق هو ' . $Code;

        if($Language === 'en') {
            $Message = 'Your verification code is ' . $Code;
        }

        $this->SendVerificationCode($Phone, $Message);

       // (new HelperController)->ErrorSMSSending($Phone, $Message, "", "", "");

        if(!$Result || $Result === null)
            return $this->returnLoginError('', 'Not registered', $Code);

        return $this->returnLoginDate('User', $Result, $Code);
    }

    public function SendVerificationCode($Phone, $Message) {
        $SendMSMRequest = new Request();
        $SendMSMRequest->setMethod('POST');
        $SendMSMRequest->request->add(['Phone' => $Phone, 'Message' => $Message]);
        (new HelperController)->SendSMS($SendMSMRequest);
    }

    public function UpdateAfterUserLogin(Request $request)
    {
        $TableName = HelperController::GetTableNameByAccountTypeId($request->AccountTypeId);

        $now = now();

        if ($request->UpdateLocation == 1) {
            $Data = ['Location'=> $request->Location, 'Lang'=> $request->Lang, 'DeviceType'=> $request->DeviceType, 'LastActive'=> $now, 'TokenId'=> $request->TokenId];
        } else {
            $Data = ['Lang'=> $request->Lang, 'DeviceType'=> $request->DeviceType, 'LastActive'=> $now, 'TokenId'=> $request->TokenId];
        }

        DB::table($TableName)->where('Id', $request->Id)
            ->update($Data);

        return response()->json(['status' => true]);
    }

    public function AccountsType()
    {
        $Result = DB::table('useraccountstype')
            ->where('Status', '=', "1")
            ->where('RegisterFromApp', '=', "1")
            ->orderByRaw('OrderItem ASC')->get();

        return $this->returnDate('AccountsType', $Result, "");
    }


    public function Register(Request $request)
    {
        $GetDate = now();
        $NowDateTime = $GetDate->toDateTimeString();

        $Result = false;

        $Data = [
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Location' => $request->Location,
            'Lang' => $request->Lang,
            'DeviceType' => $request->DeviceType,
            'TokenId' => $request->TokenId,
            'LastActive' => $NowDateTime,
        ];

        $TableName = HelperAPIsFun::GetTableName($request->AccountTypeId);

        if($request->AccountTypeId == "1")
        {
            $FinalBirthday = $request->Birthday;
            $ArrayBirth = explode('/', $FinalBirthday);

            if (count($ArrayBirth) > 0) {
                $Year = $ArrayBirth[0];
                $Month = $ArrayBirth[1];
                $Day = $ArrayBirth[2];
                try {
                    $FinalBirthday = Hijri::DateToGregorianFromDMY($Day, $Month, $Year);
                } catch (\Exception $e) {}
            }

            // Player
            $Data += [
                'Birthday' => $FinalBirthday,
            ];

        } else if($request->AccountTypeId == "3")
        {
            // Referee

            $Data += [
                'Fee' => $request->Fee,
            ];

            $this->SendToSupervisors('تم تسجيل حكم جديد, بانتظار الاعتماد');

        } else if($request->AccountTypeId == "4")
        {
            // Commentator

            $Data += [
                'Fee' => $request->Fee,
            ];

            $this->SendToSupervisors('تم تسجيل معلق صوت جديد, بانتظار الاعتماد');

        }  else if($request->AccountTypeId == "5")
        {
            // Photographer

            $Data += [
                'Fee' => $request->Fee,
            ];

        } else if($request->AccountTypeId == "6")
        {
            // Stadium
            $this->SendToSupervisors('تم انضمام ملعب جديد, بانتظار الاعتماد');

            $Data += [
                'Name' => $request->Name,
            ];

        } else if($request->AccountTypeId == "7")
        {
            // Advertising agency

            $Data += [
                'Name' => $request->Name,
            ];

        } else if($request->AccountTypeId == "8")
        {
            // Store

            $Data += [
                'StoreCategoryId' => $request->StoreCategoryId,
                'Name' => $request->Name,
            ];

        } else if($request->AccountTypeId == "9")
        {
            // Scout club

            $Data += [
                'Name' => $request->Name,
            ];

        } else if($request->AccountTypeId == "10")
        {
            // Physiotherapy clinic

            $Data += [
                'Name' => $request->Name,
            ];
        } else if($request->AccountTypeId == "12")
        {
            // Organizer

            $Data += [
                'Fee' => $request->Fee,
            ];

            $this->SendToSupervisors('تم تسجيل منظم جديد, بانتظار الاعتماد');

        } else if($request->AccountTypeId == "14")
        {
            // League Organizer
            $this->SendToSupervisors('تم تسجيل منظم دوريات جديد, بانتظار الاعتماد');

            $Data += [
                'LeagueNoAYear' => $request->LeagueNoAYear,
            ];
        }

        $Result = DB::table($TableName->TableName)->insertGetId($Data);

        if(!$Result)
            return $this->returnError('', 'Not found');

        return $this->returnDate('Register', ['Id' => $Result]);

    }
    public function SendToSupervisors($message)
    {
        $AllSupervisors = DB::table('supervisors')
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        $AllTokenIds = [];

        for($y = 0; $y<count($AllSupervisors); $y++)
        {
            $GetTokenId = $AllSupervisors[$y]->TokenId;
            $AllTokenIds[] = $GetTokenId;
        }

        (new GeneralController)->SendManyNotifications($message, $AllTokenIds);
    }


    public function UploadImage(Request $request)
    {
        $GetDate = now();
        $NowDateTime = $GetDate->toDateTimeString();

        $FinalFile = $request->File;

        if($request->FileType == 'image')
            $FinalFile = HelperAPIsFun::UploadImage($FinalFile, 'jpeg');

        $Data = [
            'UserId' => $request->Id,
            'UserAccountTypeId' => $request->AccountTypeId,
            'File' => $FinalFile,
            'Topic' => $request->Topic,
            'Descr' => $request->Descr,
            'FileType' => $request->FileType,
            'CreateDate' => $NowDateTime,
        ];

        $Result = DB::table('attachments')->insertGetId($Data);

        if($Result) {
            return response()->json([
                'status' => true,
                'MediaId' => $Result,
                'MediaUrl' => $FinalFile
            ]);

        } else {
            return $this->returnError('', []);
        }
    }

    public function GetUserMedia(Request $request)
    {
        $Result = DB::table('attachments')
            ->where('UserId', '=', $request->UserId)
            ->where('UserAccountTypeId', '=', $request->UserAccountTypeId)
            ->where('IsDeleted', '=', "0")
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $GetUserId = $Result[$y]->UserId;
            $GetUserAccountTypeId = $Result[$y]->UserAccountTypeId;

            if($GetUserAccountTypeId == 1)
            {
                $PlayerInfo = DB::table('players')
                    ->where('Id', '=', $GetUserId)
                    ->first();

                if ($PlayerInfo->TeamId != null) {
                    $Result[$y]->UserTeamId = $PlayerInfo->TeamId;
                } else {
                    $Result[$y]->UserTeamId = 0;
                }
            } else
            {
                $Result[$y]->UserTeamId = 0;
            }
        }

        return $this->returnDate('Data', $Result, "");
    }

    public function DeleteMedia(Request $request)
    {
        $GetDate = now();
        $NowDateTime = $GetDate->toDateTimeString();
        $Result = DB::table('attachments')->where('Id', $request->FileId)->update(['IsDeleted'=> "1", 'ShareWithTeam'=> "0", 'IsInCommunity'=> "0", 'DeletedDate'=> $NowDateTime]);

        if($Result)
        {
            return $this->returnDate('', []);

        } else
        {
            return $this->returnError('', []);
        }
    }

    public function UploadVideo(Request $request)
    {
        if ($request->hasFile('File'))
        {
            $videoName = HelperController::uploadVideo($request->file('File'));

            if ($request->Thumbnail != "")
            {
                $request->Thumbnail = HelperAPIsFun::UploadImage($request->Thumbnail, 'jpeg');
            }

            $GetDate = now();
            $NowDateTime = $GetDate->toDateTimeString();

            $Data = [
                'UserId' => $request->Id,
                'UserAccountTypeId' => $request->AccountTypeId,
                'File' => $videoName,
                'VideoWidth' => (int)$request->VideoWidth,
                'VideoHeight' => (int)$request->VideoHeight,
                'VideoThumbnail' => $request->Thumbnail,
                'Topic' => $request->Topic,
                'Descr' => $request->Descr,
                'FileType' => 'video',
                'CreateDate' => $NowDateTime,
            ];

            $Result = DB::table('attachments')->insertGetId($Data);

            if($Result) {
                return response()->json([
                    'status' => true,
                    'MediaId' => $Result,
                    'MediaUrl' => $videoName,
                    'ThumbnailUrl' => $request->Thumbnail
                ]);

            } else {
                return $this->returnError('11', []);
            }

        } else
        {
            return $this->returnError('00', []);
        }
    }

    public function UpdateVideoSize(Request $request)
    {
        $GetMediaId = $request->Id;
        $GetVideoWidth = (int)$request->VideoWidth;
        $GetVideoHeight = (int)$request->VideoHeight;

        $Result = DB::table('attachments')
            ->where('Id', $GetMediaId)
            ->update(['VideoWidth'=> $GetVideoWidth, 'VideoHeight'=> $GetVideoHeight]);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function FetchUserData(Request $request)
    {
        // Posts Count
        $PostsCount = DB::table('attachments')
            ->where('UserId', '=', $request->Id)
            ->where('UserAccountTypeId', '=', $request->AccountTypeId)
            ->where('IsDeleted', '=', '0')
            ->get()->count();

        return $this->returnDate('Data', ['PostsCount' => $PostsCount]);
    }

/*    public function GetAllMedia(Request $request)
    {
        $Result = DB::table('attachments')
            ->where('FileType', '=', "video")
            ->where('IsDeleted', '=', 0)
            ->where('IsInCommunity','=', 1)
            ->where('UserAccountTypeId','!=', 6)
            ->where('UserAccountTypeId','!=', 7)
            ->where('UserAccountTypeId','!=', 8)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $GetFileId = $Result[$y]->Id;

            $GetUserId = $Result[$y]->UserId;
            $GetUserAccountTypeId = $Result[$y]->UserAccountTypeId;

            $TableName = HelperController::GetTableNameByAccountTypeId($GetUserAccountTypeId);

            $UserInfo = DB::table($TableName)
                ->where('Id', '=', $GetUserId)
                ->first();

            //  $Result[$y]->UserImage = $UserInfo->Image;

            $GetShareCount = DB::table('shares')
                ->where('FileId', '=', $GetFileId)
                ->get()->count();

            $Result[$y]->ShareCount = $GetShareCount;

            $GetLikeCount = DB::table('likes')
                ->where('FileId', '=', $GetFileId)
                ->get()->count();

            $Result[$y]->LikeCount = $GetLikeCount;

            $GetIfUserLike = DB::table('likes')
                ->where('FileId', '=', $GetFileId)
                ->where('FromUserId', '=', $request->Id)
                ->where('UserAccountTypeId', '=', $request->AccountTypeId)
                ->get()->count();

            if($GetIfUserLike)
            {
                $Result[$y]->ThisUserLike = true;

            } else
            {
                $Result[$y]->ThisUserLike = false;
            }

            $GetCommentsCount = DB::table('comments')
                ->where('FileId', '=', $GetFileId)
                ->where('IsDeleted', '=', '0')
                ->get()->count();

            $Result[$y]->CommentsCount = $GetCommentsCount;

            $GetIsFollow = DB::table('follow')
                ->where('FromUserId', '=', $request->Id)
                ->where('FromUserAccountTypeId', '=', $request->AccountTypeId)
                ->where('FollowId', '=', $Result[$y]->UserId)
                ->where('FollowUserAccountTypeId', '=', $Result[$y]->UserAccountTypeId)
                ->where('IsFollow', '=', '1')
                ->get()->count();

            if($GetIsFollow)
            {
                $Result[$y]->IsFollow = true;

            } else
            {
                $Result[$y]->IsFollow = false;
            }

        }

        return $this->returnDate('Media', $Result, "");
    }*/

    public function GetAllMedia(Request $request) {

      //  $Id = $request->Id;
     //   $AccountTypeId = $request->AccountTypeId;

      //  $Result = DB::select('SELECT DISTINCT att.Id Id, pl.Id UserId, pl.AccountTypeId UserAccountTypeId, att.File File, att.VideoWidth VideoWidth, att.VideoHeight VideoHeight, att.Topic Topic, att.Descr Descr, att.FileType FileType, att.ShareWithTeam ShareWithTeam, att.IsDeleted IsDeleted, att.DeletedDate DeletedDate, att.CreateDate CreateDate, pl.Image UserImage, COUNT(DISTINCT lk.Id) LikeCount, COUNT(DISTINCT sh.Id) ShareCount, COUNT(DISTINCT com.Id) CommentsCount, CASE WHEN COUNT(DISTINCT lke.Id) > 0 THEN 1 ELSE 0 END as ThisUserLike, CASE WHEN COUNT(DISTINCT fl.Id) > 0 THEN true ELSE false END as IsFollow FROM attachments att LEFT JOIN players pl ON att.UserId = pl.Id LEFT JOIN likes lk ON lk.FileId = att.Id LEFT JOIN likes lke ON lke.FileId = att.Id AND lke.FromUserId = ? AND lke.UserAccountTypeId = ? LEFT JOIN comments com ON com.FileId = att.Id AND com.IsDeleted = 0 LEFT JOIN shares sh ON sh.FileId = att.Id LEFT JOIN follow fl ON fl.FromUserId = ? AND fl.FromUserAccountTypeId = ? AND fl.FollowId = att.UserId AND fl.FollowUserAccountTypeId = att.UserAccountTypeId AND fl.IsFollow = 1 WHERE att.FileType = "video" AND att.IsDeleted = 0 AND att.IsInCommunity = 1 AND att.UserAccountTypeId != 6 AND att.UserAccountTypeId != 7 AND att.UserAccountTypeId != 8 GROUP BY att.Id, pl.Id, pl.AccountTypeId, att.File, att.VideoWidth, att.VideoHeight, att.Topic, att.Descr, att.FileType, att.ShareWithTeam, att.IsDeleted, att.DeletedDate, att.CreateDate, pl.Image ORDER BY Id DESC', [$Id, $AccountTypeId, $Id, $AccountTypeId]);

	    $UserId = $request->Id;
        $AccountTypeId = $request->AccountTypeId;

        $AllMedia = [];
        
		$Result = DB::table('attachments')
            ->where('FileType', '=', 'video')
            ->where('IsInCommunity', '=', 1)
            ->where('IsCommunityApproved', '=', 1)
            ->where('IsDeleted', '=', 0)
            ->orderByRaw('Id DESC')
            ->get()->take(60);

        for($y = 0; $y<count($Result); $y++) {
            $GetUserId = $Result[$y]->UserId;
            $GetUserAccountTypeId = $Result[$y]->UserAccountTypeId;

            $FileId = $Result[$y]->Id;
            $File = $Result[$y]->File;
            $VideoWidth = $Result[$y]->VideoWidth;
            $VideoHeight = $Result[$y]->VideoHeight;
            $Topic = $Result[$y]->Topic;
            $Descr = $Result[$y]->Descr;
            $FileType = $Result[$y]->FileType;
            $ShareWithTeam = $Result[$y]->ShareWithTeam;
            $IsDeleted = $Result[$y]->IsDeleted;
            $DeletedDate = $Result[$y]->DeletedDate;
            $CreateDate = $Result[$y]->CreateDate;
            $ThisUserLike = 0; // 1
            $IsFollow = 0; // 1

            $TableName = HelperAPIsFun::GetTableName($GetUserAccountTypeId);

            $UserInfo = DB::table($TableName->TableName)
                ->where('Id', '=', $GetUserId)
                ->first();

            $UserImage = $UserInfo->Image;

            $LikeCount = DB::table('likes')
                ->where('FileId', '=', $FileId)
                ->count();

            $ShareCount = DB::table('shares')
                ->where('FileId', '=', $FileId)
                ->count();

            $CommentsCount = DB::table('comments')
                ->where('FileId', '=', $FileId)
                ->where('IsDeleted', '=', 0)
                ->count();

            $CheckUserLike = DB::table('likes')
                ->where('FileId', '=', $FileId)
                ->where('FromUserId', '=', $UserId)
                ->where('UserAccountTypeId', '=', $AccountTypeId)
                ->first();

            if ($CheckUserLike) {
                $ThisUserLike = 1;
            }

            $CheckFollow = DB::table('follow')
                ->where('FromUserId', '=', $UserId)
                ->where('FromUserAccountTypeId', '=', $AccountTypeId)
                ->where('FollowId', '=', $GetUserId)
                ->where('FollowUserAccountTypeId', '=', $GetUserAccountTypeId)
                ->where('IsFollow', '=', 1)
                ->first();

            if ($CheckFollow) {
                $IsFollow = 1;
            }

            $AllMedia[] = ['Id' => $FileId, 'UserId' => $GetUserId, 'UserAccountTypeId' => $GetUserAccountTypeId, 'File' => $File, 'VideoWidth' => $VideoWidth, 'VideoHeight' => $VideoHeight,
                'Topic' => $Topic, 'Descr' => $Descr, 'FileType' => $FileType, 'ShareWithTeam' => $ShareWithTeam, 'IsDeleted' => $IsDeleted, 'DeletedDate' => $DeletedDate, 'CreateDate' => $CreateDate,
                'UserImage' => $UserImage, 'LikeCount' => $LikeCount, 'ShareCount' => $ShareCount, 'CommentsCount' => $CommentsCount, 'ThisUserLike' => $ThisUserLike, 'IsFollow' => $IsFollow];
        }

        return $this->returnDate('Media', $AllMedia, "");
    }

    public function ShareFile(Request $request)
    {
        $Data = [
            'FromUserId' => $request->Id,
            'UserAccountTypeId' => $request->AccountTypeId,
            'FileId' => $request->FileId,
        ];

        $Result = DB::table('shares')->insert($Data);

        return $this->returnDate('Share', []);
    }

    public function LikeFile(Request $request)
    {
        $Check = DB::table('likes')
            ->where('FromUserId', '=', $request->Id)
            ->where('UserAccountTypeId', '=', $request->AccountTypeId)
            ->where('FileId', '=', $request->FileId)
            ->first();

        if($Check)
        {
            DB::table('likes')
                ->where('Id', '=', $Check->Id)
                ->delete();
        } else
        {
            $Data = [
                'FromUserId' => $request->Id,
                'UserAccountTypeId' => $request->AccountTypeId,
                'FileId' => $request->FileId,
            ];

            DB::table('likes')->insert($Data);
        }

        return $this->returnDate('like', []);
    }

    public function GetCommentsOfFile(Request $request)
    {
        $Result = DB::table('comments')
            ->where('FileId', '=', $request->FileId)
            ->where('IsDeleted', '=', '0')
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $GetUserId = $Result[$y]->ByUserId;
            $GetUserAccountTypeId = $Result[$y]->UserAccountTypeId;

            $TableName = HelperController::GetTableNameByAccountTypeId($GetUserAccountTypeId);

            $User = DB::table($TableName)
                ->where('Id', '=', $GetUserId)
                ->first();

            $Result[$y]->ByName =  "";
            $Result[$y]->ByUserIMG =  "";

            if ($User) {
                if ($User->FirstName != null && $User->LastName != null) {
                    $Result[$y]->ByName = $User->FirstName . ' ' . $User->LastName;
                }
                if ($User->Image != null) {
                    $Result[$y]->ByUserIMG = $User->Image;
                }
            }

            $Result[$y]->CreateDate = date('Y-m-d  h:i A', strtotime($Result[$y]->CreateDate));
        }
        return $this->returnDate('Comments', $Result, "");
    }

    public function SendComment(Request $request)
    {
        $GetDate = now();
        $NowDateTime = $GetDate->toDateTimeString();

        $Data = [
            'ByUserId' => $request->Id,
            'UserAccountTypeId' => $request->AccountTypeId,
            'FileId' => $request->FileId,
            'Comment' => $request->Comment,
            'CreateDate' => $NowDateTime,
        ];

        $Result = DB::table('comments')->insert($Data);

        return $this->returnDate('Send', []);
    }

    public function Follow(Request $request)
    {
        $Check = DB::table('follow')
            ->where('FromUserId', '=', $request->FromUserId)
            ->where('FromUserAccountTypeId', '=', $request->FromUserAccountTypeId)
            ->where('FollowId', '=', $request->FollowId)
            ->where('FollowUserAccountTypeId', '=', $request->FollowUserAccountTypeId)
            ->first();

        if(!$Check)
        {
            $Data = [
                'FromUserId' => $request->FromUserId,
                'FromUserAccountTypeId' => $request->FromUserAccountTypeId,
                'FollowId' => $request->FollowId,
                'FollowUserAccountTypeId' => $request->FollowUserAccountTypeId,
            ];

            $Result = DB::table('follow')->insert($Data);
        }

        return $this->returnDate('Send', []);
    }

    public function GetMangeUsers(Request $request)
    {
        $GetUserTypeId = $request->UserTypeId;
        $GetStatus = $request->Status;

        $TableName = HelperController::GetTableNameByAccountTypeId($GetUserTypeId);
        $AllUsers = [];


        if($GetStatus == "WaitingApproval")
        {
            $AllUsers = DB::select('SELECT * FROM ' . $TableName . ' WHERE IsApproved = 0 || IsApproved IS null ORDER BY Id DESC');

        } else if($GetStatus == "Active")
        {
            $AllUsers = DB::table($TableName)
                ->where('IsApproved', '=', 1)
                ->where('Status', '=', 1)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == "Inactive")
        {
            $AllUsers = DB::table($TableName)
                ->where('IsApproved', '=', 1)
                ->where('Status', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();
        }

        return $this->returnDate('Data', $AllUsers, "");
    }

    public function GetMediaMange(Request $request)
    {
        $GetStatus = $request->Status;

        $Result = [];

        if($GetStatus == 'New')
        {
            $Result = DB::table('attachments')
                ->where('FileType', '=', 'video')
                ->where('IsInCommunity', '=', 1)
                ->where('IsCommunityApproved', '=', 0)
                ->where('IsDeleted', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == 'ActiveMedia')
        {
            $Result = DB::table('attachments')
                ->where('FileType', '=', 'video')
                ->where('IsInCommunity', '=', 1)
                ->where('IsCommunityApproved', '=', 1)
                ->where('IsDeleted', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == 'InactiveMedia')
        {
            $Result = DB::table('attachments')
                ->where('FileType', '=', 'video')
                ->where('IsInCommunity', '=', 0)
                ->where('IsDeleted', '=', 1)
                ->orderByRaw('Id DESC')
                ->get();

        } else if($GetStatus == 'AllMedia')
        {
//            $Result = DB::table('attachments')
//                ->where(['FileType' => 'video', 'IsInCommunity' => 0, 'IsDeleted' => 0])
//                ->orWhere(['FileType' => 'video','IsInCommunity' => 1, 'IsCommunityApproved' => -1])
//                ->orderByRaw('Id DESC')
//                ->get();

            $Result = DB::select('SELECT * FROM attachments att WHERE att.FileType = "video" AND att.IsDeleted = 0 AND (att.IsInCommunity = 0) OR (att.IsInCommunity = 1 AND att.IsCommunityApproved = -1)');

        }

        for($y = 0; $y<count($Result); $y++)
        {
            $GetUserId = $Result[$y]->UserId;
            $GetUserAccountTypeId = $Result[$y]->UserAccountTypeId;

            $TableName = HelperController::GetTableNameByAccountTypeId($GetUserAccountTypeId);

            $User = DB::table($TableName)
                ->where('Id', '=', $GetUserId)
                ->first();

            $Result[$y]->User = $User;
        }

        return $this->returnDate('Data', $Result, "");
    }

    public function UpdateMediaInfo(Request $request)
    {
        $GetMediaId = $request->MediaId;
        $GetTopic = $request->Topic;
        $GetDesc = $request->Desc;

        $Result = DB::table('attachments')
            ->where('Id', $GetMediaId)
            ->update(['Topic'=> $GetTopic, 'Descr'=> $GetDesc]);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function ShareMediaWithTeam(Request $request)
    {
        $GetMediaId = $request->MediaId;
        $GetShare = $request->Share;

        $Result = DB::table('attachments')
            ->where('Id', $GetMediaId)
            ->update(['ShareWithTeam'=>$GetShare]);

        return $this->returnDate('ExecuteStatus', true);
    }
	
    public function MangeAddOrRemoveMediaToCommunity(Request $request)
    {
        $GetMediaId = $request->MediaId;
        $GetIsInCommunity = $request->IsInCommunity;
        $GetByUserId = $request->ByUserId;
        $GetByUserAccountId = $request->ByUserAccountId;

        if ($GetIsInCommunity == 1) {
            $Date = [
                'IsInCommunity'=> $GetIsInCommunity,
                'IsCommunityApproved'=> 1,
                'ApprovedBy'=> $GetByUserId,
                'ApprovedByAccountTypeId'=> $GetByUserAccountId
            ];
        } else {
            $Date = [
                'IsInCommunity'=> $GetIsInCommunity,
                'IsCommunityApproved'=> -1,
                'ApprovedBy'=> $GetByUserId,
                'ApprovedByAccountTypeId'=> $GetByUserAccountId
            ];
        }

        $Result = DB::table('attachments')
            ->where('Id', $GetMediaId)
            ->update($Date);

        $VideoInfo = DB::table('attachments')
            ->where('Id', '=', $GetMediaId)
            ->first();

        $TableName = HelperController::GetTableNameByAccountTypeId($VideoInfo->UserAccountTypeId);

        $OwnerInfo = DB::table($TableName)
            ->where('Id', '=', $VideoInfo->UserId)
            ->first();

        $GetTokenId = $OwnerInfo->TokenId;
        $GetLang = $OwnerInfo->Lang;

        if ($GetTokenId != "" && $GetLang != "") {
            if ($GetLang == 'en') {
                if ($GetIsInCommunity == 1) {
                    $Message = 'Your video has been approved in My Community';
                } else {
                    $Message = 'Your video has been rejected in My Community';
                }
            } else {
                if ($GetIsInCommunity == 1) {
                    $Message = 'تم نشر الفيديو بمجتمعي';
                } else {
                    $Message = 'تم الغاء نشر الفيديو بمجتمعي';
                }
            }
            $FinalResult = [$GetTokenId];
            HelperController::SendNotifications($FinalResult, $Message);
        }

        return $this->returnDate('ExecuteStatus', true);
    }
	
    public function AddOrRemoveMediaToCommunity(Request $request)
    {
        $GetMediaId = $request->MediaId;
        $GetIsInCommunity = $request->IsInCommunity;

        $Result = DB::table('attachments')
            ->where('Id', $GetMediaId)
            ->update(['IsInCommunity'=> $GetIsInCommunity]);

        $this->SendToSupervisors('يوجد طلب جديد لاضافة فيديو لمجتمعي');

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetFollowList(Request $request)
    {
        $FollowType = $request->FollowType;
        $UserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;

        if($FollowType == "Following")
        {
            $Result = DB::table('follow')
                ->where('FromUserId', '=', $UserId)
                ->where('FromUserAccountTypeId', '=', $GetUserAccountTypeId)
                ->where('IsFollow', '=', 1)
                ->get();

            for($y = 0; $y<count($Result); $y++)
            {
                $GetFollowId = $Result[$y]->FollowId;
                $GetFollowUserAccountTypeId = $Result[$y]->FollowUserAccountTypeId;

                $TableName = HelperController::GetTableNameByAccountTypeId($GetFollowUserAccountTypeId);

                if ($GetFollowUserAccountTypeId == 13) { // Account type Id Of Team

                    $Result[$y]->TeamInfo = DB::table($TableName)
                        ->where('Id', '=', $GetFollowId)
                        ->first();

                } else {
                    $Result[$y]->UserInfo = DB::table($TableName)
                        ->where('Id', '=', $GetFollowId)
                        ->first();
                }
            }
        } else
        {
            $Result = DB::table('follow')
                ->where('FollowId', '=', $UserId)
                ->where('FollowUserAccountTypeId', '=', $GetUserAccountTypeId)
                ->where('IsFollow', '=', 1)
                ->get();

            for($y = 0; $y<count($Result); $y++)
            {
                $GetFromUserId = $Result[$y]->FromUserId;
                $GetFromUserAccountTypeId = $Result[$y]->FromUserAccountTypeId;

                $TableName = HelperController::GetTableNameByAccountTypeId($GetFromUserAccountTypeId);

                if ($GetFromUserAccountTypeId == 13) { // Account type Id Of Team

                    $Result[$y]->TeamInfo = DB::table($TableName)
                        ->where('Id', '=', $GetFromUserId)
                        ->first();

                } else {
                    $Result[$y]->UserInfo = DB::table($TableName)
                        ->where('Id', '=', $GetFromUserId)
                        ->first();
                }
            }
        }

        return $this->returnDate('Data', $Result, "");
    }

    public function GetUserDocuments(Request $request)
    {
        $UserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;

        $Result = DB::table('documents')
            ->where('UserId', '=', $UserId)
            ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
            ->get();

        return $this->returnDate('Data', $Result, "");
    }

    public function UserUploadDocument(Request $request)
    {
        $UserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $GetName = $request->Name;
        $GetFile = $request->File;


        $CheckFile = DB::table('documents')
            ->where('UserId', '=', $UserId)
            ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
            ->where('Name', '=', $GetName)
            ->first();

        if($CheckFile)
        {
            DB::table('documents')
                ->where('Id', $CheckFile->Id)
                ->update(['File'=> $GetFile]);
        } else
        {
            $Data = [
                'UserId' => $UserId,
                'UserAccountTypeId' => $GetUserAccountTypeId,
                'File' => $GetFile,
                'Name' => $GetName,
            ];


            DB::table('documents')->insert($Data);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

	public function SendDeleteUserDataRequest(Request $request)
    {
        $Check = DB::table('deleteuserdatarequest')
            ->where('Phone','=' , $request->Phone)
            ->where('ApprovedBy','=' , 0)
            ->first();

        if ($Check) {
            return ['status' => '2'];
        } else {
            DB::table('deleteuserdatarequest')->insert([
                'FullName' => $request->Name,
                'Phone' => $request->Phone,
                'Email' => $request->Email
            ]);
            return ['status' => '1'];
        }
    }
}
