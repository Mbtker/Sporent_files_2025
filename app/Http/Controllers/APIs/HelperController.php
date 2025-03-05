<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use DateTime;
use ffmpeg_movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lakshmaji\Thumbnail\Thumbnail;

class HelperController extends Controller
{
    use GeneralTrait;

    static function SendSMS(Request $request)
    {
        $Phone = $request->Phone;
        $Message = $request->Message;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://msegat.com/gw/sendsms.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"userName\":\"Sporent\",\n  \"numbers\": \"$Phone\",\n  \"userSender\":\"Sport talen\",\n  \"apiKey\":\"88e0bc0c5c59031643cac31e18101bde\",\n  \"msg\":\"$Message\"\n}",
           CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 3ee535b6-7e72-d81e-09d2-7d662ff5e82b"
            ),
        ));

        $responseExec = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $Response = json_decode($responseExec);

        if ($Response->code != '1') {
            (new HelperController)->ErrorSMSSending($Phone, $Message, $Response->code, $Response->message, $responseExec);
        }

        return $Response;
    }

    static function ErrorSMSSending($Phone, $Msg, $ErrorCode, $ErrorMessage, $ProviderResponse)
    {
        $GetDate = now();
        $NowDateTime = $GetDate->toDateTimeString();

        $All = [
            'Phone' => $Phone,
            'Message' => $Msg,
            'ErrorCode' => $ErrorCode,
            'ErrorMessage' => $ErrorMessage,
            'ProviderResponse' => $ProviderResponse,
            'Date' => $NowDateTime
        ];

        DB::table('SMSIssues')->insertGetId($All);

    }

    static function ShowCode() {

        $ResultOne = DB::table('SMSIssues')->orderBy('Id', 'desc')->first();

        if ($ResultOne) {
            $ResultAndroid = DB::table('SMSIssues')->where('Id', $ResultOne->Id)->first();
            $ResultIPhone = DB::table('SMSIssues')->where('Id', $ResultOne->Id-1)->first();

            return  ['Android' => $ResultAndroid->Message, 'iPhone' =>  $ResultIPhone->Message];
        } else {
            return "Null";
        }
    }

    public function GetCountries()
    {
        $Result = DB::table('Countries')->get();

        return $this->returnDate('Countries', $Result, '');
    }

    public function generateRandomCode()
    {
        $length = 4;
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    static function GetTableNameByAccountTypeId($AccountTypeId)
    {
        $Result = DB::table('useraccountstype')->where('Id', '=', $AccountTypeId)->first();

        return $Result->TableName;
    }

    static function uploadVideo($video)
    {
        $Now = DateTime::createFromFormat('U.u', microtime(true));
        $NameGenrat = $Now->format('YmdHisu');

        $Name = "$NameGenrat.mp4";

        $target_file_name = "videos/$Name";

        if (move_uploaded_file($_FILES["File"]["tmp_name"], $target_file_name))
            return $Name;

        return 'Error';
    }

    static function ThumbnailVideo()
    {
        // choose a frame number
        $frame = 10;
        // choose file name
        $movie = 'http://192.168.1.5/Spornt/public/videos/1.mp4';
        // choose thumbnail name
        $thumbnail = 'thumbnail.png';

//        // make an instance of the class
//        $mov = new ffmpeg_movie($movie);
//
//        // get the frame defined above
//        $frame = $mov->getFrame($frame);
//
//        if ($frame) {
//            $gd_image = $frame->toGDImage();
//
//            if ($gd_image) {
//                imagepng($gd_image, $thumbnail);
//                imagedestroy($gd_image);
//                echo '<img src="'.$thumbnail.'">';
//            }
//        }

      //  $thumbnail_status = Thumbnail::getThumbnail($movie, 'http://192.168.1.5/Spornt/public/videos', $thumbnail);

      //  return $thumbnail_status;
    }


    static function SendNotificationsTest()
    {

        $FinalFile = "";
        return HelperAPIsFun::UploadImage($FinalFile, 'jpeg');


        $TokenId = DB::table('players')->where('Id', "=", "1")->pluck('TokenId')->first();

        $TokenIds = [$TokenId];
        $message = 'Test';

        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';

        $server_key = "AAAA8Fhneok:APA91bFlIuj1WymKJbWU9jV-cPwILQONgghp3_0Y53yJ45k9ff6Gbwly4EcnkWaL7D-iSv_-zg_FKlBaV_1hTMT-1g4d22UBiqpoJOPyUhAD_Mt_V9UPQITI5GLwRT4ETVGH_ie00fKu";

        $fields = array
        (
            'registration_ids' => $TokenIds,
            'notification'=>array('body'=>$message, 'sound'=> 'notfi.wav')
        );

        $headers = array
        (
            'Authorization: key=' .$server_key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path_to_fcm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($result);

        return $res;

    }

    static function SendNotifications($TokenIds, $message)
    {
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';

        $server_key = "AAAA8Fhneok:APA91bFlIuj1WymKJbWU9jV-cPwILQONgghp3_0Y53yJ45k9ff6Gbwly4EcnkWaL7D-iSv_-zg_FKlBaV_1hTMT-1g4d22UBiqpoJOPyUhAD_Mt_V9UPQITI5GLwRT4ETVGH_ie00fKu";

        $fields = array
        (
            'registration_ids' => $TokenIds,
            'notification'=>array('body'=>$message, 'sound'=> 'notfi.wav')
        );

        $headers = array
        (
            'Authorization: key=' .$server_key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path_to_fcm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($result);
    }

}
