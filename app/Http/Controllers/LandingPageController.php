<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function show()
    {
        $Clinics = DB::table('physiotherapyclinics')->count();
        $Media = DB::table('attachments')->count();
        $Exercises = DB::table('exercises')->count();
        $Users = 0;

        $Users += DB::table('players')->count();
        $Users += DB::table('coaches')->count();
        $Users += DB::table('referees')->count();
        $Users += DB::table('commentators')->count();
        $Users += DB::table('photographers')->count();
        $Users += DB::table('scoutsofclubs')->count();
        $Users += DB::table('supervisors')->count();
        $Users += DB::table('organizers')->count();
        $Users += DB::table('LeagueOrganizers')->count();

        return view('landingPageEn', ['Clinics' => $Clinics, 'Media' => $Media, 'Exercises' => $Exercises, 'Users' => $Users]);
    }

    public function showAr() {

        $Clinics = DB::table('physiotherapyclinics')->count();
        $Media = DB::table('attachments')->count();
        $Exercises = DB::table('exercises')->count();
        $Users = 0;

        $Users += DB::table('players')->count();
        $Users += DB::table('coaches')->count();
        $Users += DB::table('referees')->count();
        $Users += DB::table('commentators')->count();
        $Users += DB::table('photographers')->count();
        $Users += DB::table('scoutsofclubs')->count();
        $Users += DB::table('supervisors')->count();
        $Users += DB::table('organizers')->count();
        $Users += DB::table('LeagueOrganizers')->count();

        return view('landingPage', ['Clinics' => $Clinics, 'Media' => $Media, 'Exercises' => $Exercises, 'Users' => $Users]);
    }
}
