<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'failed_at' => 'datetime',
        ];
    }

    public function jobName(): string
    {
        $payload = json_decode($this->payload, true);

        return $payload['displayName'] ?? 'Unknown';
    }
}
