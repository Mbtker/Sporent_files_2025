<?php

use App\Http\Controllers\AdvertisingAgenciesController;
use App\Http\Controllers\APIs\GeneralController;
use App\Http\Controllers\APIs\HelperController;
use App\Http\Controllers\APIs\LeaguePageAPIs\FirebaseController;
use App\Http\Controllers\APIs\PlayerController;
use App\Http\Controllers\APIs\TeamController;
use App\Http\Controllers\APIs\UserController;
use App\Http\Controllers\ChangeLanguageController;
use App\Http\Controllers\CoachesController;
use App\Http\Controllers\CommentatorsController;
use App\Http\Controllers\DeleteUserDataController;
use App\Http\Controllers\ExercisesController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LeaguesController;
use App\Http\Controllers\MatchesController;
use App\Http\Controllers\OrganizersController;
use App\Http\Controllers\PhotographersController;
use App\Http\Controllers\PhysiotherapyClinicsController;
use App\Http\Controllers\PlayersController;
use App\Http\Controllers\RefereesController;
use App\Http\Controllers\ScoutsClubsController;
use App\Http\Controllers\SponsorsController;
use App\Http\Controllers\StadiumsController;
use App\Http\Controllers\StoresController;
use App\Http\Controllers\SupervisorsController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\TransfersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\LeaguePageAPIs\LeaguePageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


define('PAGINATION_COUNT', 10);
define('MAX_DISTANCE_FOR_APP', 100);
define('MAX_DISTANCE_FOR_NEARLY_PLAYERS', 20);
define('MAX_DISTANCE_FOR_CITY_CHAT_GROUP', 80);

Route::get('/TermsOfUse', function () {
    return view('termsOfUse');
})->name('TermsOfUse');

Route::get('/TermsOfUseEn', function () {
    return view('termsOfUseEn');
})->name('TermsOfUseEn');

Route::get('/PrivacyPolicy', function () {
    return view('privacyPolicy');
})->name('PrivacyPolicy');

Route::get('/PrivacyPolicyEn', function () {
    return view('privacyPolicyEn');
})->name('PrivacyPolicyEn');

// Language Routes
Route::get('lang/{lang}', ['as' => 'lang.switch', 'uses' => 'App\Http\Controllers\LanguageController@switchLang']);

Route::get('/',[LandingPageController::class, 'showAr'])->name('LandingAr');
Route::get('/En',[LandingPageController::class, 'show'])->name('LandingEn');

Route::get('/DeleteYourData',[DeleteUserDataController::class, 'showAr'])->name('DeleteYourData');
Route::get('/DeleteYourDataEn',[DeleteUserDataController::class, 'show'])->name('DeleteYourDataEn');
Route::post('sendDeleteUserData', 'App\Http\Controllers\DeleteUserDataController@SendDeleteUserDataRequest') -> name('sendDeleteUserData');

// Supervisors Routes
Route::get('/Supervisors',[SupervisorsController::class, 'show'])->name('Supervisors');
Route::post('/editSupervisor',[SupervisorsController::class, 'editSupervisor'])->name('editSupervisor');
Route::get('/SupervisorsSearching',[SupervisorsController::class, 'Searching'])->name('SupervisorsSearching');
Route::get('/SupervisorsDetails',[SupervisorsController::class, 'Details'])->name('SupervisorsDetails');
Route::get('/GetSupervisorsInfo',[SupervisorsController::class, 'GetSupervisorsInfo'])->name('GetSupervisorsInfo');


// Organizers Routes
Route::get('/Organizers',[OrganizersController::class, 'show'])->name('Organizers');
Route::post('/editOrganizers',[OrganizersController::class, 'editOrganizers'])->name('editOrganizers');
Route::get('/OrganizersSearching',[OrganizersController::class, 'Searching'])->name('OrganizersSearching');
Route::get('/OrganizersDetails',[OrganizersController::class, 'Details'])->name('OrganizersDetails');
Route::get('/GetOrganizersInfo',[OrganizersController::class, 'GetOrganizersInfo'])->name('GetOrganizersInfo');


// Players Routes
Route::get('/Players',[PlayersController::class, 'show'])->name('Players');
Route::post('/editPlayers',[PlayersController::class, 'edit'])->name('editPlayers');
Route::get('/PlayersSearching',[PlayersController::class, 'Searching'])->name('PlayersSearching');
Route::get('/PlayerDetails',[PlayersController::class, 'Details'])->name('PlayerDetails');
Route::get('/GetPlayerInfo',[PlayersController::class, 'GetPlayerInfo'])->name('GetPlayerInfo');

// Teams Routes
Route::get('/Teams',[TeamsController::class, 'show'])->name('Teams');
Route::get('/TeamsSearching',[TeamsController::class, 'Searching'])->name('TeamsSearching');
Route::get('/TeamDetails',[TeamsController::class, 'Details'])->name('TeamDetails');

// Transfers Routes
Route::get('/Transfers',[TransfersController::class, 'show'])->name('Transfers');
Route::get('/TransfersSearching',[TransfersController::class, 'Searching'])->name('TransfersSearching');

