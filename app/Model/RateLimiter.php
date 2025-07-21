<?php
declare(strict_types=1);

namespace App\Model;


use Nette\Caching\Cache;
use Nette\Caching\Storage;

class RateLimiter
{
    /**
     * Default limity 60/60
     */
    private const DEFAULT_LIMIT = 60;   // počet požadavků
    private const DEFAULT_WINDOW = 60;  // časové okno 60s

    /**
     * Cache pro uložení počítadla
     */
    private Cache $cache;

    /**
     * Konstruktor
     */
    public function __construct(Storage $storage)
    {
        $this->cache = new Cache($storage, 'ratelimit');
    }

    /**
     * Kontrola zda požadavky nepřekročili limit
     *
     * @param string $identifier    Identifikátor uživatele
     * @param int $limit            Max počet požadavků(60)
     * @param int $window           Časové okno (60s)
     * @return bool                 Povolíme požadvek True, Když překročil - False
     */
    public function check(string $identifier, int $limit = self::DEFAULT_LIMIT, int $window = self::DEFAULT_WINDOW): bool
    {
        // Vytvoření klíče pro cache
        $cacheKey = 'ratelimit_' . md5($identifier);

        // Zkus načíst záznam z cache
        $record = $this->cache->load($cacheKey);

        $now = time();

        // Pokud nemá záznam, vytvoří nový
        if ($record === null) {
            $record = [
                'count' => 1,
                'reset_time' => $now + $window,
                'window_start' => $now,
            ];

            $this->cache->save($cacheKey, $record, [
                Cache::Expire => $window,
            ]);

            return true; // Prvotní požadavek, projde vždy
        }

        // Při uběhnutí window (60S) - reset počítadla
        if ($now >= $record['reset_time']) {    // pokud je TED větší nebo roven času TEĎ + WINDOW
            $record = [
                'count' => 1,
                'reset_time' => $now + $window,
                'window_start' => $now,
            ];

            $this->cache->save($cacheKey, $record, [
                Cache::Expire => $window,
            ]);

            return true;
        }

        // Kontrola překročení limitu
        if ($record['count'] >= $limit) {
            return false; // překročil limit
        }

        // Inkrementace počítadla
        $record['count']++;
        $this->cache->save($cacheKey, $record, [
            Cache::Expire => $window,
        ]);

        return true;
    }

    public function getInfo(string $identifier, int $limit = self::DEFAULT_LIMIT, int $window = self::DEFAULT_WINDOW): array
    {
        // Klíč pro cache
        $cacheKey = 'ratelimit_' . md5($identifier);

        // Zkusí načíst záznam cache
        $record = $this->cache->load($cacheKey);

        $now = time();

        // Když záznam neexistuje, nebo když vypršel, vytvoříme defaultní info
        if ($record === null || $now >= $record['reset_time']) {
            return [
                'count' => $limit,
                'remaining' => $limit - 1,  // Tento požadavek odečteme
                'reset' => $now + $window,  // dáme mu zase 60s
            ];
        }

        // Vrátí info o aktuálním stavu
        return [
            'limit' => $limit,
            'remaining' => max(0, $limit - $record['count']),
            'reset' => $record['reset_time'],
        ];
    }
}