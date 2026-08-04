<?php

namespace App\Console\Commands;

use App\Models\Release;
use Illuminate\Console\Command;

class ReleaseStamp extends Command
{
    protected $signature = 'release:stamp
        {--bump=patch : 版本提升類型: patch, minor, major }
        {--ver= : 直接指定版本號 }';

    protected $description = '建立新的 release token';

    public function handle(): int
    {
        $lastRelease = Release::latest('deployed_at')->first();
        $currentVersion = $lastRelease?->version ?? '1.0.0';

        if ($version = $this->option('ver')) {
            $newVersion = $version;
        } else {
            $newVersion = $this->bumpVersion($currentVersion, $this->option('bump'));
        }

        $token = $this->generateToken($newVersion);

        Release::create([
            'version' => $newVersion,
            'deployed_at' => now(),
            'token' => $token,
            'git_sha' => $this->getGitSha(),
        ]);

        $this->info("Release {$newVersion} created with token: {$token}");

        return Command::SUCCESS;
    }

    protected function bumpVersion(string $version, string $type): string
    {
        $parts = explode('.', $version);
        $major = (int) ($parts[0] ?? 1);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        match ($type) {
            'major' => [$major++, $minor = 0, $patch = 0],
            'minor' => [$minor++, $patch = 0],
            default => $patch++,
        };

        return "{$major}.{$minor}.{$patch}";
    }

    protected function generateToken(string $version): string
    {
        $appKey = config('app.key');
        $seed = $version . '|' . now()->toIso8601String() . '|' . ($this->getGitSha() ?? '');
        $hash = hash_hmac('sha256', $seed, $appKey);

        // 12 位小寫英數，第一位為字母
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $first = $letters[random_int(0, 25)];
        $rest = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 11);

        return $first . $rest;
    }

    protected function getGitSha(): ?string
    {
        try {
            return trim(exec('git log --oneline -1 --format=%H 2>/dev/null') ?? '');
        } catch (\Throwable) {
            return null;
        }
    }
}