// Coaches Routes
Route::get('/Coaches',[CoachesController::class, 'show'])->name('Coaches');
Route::get('/CoachesSearching',[CoachesController::class, 'Searching'])->name('CoachesSearching');
Route::post('/editCoach',[CoachesController::class, 'edit'])->name('editCoach');
Route::get('/CoachDetails',[CoachesController::class, 'Details'])->name('CoachDetails');
Route::get('/GetCoachInfo',[CoachesController::class, 'GetCoachInfo'])->name('GetCoachInfo');

// Referees Routes
Route::get('/Referees',[RefereesController::class, 'show'])->name('Referees');
Route::get('/RefereesSearching',[RefereesController::class, 'Searching'])->name('RefereesSearching');
Route::post('/editReferee',[RefereesController::class, 'edit'])->name('editReferee');
Route::get('/RefereeDetails',[RefereesController::class, 'Details'])->name('RefereeDetails');
Route::get('/GetRefereeInfo',[RefereesController::class, 'GetRefereeInfo'])->name('GetRefereeInfo');

// Stadiums Routes
Route::get('/Stadiums',[StadiumsController::class, 'show'])->name('Stadiums');
Route::get('/StadiumsSearching',[StadiumsController::class, 'Searching'])->name('StadiumsSearching');
Route::post('/editStadium',[StadiumsController::class, 'edit'])->name('editStadium');
Route::get('/StadiumDetails',[StadiumsController::class, 'Details'])->name('StadiumDetails');
Route::get('/GetStadiumInfo',[StadiumsController::class, 'GetStadiumInfo'])->name('GetStadiumInfo');

// Commentators Routes
Route::get('/Commentators',[CommentatorsController::class, 'show'])->name('Commentators');
Route::get('/CommentatorsSearching',[CommentatorsController::class, 'Searching'])->name('CommentatorsSearching');
Route::post('/editCommentators',[CommentatorsController::class, 'edit'])->name('editCommentators');
Route::get('/CommentatorDetails',[CommentatorsController::class, 'Details'])->name('CommentatorDetails');
Route::get('/GetCommentatorInfo',[CommentatorsController::class, 'GetCommentatorInfo'])->name('GetCommentatorInfo');

// Photographers Routes
Route::get('/Photographers',[PhotographersController::class, 'show'])->name('Photographers');
Route::get('/PhotographersSearching',[PhotographersController::class, 'Searching'])->name('PhotographersSearching');
Route::post('/editPhotographers',[PhotographersController::class, 'edit'])->name('editPhotographers');
Route::get('/PhotographerDetails',[PhotographersController::class, 'Details'])->name('PhotographerDetails');
Route::get('/GetPhotographerInfo',[PhotographersController::class, 'GetPhotographerInfo'])->name('GetPhotographerInfo');

// Sponsors Routes
Route::get('/Sponsors',[SponsorsController::class, 'show'])->name('Sponsors');
Route::get('/SponsorsSearching',[SponsorsController::class, 'Searching'])->name('SponsorsSearching');
Route::post('/editSponsors',[SponsorsController::class, 'edit'])->name('editSponsors');
Route::get('/SponsorDetails',[SponsorsController::class, 'Details'])->name('SponsorDetails');
Route::get('/GetSponsorInfo',[SponsorsController::class, 'GetSponsorInfo'])->name('GetSponsorInfo');

// AdvertisingAgencies Routes
Route::get('/AdvertisingAgencies',[AdvertisingAgenciesController::class, 'show'])->name('AdvertisingAgencies');
Route::get('/AdvertisingAgenciesSearching',[AdvertisingAgenciesController::class, 'Searching'])->name('AdvertisingAgenciesSearching');
Route::post('/editAdvertisingAgencies',[AdvertisingAgenciesController::class, 'edit'])->name('editAdvertisingAgencies');
Route::get('/AgencyDetails',[AdvertisingAgenciesController::class, 'Details'])->name('AgencyDetails');
Route::get('/GetAgencyInfo',[AdvertisingAgenciesController::class, 'GetAgencyInfo'])->name('GetAgencyInfo');

// Leagues Routes
Route::get('/Leagues',[LeaguesController::class, 'show'])->name('Leagues');
Route::get('/LeaguesSearching',[LeaguesController::class, 'Searching'])->name('LeaguesSearching');
Route::post('/editLeagues',[LeaguesController::class, 'edit'])->name('editLeagues');
Route::get('/LeagueDetails',[LeaguesController::class, 'Details'])->name('LeagueDetails');
Route::get('/GetLeaguesInfo',[LeaguesController::class, 'GetLeaguesInfo'])->name('GetLeaguesInfo');

// Matches Routes
Route::get('/Matches',[MatchesController::class, 'show'])->name('Matches');
Route::get('/MatchesSearching',[MatchesController::class, 'Searching'])->name('MatchesSearching');
Route::post('/editMatches',[MatchesController::class, 'edit'])->name('editMatches');
Route::get('/MatchDetails',[MatchesController::class, 'Details'])->name('MatchDetails');
Route::get('/GetMatchInfo',[MatchesController::class, 'GetMatchInfo'])->name('GetMatchInfo');

