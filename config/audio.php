<?php

return [

    'upload_max_size_kb' => (int) env('UPLOAD_MAX_SIZE_KB', 512000),

    'allowed_document_mimes' => ['pdf', 'docx', 'txt'],

    'allowed_audio_mimes' => ['mp3', 'wav'],

];
