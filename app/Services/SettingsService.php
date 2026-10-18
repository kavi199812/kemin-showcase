<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected const CACHE_KEY = 'kemin_showcase_settings';

    /**
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'presenter_pin_hash' => null,
        'pin_length' => 4,
        'lockout_max_attempts' => 5,
        'lockout_window_minutes' => 15,
        'lockout_minutes' => 15,
        'global_lockout_threshold' => 30,
        'presenter_session_hours' => 8,
        'presenter_session_version' => 1,
        'hero_line_1' => 'PRECISION',
        'hero_line_2' => 'Meets Craftsmanship',
        'tagline' => 'Packaging & Print Manufacturer, Sri Lanka',
        'stats_json' => '[{"label":"Years of Excellence","value":"15+"},{"label":"Global Clients","value":"120+"},{"label":"Export Destinations","value":"25+"}]',
    ];

    /**
     * Get a setting by key with fallback.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return $all[$key] ?? $default ?? ($this->defaults[$key] ?? null);
    }

    /**
     * Get all settings from cache or database.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $settings = Setting::all()->pluck('value', 'key')->toArray();

            return array_merge($this->defaults, $settings);
        });
    }

    /**
     * Set a setting value.
     */
    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value]
        );

        $this->clearCache();
    }

    /**
     * Set multiple settings.
     *
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : (string) $value]
            );
        }

        $this->clearCache();
    }

    /**
     * Increment presenter session version (e.g. upon PIN change to invalidate sessions).
     */
    public function bumpSessionVersion(): int
    {
        $current = (int) $this->get('presenter_session_version', 1);
        $new = $current + 1;
        $this->set('presenter_session_version', $new);

        return $new;
    }

    /**
     * Clear cached settings.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
