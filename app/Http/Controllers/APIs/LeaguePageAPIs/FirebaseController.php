<?php

namespace App\Http\Controllers\APIs\LeaguePageAPIs;

use App\Http\Controllers\APIs\Enums\FirebaseKeys;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Exception\DatabaseException;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }

    public function index()
    {
        $Firebase = (new Factory)
            ->withServiceAccount(__DIR__.'/sporent-6a447-firebase-adminsdk-mu5ch-86f69cddcc.json')
            ->withDatabaseUri('https://sporent-6a447-default-rtdb.firebaseio.com/');

        $database = $Firebase->createDatabase();
        $data = $database->getReference('League');

        return $data->getValue();
    }

    static function CheckLeague($LeagueId) {
        $LeaguePage = DB::table('leaguepage')->where('Id', '=', 1)->first();
        if ($LeaguePage) {
            $CurrentLeagueId = $LeaguePage->LeagueId;
            if ($LeagueId == $CurrentLeagueId) {
                return true;
            }
        }
        return false;
    }

    static function UpdateFirebase($LeagueId, $LeagueTopicAndTeams, $LeagueChampionship, $CurrentMatch, $UpcomingMatch, $OtherMatches, $LeagueSponsors, $LeagueVideo, $TheNews) {

        // FirebaseController::UpdateFirebase($LeagueId, $LeagueTopicAndTeams, $LeagueChampionship, $CurrentMatch, $UpcomingMatch, $OtherMatches, $LeagueSponsors, $LeagueVideo, $TheNews);

        if (FirebaseController::CheckLeague($LeagueId) == true) {

            $Firebase = (new Factory)
                ->withServiceAccount(__DIR__.'/sporent-6a447-firebase-adminsdk-mu5ch-86f69cddcc.json')
                ->withDatabaseUri('https://sporent-6a447-default-rtdb.firebaseio.com/');

            $database = $Firebase->createDatabase();

            $GetDateAndTime = date('Y-m-d h:i:s');

            $updateData = [];

            if ($LeagueTopicAndTeams == true) {
                $updateData += [
                    FirebaseKeys::LeagueTopicAndTeams => $GetDateAndTime
                ];
            }

            if ($LeagueChampionship == true) {
                $updateData += [
                    FirebaseKeys::LeagueChampionship => $GetDateAndTime
                ];
            }

            if ($CurrentMatch == true) {
                $updateData += [
                    FirebaseKeys::CurrentMatch => $GetDateAndTime
                ];
            }

            if ($UpcomingMatch == true) {
                $updateData += [
                    FirebaseKeys::UpcomingMatch => $GetDateAndTime
                ];
            }

            if ($OtherMatches == true) {
                $updateData += [
                    FirebaseKeys::OtherMatches => $GetDateAndTime
                ];
            }

            if ($LeagueSponsors == true) {
                $updateData += [
                    FirebaseKeys::LeagueSponsors => $GetDateAndTime
                ];
            }

            if ($LeagueVideo == true) {
                $updateData += [
                    FirebaseKeys::LeagueVideo => $GetDateAndTime
                ];
            }

            if ($TheNews == true) {
                $updateData += [
                    FirebaseKeys::TheNews => $GetDateAndTime
                ];
            }

            if ($updateData != null) {
                try {
                    $database->getReference('League')->update($updateData);
                } catch (DatabaseException $e) {
                    return $e;
                }
                return 'Updated';
            } else {
                return 'Not updated';
            }
        }
    }

    public function getDate() {

        $GetDateAndTime = date('Y-m-d h:i:s');

        return ['Like' => '2024-03-13 22:39:44', 'DateAndTime' => $GetDateAndTime];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
