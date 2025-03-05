<?php
namespace App\Http\Controllers\APIs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\DateTime;

class HelperAPIsFun
{

    static function GetTableName($AccountTypeId) {
        $Type = DB::table('useraccountstype')->where('Id', '=', $AccountTypeId)->first();
        return $Type;
    }

    static function GetAMPMTime($Date)
    {
        return date('Y/m/d h:i A', strtotime($Date));
    }


    static function UploadImage($base64_str, $extension)
    {
        if($base64_str != null)
        {
            // Save In Storage Direction:
            // $image = base64_decode($base64_str);
             $NewName = rand().'.'.$extension;
            // Storage::disk('images')->put($NewName, $image);

            $base64_str = str_replace('data:image/png;base64,', '', $base64_str);
            $base64_str = str_replace(' ', '+', $base64_str);
            $data = base64_decode($base64_str);

            $fileDestination = 'images/'.$NewName;

            file_put_contents($fileDestination, $data);

            return $NewName;
        }
    }

    static function CheckFollowing($FromUserId, $FromUserAccountTypeId, $FollowId, $FollowUserAccountTypeId)
    {
        return DB::table('follow')
            ->where('FromUserId', '=', $FromUserId)
            ->where('FromUserAccountTypeId', '=', $FromUserAccountTypeId)
            ->where('FollowId', '=', $FollowId)
            ->where('FollowUserAccountTypeId', '=', $FollowUserAccountTypeId)
            ->first();
    }


}
