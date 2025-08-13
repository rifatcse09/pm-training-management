<?php

namespace App\Services\v1;

use App\Models\Organizer;

class OrganizerService
{
    public function getAllOrganizers()
    {
        return Organizer::all();
    }

    public function createOrganizer(array $data)
    {
        return Organizer::create($data);
    }

    public function getOrganizerById($id)
    {
        return Organizer::findOrFail($id);
    }

    public function updateOrganizer($id, array $data)
    {
        $organizer = Organizer::findOrFail($id);
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
