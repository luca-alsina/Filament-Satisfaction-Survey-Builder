<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormUser;

class EntrySaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public SurveyFormUser $entry
    ) {}
}
