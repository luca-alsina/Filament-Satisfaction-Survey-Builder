<?php

return [
    'field-types' => [
        'TEXT'              => 'Text Input',
        'TEXTAREA'          => 'Textarea',
        'SELECT'            => 'Select',
        'SELECT_MULTIPLE'   => 'Select Multiple',
        'RICH_EDITOR'       => 'Rich Editor',
        'TOGGLE'            => 'Toggle',
        'CHECKBOX'          => 'Checkbox',
        'CHECKBOX_LIST'     => 'Checkbox List',
        'RADIO'             => 'Radio',
        'DATE_TIME_PICKER'  => 'DateTime Picker',
        'DATE_PICKER'       => 'Date Picker',
        'TIME_PICKER'       => 'Time Picker',
        'MARKDOWN_EDITOR'   => 'Markdown Editor',
        'COLOR_PICKER'      => 'Color Picker',
        'FILE_UPLOAD'       => 'File Upload',
        'REPEATER'          => 'Repeater',
        'HEADING'           => 'Heading',
        'STAR_RATING'       => 'Star Rating',
    ],

    'survey-form-group-fields' => [
        'name' => [
            'singular' => __('Field'),
            'plural' => __('Fields')
        ],

        'create' => __('Create Field'),
        'lock' => __('Lock Fields'),
        'unlock' => __('Unlock Fields'),
        'fields' => [
            'type' => __('Type'),
            'label' => __('Label'),
            'options' => __('Options'),
            'hint' => __('Hint'),
            'subheading' => __('Subheading'),
            'order' => __('Order'),
            'required' => __('Required'),
            'average' => __('Average'),
            'schema' => __('Fields'),
        ]
    ],

    'survey-form' => [
        'name' => [
            'singular' => __('Form'),
            'plural' => __('Forms')
        ],

        'create' => __('Create Form'),
        'empty_state_heading' => __('No Forms'),

        'sections' => [
            'notifications' => __('Notifications'),
            'notifications_description' => __('Configure email notifications for form submissions'),
            'limitations' => __('Limitations'),
            'limitations_description' => __('Configure limitations for submissions to this form'),
            'average_data' => __('Averages'),
            'average_data_description' => __('Calculated values generated from submitted entries'),
            'average_data_empty' => __('No averages available yet. Use "Regenerate Averages" after receiving submissions.'),
        ],

        'fields' => [
            'name' => __('Name'),
            'redirect_url' => __('Redirect URL'),
            'redirect_url_hint' => __('/(optional) complete this field to provide a custom redirect url on form completion. Use a fully qualified URL including "https://" to redirect to an external link, otherwise url will be relative to this sites domain'),
            'description' => __('Description'),
            'notification_emails' => __('Notification Email Addresses'),
            'notification_emails_helper' => __('Enter email addresses that should receive notifications when this form is submitted. Press Enter after each email.'),
            'restricted_to_users' => __('Restricted to Users'),
            'restricted_to_users_hint' => __('Restrict entries to users added in "allowed users" list linked to this form.'),
            'private_entries' => __('Private Entries'),
            'private_entries_hint' => __('Restrict entries for this form programmatically (e.g. via a gate in your application).'),
            'permit_guest_entries' => __('Permit Guest Entries'),
            'permit_guest_entries_hint' => __('Permit non registered users to submit this form'),
        ],

        'table' => [
            'columns' => [
                'created_at' => __('Created At'),
                'updated_at' => __('Updated At'),
                'name' => __('Name'),
                'form_link' => __('Form Link'),
                'permit_guest_entries' => __('Guest Entries Allowed'),
                'private_entries' => __('Private Entries'),
                'locked' => __('Locked'),
            ],
            'copy_message' => __('Form link copied to clipboard'),
        ],

        'actions' => [
            'preview' => __('Preview'),
            'documents_group' => __('Documents'),
            'regenerate_average' => __('Regenerate Averages'),
            'regenerate_average_in_progress' => __('Averages are being regenerated! This may take a few minutes...'),
            'download_average_pdf' => __('Averages PDF'),
            'download_average_pdf_empty' => __('No average data available to export.'),
            'download_responses_pdf' => __('Responses PDF'),
            'copy' => __('Copy'),
            'copy_success_title' => __('Form copied successfully'),
            'copy_success_body' => __('Please change the name of the form to something unique and remove the "(Copy)" suffix'),
        ],

        'average_data' => [
            'fill_rate' => __('Fill rate'),
            'average_value' => __('Average'),
            'entries' => __('entries'),
            'valid_entries' => __('valid entries'),
            'total_entries' => __('total entries'),
            'no_option_data' => __('No option data available.'),
            'unsupported' => __('Unsupported average format.'),
        ],

        'pdf' => [
            'responses' => [
                'total_respondents' => __('Total respondents'),
                'guest' => __('Guest'),
                'submitted_at' => __('Submitted at'),
                'no_answers' => __('No answers recorded.'),
                'no_entries' => __('No entries available for this form.'),
            ],
        ],
    ],

    'survey-form-users' => [
        'name' => [
            'singular' => __('User'),
            'plural' => __('Users')
        ],

        'create' => __('Add User'),
        'label' => __('Users'),

        'fields' => [
            'user_id' => __('User'),
        ],

        'table' => [
            'heading' => __('Users'),
            'model_label' => __('User'),
            'columns' => [
                'user_name' => __('Name'),
                'response_exists' => __('Response Exists'),
                'created_at' => __('Created At'),
                'updated_at' => __('Updated At'),
            ],
            'actions' => [
                'clear_response' => __('Clear Response'),
                'clear_response_success' => __('Response cleared successfully !'),
                'delete' => __('Delete'),
            ],
        ],

        'actions' => [
            'add_user' => __('Add user'),
            'export_selected' => __('Export Selected'),
        ],

        'filters' => [
            'guest_entries' => __('Guest Entries'),
            'user_entries' => __('User Entries'),
        ],
    ],

    'survey-form-groups' => [
        'name' => [
            'singular' => __('Group'),
            'plural' => __('Groups')
        ],

        'create' => __('Create Group'),

        'table' => [
            'heading' => __('Groups'),
            'model_label' => __('Group'),
            'columns' => [
                'name' => __('Name'),
                'order' => __('Order'),
            ],
        ],

        'actions' => [
            'create' => __('Create Group'),
        ],
    ],

    'survey-form-group' => [
        'name' => [
            'singular' => __('Question Group'),
            'plural' => __('Question Groups')
        ],
    ],
];
