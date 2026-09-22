<?php

declare(strict_types=1);

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;

class ShowForm extends Page
{
    public SurveyForm $form;

    public ?string $token = null;

    protected static ?string $navigationLabel = null;

    protected static bool $shouldRegisterNavigation = false;

    public function getView(): string
    {
        // If authenticated and using app panel, use app panel view
        // Otherwise, use guest panel view
        if (auth()->check() && $this->getPanel()->getId() === config('filament-satisfaction-survey-builder.app-panel-id', 'app')) {
            return 'filament-satisfaction-survey-builder::pages.show-form-app';
        }

        return 'filament-satisfaction-survey-builder::pages.show-form-guest';
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        return 'filament-satisfaction-survey-builder.show';
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
        return Filament::getPanel($guestPanelId);
    }

    public function mount(SurveyForm|string|int $form, ?string $token = null): void
    {
        // Resolve the configured model so custom models extending the base work
        // even when implicit binding returned the base class or a raw key.
        if (! $form instanceof SurveyForm) {
            $form = SurveyForm::configuredQuery()->whereKey($form)->firstOrFail();
        } elseif (get_class($form) !== SurveyForm::configuredClass()) {
            // Re-fetch as the configured class to get custom behavior/accessors.
            $configured = SurveyForm::configuredQuery()->whereKey($form->getKey())->first();
            if ($configured) {
                $form = $configured;
            }
        }
        // Check if token is provided and valid (from route param or query string)
        $isValidToken = false;
        $token = $token ?? request()->route('token') ?? request()->query('token');
        if ($token) {
            $surveyFormUser = $form->getFormUserByToken($token);
            $isValidToken = $surveyFormUser !== null;
        }
        
        // If form doesn't permit guest entries and user is not authenticated, redirect to login
        // Unless a valid token is provided
        if (!auth()->check() && !$form->permit_guest_entries && !$isValidToken) {
            $loginRoute = config('filament-satisfaction-survey-builder.login-route', 'filament.app.auth.login');

            $this->redirect(route($loginRoute, [
                'redirect' => request()->fullUrl(),
            ]), navigate: false);

            return;
        }

        // Show form (middleware sets the appropriate panel based on authentication)
        $this->token = $token;
        $this->form = $form->load('filamentFormGroups', 'filamentFormGroups.filamentFormGroupFields');
    }

    public function getTitle(): string
    {
        return $this->form->name ?? 'Form';
    }
}
