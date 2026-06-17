<?php

declare(strict_types=1);

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormUser;

class ShowEntry extends Page
{
    public SurveyFormUser $entry;

    protected static ?string $navigationLabel = null;

    protected static bool $shouldRegisterNavigation = false;

    public function getView(): string
    {
        // If authenticated and using app panel, use app panel view
        // Otherwise, use guest panel view
        if (auth()->check() && $this->getPanel()->getId() === config('filament-satisfaction-survey-builder.app-panel-id', 'app')) {
            return 'filament-satisfaction-survey-builder::pages.show-entry-app';
        }

        return 'filament-satisfaction-survey-builder::pages.show-entry-guest';
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        return 'filament-form-users.show';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getPanel(): Panel
    {
        // Use the current panel set by middleware (app for authenticated, guest for unauthenticated)
        $guestPanelId = config('filament-satisfaction-survey-builder.guest-panel-id', 'guest');
        $appPanelId = config('filament-satisfaction-survey-builder.app-panel-id', 'app');

        $currentPanel = Filament::getCurrentPanel();

        if ($currentPanel) {
            return $currentPanel;
        }

        // Fallback to guest panel if no current panel
        $guestPanel = Filament::getPanel($guestPanelId);

        if ($guestPanel) {
            return $guestPanel;
        }

        // Fallback to app panel if guest panel doesn't exist
        $appPanel = Filament::getPanel($appPanelId);

        if ($appPanel) {
            return $appPanel;
        }

        // If no panels are configured, throw a meaningful exception
        throw new \RuntimeException('No Filament panels are configured for satisfaction survey builder.');
    }

    public function mount(SurveyFormUser $entry): void
    {
        $loginRoute = config('filament-satisfaction-survey-builder.login-route', 'filament.app.auth.login');

        // For guest entries, require a valid signed URL
        if ($entry->user_id === null) {
            if (!request()->hasValidSignature()) {
                abort(403, 'This link has expired or is invalid.');
            }
        } else {
            // For authenticated user entries: use policy if registered, otherwise only allow submitter
            if (auth()->check()) {
                $user = auth()->user();
                $policy = policy($entry);
                if ($policy && method_exists($policy, 'view')) {
                    if (!$user->can('view', $entry)) {
                        abort(403, 'You do not have permission to view this form submission.');
                    }
                } elseif ($entry->user_id !== $user->id) {
                    abort(403, 'You can only view your own form submissions.');
                }
            } else {
                // Not authenticated and entry has a user - redirect to login
                $this->redirect(route($loginRoute, [
                    'redirect' => request()->fullUrl(),
                ]), navigate: false);

                return;
            }
        }

        $this->entry = $entry->load('user', 'filamentForm');
    }

    public function getTitle(): string
    {
        return 'Form Submission';
    }
}
