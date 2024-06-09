<?php

namespace App\Traits;

trait FullTextSearchTrait
{
    protected function fullTextWildcards($term)
    {
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);
        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            if (strlen($word) >= 1) {
                $words[$key] = '+' . $word . '*';
            }
        }
        $searchTerm = implode(' ', $words);

        return $searchTerm;
    }

    public function scopeSearch($query, $term)
    {
        $columns = implode(',', $this->searchable);
        $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->fullTextWildcards($term));

        return $query;
    }
}
