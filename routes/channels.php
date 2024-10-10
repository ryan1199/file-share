<?php

use App\Models\ArchiveBox;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('user.index');
Broadcast::channel('user.show.{user}.archive-box.index', function (User $user) {
    return (User::find($user->id) != null) ? true : false;
});
Broadcast::channel('user.show.{user}.log.index', function (User $user) {
    return (User::find($user->id) != null) ? true : false;
});
Broadcast::channel('archive-box.index');
Broadcast::channel('archive-box.user.create');
Broadcast::channel('archive-box.show.{archiveBox}', function (ArchiveBox $archiveBox) {
    return (ArchiveBox::find($archiveBox->id) != null) ? true : false;
});
Broadcast::channel('archive-box.show.{archiveBox}.file.index', function (ArchiveBox $archiveBox) {
    return (ArchiveBox::find($archiveBox->id) != null) ? true : false;
});
Broadcast::channel('archive-box.show.{archiveBox}.user.index', function (ArchiveBox $archiveBox) {
    return (ArchiveBox::find($archiveBox->id) != null) ? true : false;
});
Broadcast::channel('archive-box.show.{archiveBox}.log.index', function (ArchiveBox $archiveBox) {
    return (ArchiveBox::find($archiveBox->id) != null) ? true : false;
});
Broadcast::channel('archive-box.show.{archiveBox}.user.edit', function (ArchiveBox $archiveBox) {
    return (ArchiveBox::find($archiveBox->id) != null) ? true : false;
});
Broadcast::channel('file.show.{file}', function (File $file) {
    return (File::find($file->id) != null) ? true : false;
});