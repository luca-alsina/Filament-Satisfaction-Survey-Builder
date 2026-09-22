<?php

return [
    'livewire' => [
        'filament-form' => [
            'show' => [
                'submit' => __('Submit'),
                'template_message' => __('This form is a template and cannot be filled out.'),
                'already_submitted_title' => __('You have already filled out this form.'),
                'already_submitted_message' => __('You cannot fill out this form a second time. You can view your answers using the button below.'),
                'view_answers' => __('View my answers'),
            ],
        ],
        'filament-form-user' => [
            'show' => [
                'title' => __('Form Submission'),
                'fields' => [
                    'name' => __('Name'),
                    'form_name' => __('Form Name'),
                    'form_completed_at' => __('Form Completed At'),
                    'user_added_at' => __('User Added At'),
                    'uploaded_files' => __('Uploaded Files'),
                    'question' => __('Question'),
                    'file_name' => __('File Name'),
                    'answer' => __('Answer'),
                ],
            ],
        ],
    ],
];
