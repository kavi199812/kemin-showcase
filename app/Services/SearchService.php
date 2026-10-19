<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    /**
     * Sanitize query string according to security rule 9.
     * Strip `+ - > < ( ) ~ * " @`, trim, max 60 chars.
     */
    public function sanitize(string $term): string
    {
        $term = trim($term);
        $term = mb_substr($term, 0, 60);

        // Strip dangerous/boolean operators
        $stripped = preg_replace('/[+\-><()~*"@]/u', ' ', $term) ?? '';

        // Collapse whitespace
        return trim(preg_replace('/\s+/u', ' ', $stripped) ?? '');
    }

    /**
     * Split term into tokens.
     *
     * @return list<string>
     */
    public function tokenize(string $term): array
    {
        $clean = $this->sanitize($term);
        if ($clean === '') {
            return [];
        }

        $parts = explode(' ', $clean);

        return array_values(array_filter($parts, fn ($p) => $p !== ''));
    }

    /**
     * Escape LIKE wildcard characters `%`, `_`, and `\`.
     */
    public function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /**
     * Apply search on Product query.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<T>  $query
     * @return Builder<T>
     */
    public function applyProductSearch(Builder $query, string $term): Builder
    {
        $rawTrimmed = trim($term);
        if ($rawTrimmed === '') {
            return $query;
        }

        $tokens = $this->tokenize($rawTrimmed);
        if (empty($tokens)) {
            return $query;
        }

        $escapedRaw = $this->escapeLike($rawTrimmed);

        // Determine if all tokens are 3+ characters suitable for MySQL FULLTEXT boolean mode
        $canUseFulltext = true;
        $booleanTokens = [];
        foreach ($tokens as $token) {
            if (mb_strlen($token) < 3) {
                $canUseFulltext = false;
                break;
            }
            $booleanTokens[] = '+'.$token.'*';
        }

        if ($canUseFulltext) {
            $booleanString = implode(' ', $booleanTokens);

            return $query->where(function (Builder $sub) use ($booleanString, $tokens, $escapedRaw) {
                $sub->where('job_number', 'LIKE', "%{$escapedRaw}%")
                    ->orWhereRaw('MATCH(name, short_description) AGAINST(? IN BOOLEAN MODE)', [$booleanString])
                    ->orWhere(function (Builder $likeSub) use ($tokens) {
                        // Fallback within grouped conditions for partial substring matches
                        foreach ($tokens as $token) {
                            $escaped = $this->escapeLike($token);
                            $likeSub->where(function (Builder $colSub) use ($escaped) {
                                $colSub->where('name', 'LIKE', "%{$escaped}%")
                                    ->orWhere('short_description', 'LIKE', "%{$escaped}%")
                                    ->orWhere('job_number', 'LIKE', "%{$escaped}%")
                                    ->orWhereHas('companies', function (Builder $cq) use ($escaped) {
                                        $cq->where('name', 'LIKE', "%{$escaped}%");
                                    });
                            });
                        }
                    });
            });
        }

        // Fallback to strict parameterized LIKE query
        return $query->where(function (Builder $sub) use ($tokens, $escapedRaw) {
            $sub->where('job_number', 'LIKE', "%{$escapedRaw}%")
                ->orWhere(function (Builder $tokensSub) use ($tokens) {
                    foreach ($tokens as $token) {
                        $escaped = $this->escapeLike($token);
                        $tokensSub->where(function (Builder $colSub) use ($escaped) {
                            $colSub->where('name', 'LIKE', "%{$escaped}%")
                                ->orWhere('short_description', 'LIKE', "%{$escaped}%")
                                ->orWhere('job_number', 'LIKE', "%{$escaped}%")
                                ->orWhereHas('companies', function (Builder $cq) use ($escaped) {
                                    $cq->where('name', 'LIKE', "%{$escaped}%");
                                });
                        });
                    }
                });
        });
    }

    /**
     * Apply search on Company query.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<T>  $query
     * @return Builder<T>
     */
    public function applyCompanySearch(Builder $query, string $term): Builder
    {
        $tokens = $this->tokenize($term);
        if (empty($tokens)) {
            return $query;
        }

        $canUseFulltext = true;
        $booleanTokens = [];
        foreach ($tokens as $token) {
            if (mb_strlen($token) < 3) {
                $canUseFulltext = false;
                break;
            }
            $booleanTokens[] = '+'.$token.'*';
        }

        if ($canUseFulltext) {
            $booleanString = implode(' ', $booleanTokens);

            return $query->where(function (Builder $sub) use ($booleanString, $tokens) {
                $sub->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [$booleanString])
                    ->orWhere(function (Builder $likeSub) use ($tokens) {
                        foreach ($tokens as $token) {
                            $escaped = $this->escapeLike($token);
                            $likeSub->where('name', 'LIKE', "%{$escaped}%");
                        }
                    });
            });
        }

        return $query->where(function (Builder $sub) use ($tokens) {
            foreach ($tokens as $token) {
                $escaped = $this->escapeLike($token);
                $sub->where('name', 'LIKE', "%{$escaped}%");
            }
        });
    }
}
