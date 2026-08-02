<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;

#[Signature('app:backup {--keep-days=14 : Number of days of backups to retain}')]
#[Description('Dump the database and archive uploaded storage into storage/app/backups')]
class BackupCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $timestamp = now()->format('Y-m-d_His');
        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir);

        $dumpPath = $this->dumpDatabase($backupDir, $timestamp);

        if ($dumpPath === null) {
            return self::FAILURE;
        }

        $archivePath = "{$backupDir}/backup_{$timestamp}.zip";
        $this->archive($archivePath, $dumpPath);

        File::delete($dumpPath);

        $this->prune($backupDir, (int) $this->option('keep-days'));

        $this->info("Backup created: {$archivePath}");

        return self::SUCCESS;
    }

    protected function dumpDatabase(string $backupDir, string $timestamp): ?string
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        $dumpPath = "{$backupDir}/db_{$timestamp}.sql";

        if ($connection === 'sqlite') {
            File::copy($config['database'], $dumpPath);

            return $dumpPath;
        }

        if ($connection === 'mysql') {
            $process = new Process([
                'mysqldump',
                '-h', $config['host'],
                '-P', (string) $config['port'],
                '-u', $config['username'],
                '--password='.$config['password'],
                $config['database'],
            ]);

            $process->setTimeout(600);
            $process->run();

            if (! $process->isSuccessful()) {
                $this->error('mysqldump failed: '.$process->getErrorOutput());

                return null;
            }

            File::put($dumpPath, $process->getOutput());

            return $dumpPath;
        }

        $this->error("Unsupported database connection for backup: {$connection}");

        return null;
    }

    protected function archive(string $archivePath, string $dumpPath): void
    {
        $zip = new ZipArchive;
        $zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($dumpPath, basename($dumpPath));

        foreach (['books', 'audio'] as $disk) {
            $path = storage_path("app/{$disk}");

            if (! is_dir($path)) {
                continue;
            }

            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
                $relative = $disk.'/'.substr($file->getPathname(), strlen($path) + 1);
                $zip->addFile($file->getPathname(), $relative);
            }
        }

        $zip->close();
    }

    protected function prune(string $backupDir, int $keepDays): void
    {
        $cutoff = now()->subDays($keepDays);

        foreach (glob("{$backupDir}/backup_*.zip") as $file) {
            if (File::lastModified($file) < $cutoff->timestamp) {
                File::delete($file);
            }
        }
    }
}
