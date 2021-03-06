<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Exceptions\UnableToFindUrlCredential;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Auth
{
    const COOKIE_NAME = '.DWPLATFORMAUTH';

    const CACHE_KEY = 'docuware.cookies';

    public static function store(CookieJar $cookies): void
    {
        $cookie = collect($cookies->toArray())
            ->reject(fn (array $cookie) => $cookie['Value'] === '')
            ->firstWhere('Name', self::COOKIE_NAME);

        Cache::put(
            self::CACHE_KEY,
            [$cookie['Name'] => $cookie['Value']],
            now()->addMinutes(config('docuware.cookie_lifetime')),
        );
    }

    public static function cookies(): ?array
    {
        return Cache::get(self::CACHE_KEY);
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function domain(): string
    {
        throw_if(
            empty(config('docuware.credentials.url')),
            UnableToFindUrlCredential::create(),
        );

        return Str::of(config('docuware.credentials.url'))
            ->after('//')
            ->beforeLast('/')
            ->__toString();
    }

    public static function check(): bool
    {
        return Cache::has(self::CACHE_KEY);
    }
}
