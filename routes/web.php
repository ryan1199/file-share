<?php

use App\Http\Controllers\File\Download;
use App\Http\Controllers\File\Preview;
use App\Livewire\ArchiveBox\Index as ArchiveBoxIndex;
use App\Livewire\ArchiveBox\Show as ArchiveBoxShow;
use App\Livewire\Auth\EmailVerification;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Logout;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\File\Show as FileShow;
use App\Livewire\User\Index;
use App\Livewire\User\Show;
use App\Mail\RequestEmailVerificationSended;
use App\Models\File;
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

Route::get('register', Register::class)->middleware('should.not.login')->name('auth.register');
Route::get('login', Login::class)->middleware('should.not.login')->name('auth.login');
Route::get('logout', Logout::class)->middleware('should.login')->name('auth.logout');
Route::get('reset-password/{token?}', ResetPassword::class)->middleware(['valid.token', 'should.not.login'])->name('auth.reset-password');
Route::get('email-verification/{token?}', EmailVerification::class)->middleware(['valid.token', 'should.not.login'])->name('auth.email-verification');
Route::get('user/{user:slug}', Show::class)->name('user.show');
Route::get('user-list/{search?}', Index::class)->name('user.index');
Route::get('archive-box/{archiveBox:slug}', ArchiveBoxShow::class)->name('archive-box.show');
Route::get('archive-box-list', ArchiveBoxIndex::class)->name('archive-box.index');
Route::get('file/{file:slug}', FileShow::class)->middleware('can.access.file')->name('file.show');
Route::get('file/{file:slug}/download', Download::class)->middleware('can.access.file')->name('file.download');
Route::get('file/{file:slug}/preview', Preview::class)->middleware('can.access.file')->name('file.preview');
Route::get('/mailable/email-verification', function () {
    $user = User::first();
 
    return new RequestEmailVerificationSended($user);
})->name('test.mail.email-verification');
Route::fallback(function () {
    return abort(404);
});
// implement queue for email sending (done)
// create middleware (done)
// create policy (done)
// broadcast for likes, Views, and downloads (done)
// broadcast for user (done)
// custom error page (not satisfied)
// create logs for user and archive box (done)
// log create archive box (done)
// log updated user profile (done)
// log changed user password (done)
// log deleted user account (done)
// log updated user permission in archive box (done)
// log removed user from archive box (done)
// log added user to archive box (done)
// log updated archive box (done)
// log uploaded file in archive box (done)
// log updated file in archive box (done)
// log deleted file in archive box (done)
// add log display in user page and archive box page (done)
// log format user->slug/user->name: activity (done)
// broadcast log (done)
// home page spotlight of archive boxes with the most users joined (not yet)
// about page description the purpose of this website (not yet)