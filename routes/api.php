<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\JobDescriptionTableController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\PublicEntityController;
use App\Http\Controllers\ScientificCertificateController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicantJobDescriptionController;
use App\Http\Controllers\ExcelController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


//API Routes

Route::post("login",[ApiController::class,"login"]);


Route::group(["middleware" => ["auth:sanctum"]
], function(){

    Route::get("profile",[ApiController::class,"profile"]);
    Route::post("register",[ApiController::class,"register"]);

    Route::post('/job-description-table', [JobDescriptionTableController::class, 'index']);
Route::post('/create-job-description-table', [JobDescriptionTableController::class, 'store']);
Route::put('/job-description/{jobId}', [JobDescriptionTableController::class, 'update']);
Route::get('job-description-table-show/{id}', [JobDescriptionTableController::class, 'show']);
Route::post('job-description-table-delete', [JobDescriptionTableController::class, 'destroy']);
Route::delete('job-description-table-delete/{id}', [JobDescriptionTableController::class, 'destroySolo']);


Route::post('/sort-applicants', [JobDescriptionTableController::class, 'acceptApplicants']);
Route::post('/revert-applicants', [JobDescriptionTableController::class, 'revertAcceptedApplicants']);

// Route::resource('administrative-divisions', AdministrativeDivisionController::class);

// Governorate API`s
Route::post('/create-governorates', [GovernorateController::class, 'create']);
Route::get('/get-governorates', [GovernorateController::class, 'getAll']);
Route::get('/governorates-get/{governorateId}', [GovernorateController::class, 'getById']);
Route::put('/update-governorates/{governorate_id}', [GovernorateController::class, 'update']);
Route::delete('/delete-governorates/{governorateId}', [GovernorateController::class, 'delete']);

// Public Entitie API`s
Route::post('/create-public-entities', [PublicEntityController::class, 'create']);
Route::get('/get-public-entities', [PublicEntityController::class, 'getAll']);
Route::get('/public-entities-get/{publicEntityId}', [PublicEntityController::class, 'getById']);
Route::put('/update-public-entities/{publicEntityId}/{subEntityId?}', [PublicEntityController::class, 'update']);

Route::delete('/delete-public-entities/{publicEntityId}', [PublicEntityController::class, 'deletePublicEntity']);
Route::delete('/affiliated-entities', [PublicEntityController::class, 'deleteAffiliatedEntities']);
Route::delete('/sub-affiliated-entities', [PublicEntityController::class, 'deleteSubAffiliatedEntities']);

//Scientific Certificate API`s

Route::post('/create-scientific-certificate', [ScientificCertificateController::class, 'create']);
Route::get('/get-scientific-certificate', [ScientificCertificateController::class, 'getAll']);
Route::get('/scientific-certificate-get/{generalId}', [ScientificCertificateController::class, 'getById']);
Route::put('/update-scientific-certificate/{generalId}', [ScientificCertificateController::class, 'update']);
Route::delete('/delete-scientific-certificate/{generalId}', [ScientificCertificateController::class, 'delete']);


Route::post('/applicants', [ApplicantController::class, 'index']);
Route::post('/create-applicants', [ApplicantController::class, 'store']);
Route::get('/get-applicants', [ApplicantController::class, 'getAll']);
Route::delete('delete-applicants/{id}', [ApplicantController::class, 'destroy']);
Route::get('/get-applicants-id/{id}', [ApplicantController::class, 'show']);
Route::put('/applicants/{applicantId}', [ApplicantController::class, 'update']);
Route::post('delete-applicants', [ApplicantController::class, 'deleteApplicants']);

Route::get('/job-descriptions-with-applicants', [JobDescriptionTableController::class, 'getJobDescriptionsWithApplicants']);
Route::get("logout",[ApiController::class,"logout"]);

Route::get("export-governorate",[ExcelController::class,"exportGovernorateData"]);
Route::get("export-job-description",[ExcelController::class,"exportJobDescriptionData"]);
Route::get("export-job-description-headers",[ExcelController::class,"exportJobDescriptionHeaders"]);
Route::get("export-applicants",[ExcelController::class,"exportApplicantsData"]);
Route::get("export-applicants-headers",[ExcelController::class,"exportApplicantsHeaders"]);

Route::post('import-job-description-data', [ExcelController::class, 'importJobDescriptionData']);
Route::post('import-applicants-data', [ExcelController::class, 'importApplicantsData']);

Route::put('permissions/{userID}',[ApiController::class,'updatePermissions']);
Route::get('users',[ApiController::class,'getUsers']);

Route::delete('users/{userID}', [ApiController::class, 'deleteUser']);
Route::put('/updateCanActive/{userId}', [ApiController::class, 'updateCanActive']);

Route::get('user/{id}', [ApiController::class, 'getUserById']);

});