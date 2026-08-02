<?php

namespace Tests\Feature\Filament;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class LivewireUploadLimitTest extends TestCase
{
    public function test_livewire_temporary_upload_accepts_files_larger_than_the_default_12mb_limit(): void
    {
        Storage::fake('tmp-for-tests');

        $signedUrl = URL::temporarySignedRoute('livewire.upload-file', now()->addMinutes(5));

        // 20MB — comfortably past Livewire's stock 12MB default, well under our 500MB cap.
        $audio = UploadedFile::fake()->create('long-chapter.mp3', 20 * 1024, 'audio/mpeg');

        $response = $this->post($signedUrl, ['files' => [$audio]]);

        $response->assertOk();
        $response->assertJsonStructure(['paths']);
    }
}
