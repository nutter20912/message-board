<?php

namespace App\Listeners;

use App\Events\RelationshipConfirmed;
use App\Http\Controllers\UserRelationshipController;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SyncRelationship implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(public Request $request) {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\RelationshipConfirmed  $event
     * @return void
     */
    public function handle(RelationshipConfirmed $event)
    {
        $user = $event->user;
        $requestId = $event->requestId;
        $confirm = $event->confirm;

        $this->request->setUserResolver(fn () => $user);

        $child = $user->children()
            ->where('child_id', $requestId)
            ->first();

        if ($confirm) {
            return $this->accept($child, $requestId);
        }

        return $this->reject($child);
    }

    /**
     * 同步接受關係
     *
     * @param  \App\Models\User  $child
     * @return void
     */
    public function accept(?User $child, $requestId)
    {
        if ($child) {
            $this->request->replace(['type' => 1]);
            App::make(UserRelationshipController::class)->update($this->request, $child);

            return;
        }

        $this->request->replace(['child_id' => $requestId, 'type' => 1]);
        App::make(UserRelationshipController::class)->store($this->request);
    }

    /**
     * 同步拒絕關係
     *
     * @param  \App\Models\User  $child
     * @return void
     */
    public function reject(?User $child)
    {
        if ($child) {
            App::make(UserRelationshipController::class)->destroy($child);
        }
    }
}
