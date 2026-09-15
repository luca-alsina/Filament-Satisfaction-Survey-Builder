<?php

return [
    'livewire' => [
        'filament-form' => [
            'show' => [
                'submit' => __('Soumettre'),
                'template_message' => __('Ce formulaire est un modèle et ne peut pas être rempli.'),
            ],
        ],
        'filament-form-user' => [
            'show' => [
                'title' => __('Réponse au formulaire'),
                'fields' => [
                    'name' => __('Nom'),
                    'form_name' => __('Nom du formulaire'),
                    'form_completed_at' => __('Formulaire complété le'),
                    'user_added_at' => __('Utilisateur ajouté le'),
                    'uploaded_files' => __('Fichiers téléversés'),
                    'question' => __('Question'),
                    'file_name' => __('Nom du fichier'),
                    'answer' => __('Réponse'),
                ],
            ],
        ],
    ],
];