// Exercises Routes
Route::get('/Exercises',[ExercisesController::class, 'show'])->name('Exercises');
Route::get('/ExercisesSearching',[ExercisesController::class, 'Searching'])->name('ExercisesSearching');
Route::post('/editExercises',[ExercisesController::class, 'edit'])->name('editExercises');
Route::get('/ExerciseDetails',[ExercisesController::class, 'Details'])->name('ExerciseDetails');
Route::get('/GetExerciseInfo',[ExercisesController::class, 'GetExerciseInfo'])->name('GetExerciseInfo');

// Stores Routes
Route::get('/Stores',[StoresController::class, 'show'])->name('Stores');
Route::get('/StoresSearching',[StoresController::class, 'Searching'])->name('StoresSearching');
Route::post('/editStores',[StoresController::class, 'edit'])->name('editStores');
Route::get('/StoreDetails',[StoresController::class, 'Details'])->name('StoreDetails');
Route::get('/GetStoreInfo',[StoresController::class, 'GetStoreInfo'])->name('GetStoreInfo');

// ScoutsClubs Routes
Route::get('/ScoutsClubs',[ScoutsClubsController::class, 'show'])->name('ScoutsClubs');
Route::get('/ScoutsClubsSearching',[ScoutsClubsController::class, 'Searching'])->name('ScoutsClubsSearching');
Route::post('/editScoutsClubs',[ScoutsClubsController::class, 'edit'])->name('editScoutsClubs');
Route::get('/ScoutClubsDetails',[ScoutsClubsController::class, 'Details'])->name('ScoutClubsDetails');
Route::get('/GetScoutClubsInfo',[ScoutsClubsController::class, 'GetScoutClubsInfo'])->name('GetScoutClubsInfo');

// PhysiotherapyClinics Routes
Route::get('/PhysiotherapyClinics',[PhysiotherapyClinicsController::class, 'show'])->name('PhysiotherapyClinics');
Route::get('/PhysiotherapyClinicsSearching',[PhysiotherapyClinicsController::class, 'Searching'])->name('PhysiotherapyClinicsSearching');
Route::post('/editPhysiotherapyClinics',[PhysiotherapyClinicsController::class, 'edit'])->name('editPhysiotherapyClinics');
Route::get('/PhysiotherapyClinicDetails',[PhysiotherapyClinicsController::class, 'Details'])->name('PhysiotherapyClinicDetails');
Route::get('/GetPhysiotherapyClinicInfo',[PhysiotherapyClinicsController::class, 'GetPhysiotherapyClinicInfo'])->name('GetPhysiotherapyClinicInfo');

Route::get('/ThumbnailVideo',[HelperController::class, 'ThumbnailVideo'])->name('ThumbnailVideo');
Route::get('/SendNotificationsTest',[HelperController::class, 'SendNotificationsTest'])->name('SendNotificationsTest');

//Route::get('/Statistic', function () {
//    return view('leagueStatisticLogin');
//})->name('StatisticLogin');

Route::get('/StatisticLogin',[LeaguePageController::class, 'StatisticLogin'])->name('StatisticLogin');

Route::get('/leagueStatistic',[LeaguePageController::class, 'leagueStatistic'])->name('leagueStatistic');
Route::get('/leagueQuestionnaire',[LeaguePageController::class, 'leagueQuestionnaire'])->name('leagueQuestionnaire');
Route::get('/sendQuestionnaire',[LeaguePageController::class, 'sendQuestionnaire'])->name('sendQuestionnaire');

Route::get('/CurrentMatchTest', [LeaguePageController::class, 'CurrentMatchTest'])->name('CurrentMatchTest');
Route::get('/LeagueTopicAndTeams', [LeaguePageController::class, 'LeagueTopicAndTeams'])->name('LeagueTopicAndTeams');
Route::get('/LeagueSponsors', [LeaguePageController::class, 'LeagueSponsors'])->name('LeagueSponsors');
Route::get('/LeagueChampionship', [LeaguePageController::class, 'LeagueChampionship'])->name('LeagueChampionship');
Route::get('/CurrentMatch', [LeaguePageController::class, 'CurrentMatch'])->name('CurrentMatch');
Route::get('/UpcomingMatch', [LeaguePageController::class, 'UpcomingMatch'])->name('UpcomingMatch');
Route::get('/OtherMatches', [LeaguePageController::class, 'OtherMatches'])->name('OtherMatches');
Route::get('/LeagueVideo', [LeaguePageController::class, 'LeagueVideo'])->name('LeagueVideo');
Route::get('/TheNews', [LeaguePageController::class, 'TheNews'])->name('TheNews');
Route::get('/CheckForUpdate', [LeaguePageController::class, 'CheckForUpdate'])->name('CheckForUpdate');
Route::get('/clearRoute', [LeaguePageController::class, 'clearRoute'])->name('clearRoute');
Route::get('/IncreaseVisitor', [LeaguePageController::class, 'IncreaseVisitor'])->name('IncreaseVisitor');



Route::get('/GetCurrentMatchIdInLeague', [LeaguePageController::class, 'GetCurrentMatchIdInLeague'])->name('GetCurrentMatchIdInLeague');
