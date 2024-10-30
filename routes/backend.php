<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ModuleMakerController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\Parsonal_InfoController;
use App\Http\Controllers\Backend\SkillsController;
use App\Http\Controllers\Backend\AboutController;
//don't remove this comment from route namespace

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

Route::get('/', [LoginController::class, 'loginPage'])->name('home')->middleware('AuthCheck');

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');
    Artisan::call('storage:link');
    Artisan::call('optimize');
    session()->flash('message', 'System Updated Successfully.');

    return redirect()->route('home');
});

Route::group(['as' => 'auth.'], function () {
    Route::get('/login', [LoginController::class, 'loginPage'])->name('login2')->middleware('AuthCheck');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::group(['middleware' => 'AdminAuth'], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::match(['get', 'post'], '/module-make', [ModuleMakerController::class, 'index'])->name('moduleMaker');

    Route::resource('admin', AdminController::class);
    Route::get('admin/{id}/status/{status}/change', [AdminController::class, 'changeStatus'])->name('admin.status.change');

    // for role
    Route::resource('role', RoleController::class);

    // for permission entry
    Route::resource('permission', PermissionController::class);


	// for Contact
	Route::resource('contact', ContactController::class);
	Route::get('contact/{id}/status/{status}/change', [ContactController::class, 'changeStatus'])->name('contact.status.change');

	// for Parsonal_Info
	Route::resource('parsonal_info', Parsonal_InfoController::class);
	Route::get('parsonal_info/{id}/status/{status}/change', [Parsonal_InfoController::class, 'changeStatus'])->name('parsonal_info.status.change');

	// for Skills
	Route::resource('skills', SkillsController::class);
	Route::get('skills/{id}/status/{status}/change', [SkillsController::class, 'changeStatus'])->name('skills.status.change');
    
	// for About
	Route::resource('about', AboutController::class);
	Route::get('about/{id}/status/{status}/change', [AboutController::class, 'changeStatus'])->name('about.status.change');

	//don't remove this comment from route body
});