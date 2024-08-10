<?php

namespace App\Events;

use Illuminate\Auth\Events\Login;

class UserLogin extends Login
{

    /**
     * Create a new event instance.
     */
    public $userId;

    public function __construct()
    {
        parent::__construct($user);

        $this->userId = $user->id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
}
