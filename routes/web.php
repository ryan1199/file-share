<?php

use App\Livewire\ArchiveBox\Index as ArchiveBoxIndex;
use App\Livewire\Auth\EmailVerification;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Logout;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\User\Index;
use App\Livewire\User\Show;
use App\Livewire\Welcome;
use App\Mail\RequestEmailVerificationSended;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', Welcome::class)->name('welcome');
Route::get('register', Register::class)->name('auth.register');
Route::get('login', Login::class)->name('auth.login');
Route::get('logout', Logout::class)->name('auth.logout');
Route::get('reset-password', ResetPassword::class)->name('auth.reset-password');
Route::get('email-verification/{token?}', EmailVerification::class)->name('auth.email-verification');
Route::get('user/{user:slug}', Show::class)->name('user.show');
Route::get('user-list/{search?}', Index::class)->name('user.index');
Route::get('archive-box-list', ArchiveBoxIndex::class)->name('archive-box.index');
Route::get('/mailable/email-verification', function () {
    $user = User::first();
 
    return new RequestEmailVerificationSended($user);
})->name('test.mail.email-verification');
// implement queue for email sending (not ye)