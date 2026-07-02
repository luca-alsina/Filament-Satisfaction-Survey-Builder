<?php

return [
    'field-types' => [
        'TEXT' => 'Réponse textuelle courte',
        'TEXTAREA' => 'Réponse textuelle longue',
        'SELECT' => 'Menu déroulant à choix unique',
        'SELECT_MULTIPLE' => 'Menu déroulant à choix multiple',
        'RICH_EDITOR' => 'Éditeur riche',
        'TOGGLE' => 'Interrupteur',
        'CHECKBOX' => 'Case à cocher',
        'CHECKBOX_LIST' => 'Case à cocher à choix multiple',
        'RADIO' => 'Choix multiple réponse unique',
        'DATE_TIME_PICKER' => 'Sélecteur date & heure',
        'DATE_PICKER' => 'Sélecteur de date',
        'TIME_PICKER' => 'Sélecteur d\'heure',
        'MARKDOWN_EDITOR' => 'Éditeur Markdown',
        'COLOR_PICKER' => 'Sélecteur de couleur',
        'FILE_UPLOAD' => 'Téléversement de fichier',
        'REPEATER' => 'Répéteur',
        'HEADING' => 'Titre',
        'STAR_RATING' => 'Note en étoiles',
    ],

    'survey-form-group-fields' => [
        'name' => [
            'singular' => __('Champ'),
            'plural' => __('Champs')
        ],

        'create' => __('Créer un champ'),
        'lock' => __('Verrouiller les champs'),
        'unlock' => __('Dévrouiller les champs'),
        'fields' => [
            'type' => __('Type'),
            'label' => __('Libellé'),
            'options' => __('Options'),
            'hint' => __('Indice'),
            'subheading' => __('Sous-titre'),
            'order' => __('Ordre'),
            'required' => __('Obligatoire'),
            'average' => __('Moyenne'),
            'schema' => __('Champs'),
        ]
    ],

    'survey-form' => [
        'name' => [
            'singular' => __('Formulaire'),
            'plural' => __('Formulaires')
        ],

        'create' => __('Créer un formulaire'),
        'empty_state_heading' => __('Aucun formulaire'),

        'sections' => [
            'notifications' => __('Notifications'),
            'notifications_description' => __('Configurer les notifications par email pour les soumissions de formulaire'),
            'limitations' => __('Limitations'),
            'limitations_description' => __('Configurer les limitations pour les soumissions à ce formulaire'),
            'template' => __('Modèle'),
            'template_description' => __('Configurer ce formulaire comme modèle ou utiliser un modèle existant'),
            'average_data' => __('Moyennes'),
            'average_data_description' => __('Valeurs calculées à partir des réponses soumises'),
            'average_data_empty' => __('Aucune moyenne disponible pour le moment. Utilisez "Regénérer les moyennes" après des soumissions.'),
        ],

        'fields' => [
            'name' => __('Nom'),
            'redirect_url' => __('URL de redirection'),
            'redirect_url_hint' => __('(optionnel) Remplissez ce champ pour fournir une URL de redirection personnalisée après la soumission du formulaire. Utilisez une URL complète incluant "https://" pour rediriger vers un lien externe, sinon l\'URL sera relative au domaine de ce site'),
            'description' => __('Description'),
            'notification_emails' => __('Adresses email de notification'),
            'notification_emails_helper' => __('Entrez les adresses email qui doivent recevoir des notifications lorsque ce formulaire est soumis. Appuyez sur Entrée après chaque email.'),
            'restricted_to_users' => __('Restreint aux utilisateurs'),
            'restricted_to_users_hint' => __('Restrez les entrées aux utilisateurs ajoutés dans la liste "utilisateurs autorisés" liée à ce formulaire.'),
            'private_entries' => __('Entrées privées'),
            'private_entries_hint' => __('Restrez les entrées pour ce formulaire de manière programmatique (par exemple via une politique dans votre application).'),
            'permit_guest_entries' => __('Autoriser les invités'),
            'permit_guest_entries_hint' => __('Permettre aux utilisateurs non enregistrés de soumettre ce formulaire'),
            'is_template' => __('Est un modèle'),
            'is_template_hint' => __('Marquer ce formulaire comme modèle réutilisable'),
            'template_id' => __('Modèle'),
            'template_id_helper' => __('Sélectionnez un modèle pour copier ses champs dans ce formulaire'),
        ],

        'table' => [
            'columns' => [
                'created_at' => __('Créé le'),
                'updated_at' => __('Modifié le'),
                'name' => __('Nom'),
                'form_link' => __('Lien du formulaire'),
                'permit_guest_entries' => __('Invités autorisés'),
                'private_entries' => __('Entrées privées'),
                'locked' => __('Verrouillé'),
                'is_template' => __('Modèle'),
                'template' => __('Modèle source'),
            ],
            'copy_message' => __('Lien du formulaire copié dans le presse-papier'),
        ],

        'actions' => [
            'preview' => __('Aperçu'),
            'documents_group' => __('Documents'),
            'regenerate_average' => __('Regénérer les moyennes'),
            'regenerate_average_in_progress' => __('Les moyennes sont en cours de régénération ! Cela peut prendre quelques minutes...'),
            'download_average_pdf' => __('PDF des moyennes'),
            'download_average_pdf_empty' => __('Aucune donnée de moyenne disponible à exporter.'),
            'download_responses_pdf' => __('PDF des réponses'),
            'copy' => __('Copier'),
            'copy_success_title' => __('Formulaire copié avec succès'),
            'copy_success_body' => __('Veuillez changer le nom du formulaire en quelque chose d\'unique et supprimez le suffixe "(Copy)"'),
            'duplicate_from_template' => __('Dupliquer depuis le modèle'),
            'duplicate_from_template_success' => __('Formulaire créé à partir du modèle avec succès'),
        ],

        'average_data' => [
            'fill_rate' => __('Taux de remplissage'),
            'average_value' => __('Moyenne'),
            'entries' => __('entrées'),
            'valid_entries' => __('entrées valides'),
            'total_entries' => __('entrées totales'),
            'no_option_data' => __('Aucune donnée d\'option disponible.'),
            'unsupported' => __('Format de moyenne non pris en charge.'),
        ],

        'pdf' => [
            'responses' => [
                'total_respondents' => __('Nombre de répondants'),
                'guest' => __('Invité'),
                'submitted_at' => __('Soumis le'),
                'no_answers' => __('Aucune réponse enregistrée.'),
                'no_entries' => __('Aucune entrée disponible pour ce formulaire.'),
            ],
        ],

        'filters' => [
            'is_template' => __('Type'),
            'templates' => __('Modèles'),
            'forms' => __('Formulaires'),
        ],
    ],

    'survey-form-users' => [
        'name' => [
            'singular' => __('Utilisateur'),
            'plural' => __('Utilisateurs')
        ],

        'create' => __('Ajouter un utilisateur'),
        'label' => __('Utilisateurs'),

        'fields' => [
            'user_id' => __('Utilisateur'),
        ],

        'table' => [
            'heading' => __('Utilisateurs'),
            'model_label' => __('Utilisateur'),
            'columns' => [
                'user_name' => __('Nom'),
                'response_exists' => __('Réponse existe'),
                'created_at' => __('Créé le'),
                'updated_at' => __('Modifié le'),
            ],
            'actions' => [
                'clear_response' => __('Effacer la réponse'),
                'clear_response_success' => __('Réponse effacée avec succès'),
                'delete' => __('Supprimer'),
            ],
        ],

        'actions' => [
            'add_user' => __('Ajouter un utilisateur'),
            'export_selected' => __('Exporter la sélection'),
        ],

        'filters' => [
            'guest_entries' => __('Entrées invités'),
            'user_entries' => __('Entrées utilisateurs'),
        ],
    ],

    'survey-form-groups' => [
        'name' => [
            'singular' => __('Groupe de questions'),
            'plural' => __('Groupes de questions')
        ],

        'create' => __('Créer un groupe'),

        'table' => [
            'heading' => __('Groupes'),
            'model_label' => __('Groupe'),
            'columns' => [
                'name' => __('Nom'),
                'order' => __('Ordre'),
            ],
        ],

        'actions' => [
            'create' => __('Créer un groupe'),
        ],
    ],

    'survey-form-group' => [
        'name' => [
            'singular' => __('Groupe de questions'),
            'plural' => __('Groupes de questions')
        ],
    ],

    'views' => [
        'livewire' => [
            'filament-form' => [
                'show' => [
                    'submit' => __('Envoyer'),
                ],
            ],
        ],
    ],
];
