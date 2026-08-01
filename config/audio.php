<?php

return [

    'ffmpeg_path' => env('FFMPEG_PATH', 'ffmpeg'),

    'ffprobe_path' => env('FFPROBE_PATH', 'ffprobe'),

    'chunk_duration' => (int) env('AUDIO_CHUNK_DURATION', 300),

    'whisper' => [
        'binary' => env('WHISPER_BINARY', 'whisper'),
        'model' => env('WHISPER_MODEL', 'base'),
        'language' => env('WHISPER_LANGUAGE', 'auto'),
    ],

    'upload_max_size_kb' => (int) env('UPLOAD_MAX_SIZE_KB', 512000),

    'allowed_document_mimes' => ['pdf', 'docx', 'txt'],

    'allowed_audio_mimes' => ['mp3', 'wav'],

];
