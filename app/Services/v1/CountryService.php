<?php

namespace App\Services\v1;

use App\Models\Country;

class CountryService
{
    public function getAllCountries(?string $search = null)
    {
        $query = Country::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        return $query->get(); // Fetch all countries without pagination
    }
}
