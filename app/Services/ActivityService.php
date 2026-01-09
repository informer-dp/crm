<?php
namespace App\Services;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    public static function log(
        Model $subject,
        string $event,
        ?string $description = null
    ): void {
        Activity::create([
            'subject_type' => get_class($subject),
            'subject_id'   => $subject->id,
            'event'        => $event,
            'description'  => $description,
            'user_id'      => Auth::id(),
        ]);
    }
}
