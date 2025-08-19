<?php

namespace App\Services\v1;

use App\Models\Organizer;

class OrganizerService
{
    public function getAllOrganizers(int $page = 1, int $perPage = 10, ?string $search = null)
    {
        $query = Organizer::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('place', 'like', "%{$search}%");
        }

        return $query->paginate($perPage, ['*'], 'page', $page); // Use paginate instead of get or all
    }

    public function createOrganizer(array $data)
    {
        return Organizer::create($data);
    }

    public function getOrganizerById($id)
    {
        $organizer = Organizer::find($id);

        if (!$organizer) {
            throw new \Exception('Organizer not found.');
        }

        return $organizer;
    }

    public function updateOrganizer($id, array $data)
    {
        $organizer = Organizer::find($id);

        if (!$organizer) {
            throw new \Exception('Organizer not found.');
        }

        $organizer->update($data);

        return $organizer;
    }

    public function deleteOrganizer($id)
    {
        $organizer = Organizer::findOrFail($id);
        $organizer->delete();
    }

    public function getProjectOrganizers()
    {
        return Organizer::where('is_project', true)->get(); // Fetch only project organizers
    }
}
