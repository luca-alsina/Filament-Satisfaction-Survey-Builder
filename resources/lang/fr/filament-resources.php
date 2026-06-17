<?php

return [
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
            ],
            'copy_message' => __('Lien du formulaire copié dans le presse-papier'),
        ],

        'actions' => [
            'preview' => __('Aperçu'),
            'regenerate_average' => __('Regénérer les moyennes'),
            'regenerate_average_in_progress' => __('Les moyennes sont en cours de régénération ! Cela peut prendre quelques minutes...'),
            'copy' => __('Copier'),
            'copy_success_title' => __('Formulaire copié avec succès'),
            'copy_success_body' => __('Veuillez changer le nom du formulaire en quelque chose d\'unique et supprimez le suffixe "(Copy)"'),
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
            'singular' => __('Groupe'),
            'plural' => __('Groupes')
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
];
