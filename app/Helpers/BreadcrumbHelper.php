<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BreadcrumbHelper
{
    /**
     * Resolve breadcrumb items for the current request.
     *
     * Priority: explicit override > view()->shared('cmBreadcrumbs') > config map > route-name fallback.
     *
     * @return array<int, array{label: string, url?: string}>
     */
    public static function resolve(?array $override = null): array
    {
        if ($override !== null && $override !== []) {
            return self::normalize($override);
        }

        $shared = view()->shared('cmBreadcrumbs');
        if (is_array($shared) && $shared !== []) {
            return self::normalize($shared);
        }

        $routeName = Route::currentRouteName();
        if (! $routeName) {
            return [];
        }

        $configTrail = config("breadcrumbs.routes.{$routeName}");
        if (is_array($configTrail)) {
            if ($configTrail === []) {
                return [];
            }

            return self::normalize(self::buildFromConfig($configTrail));
        }

        return self::normalize(self::fallbackFromRouteName($routeName));
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array{label: string, url?: string}>
     */
    protected static function buildFromConfig(array $items): array
    {
        $built = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $label = (string) ($item['label'] ?? '');
            $label = self::resolvePlaceholdersInString($label);

            if ($label === '') {
                continue;
            }

            $entry = ['label' => $label];

            if (! empty($item['url'])) {
                $entry['url'] = (string) $item['url'];
            } elseif (! empty($item['route']) && Route::has($item['route'])) {
                $entry['url'] = route($item['route']);
            }

            $built[] = $entry;
        }

        return $built;
    }

    protected static function resolvePlaceholdersInString(string $value): string
    {
        return preg_replace_callback('/\{([a-z_]+)\}/', function (array $matches): string {
            $resolved = self::resolvePlaceholder($matches[1]);

            return $resolved ?? $matches[0];
        }, $value) ?? $value;
    }

    protected static function resolvePlaceholder(string $key): ?string
    {
        $sources = config("breadcrumbs.placeholders.{$key}", []);

        if ($key === 'listing_title') {
            foreach ($sources as $varName) {
                $model = view()->shared($varName);
                if ($model === null) {
                    continue;
                }

                $title = trim(implode(' ', array_filter([
                    $model->year ?? null,
                    $model->make ?? $model->other_make ?? null,
                    $model->model ?? $model->other_model ?? null,
                ])));

                if ($title !== '') {
                    return $title;
                }
            }

            return 'Listing';
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array{label: string, url?: string}>
     */
    public static function normalize(array $items): array
    {
        $normalized = [];
        $count = count($items);

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $entry = ['label' => $label];
            $isLast = $index === $count - 1;

            if (! $isLast) {
                if (! empty($item['url'])) {
                    $entry['url'] = (string) $item['url'];
                } elseif (! empty($item['route']) && Route::has($item['route'])) {
                    $entry['url'] = route($item['route']);
                }
            }

            $normalized[] = $entry;
        }

        if ($normalized !== []) {
            unset($normalized[array_key_last($normalized)]['url']);
        }

        return $normalized;
    }

    /**
     * @return array<int, array{label: string, url?: string}>
     */
    protected static function fallbackFromRouteName(string $routeName): array
    {
        if ($routeName === 'welcome') {
            return [];
        }

        $segments = explode('.', $routeName);
        $label = Str::title(str_replace(['-', '_'], ' ', (string) end($segments)));

        if (count($segments) > 1) {
            $parent = Str::title(str_replace(['-', '_'], ' ', $segments[0]));

            return self::normalize([
                ['label' => 'Home', 'route' => 'welcome'],
                ['label' => $parent, 'url' => url('/')],
                ['label' => $label],
            ]);
        }

        return self::normalize([
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => $label],
        ]);
    }
}
