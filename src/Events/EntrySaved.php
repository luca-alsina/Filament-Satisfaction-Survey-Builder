<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Luca\FilamentSatisfactionSurveyBuilder\Models\FilamentSurveyFormUser;

class EntrySaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public FilamentSurveyFormUser $entry
    ) {}
}
