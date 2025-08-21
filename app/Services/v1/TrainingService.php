<?php

namespace App\Services\v1;

use App\Models\Training;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TrainingService
{
    public function getAllTrainings(int $page = 1, int $perPage = 10, ?string $search = null)
    {
        $query = Training::query()
            ->select('trainings.*', 'organizers.name as organizer_name') // Select training columns and organizer name
            ->join('organizers', 'trainings.organization_id', '=', 'organizers.id'); // Join with organizers table

        if ($search) {
            $query->where(function ($q) use ($search) {
                if ($search === 'বৈদেশিক') {
                    $q->where('trainings.type', 2); // Filter by type 2 (foreign)
                } elseif ($search === 'স্থানীয়') {
                    $q->where('trainings.type', 1); // Filter by type 1 (local)
                } else {
                    $q->where('trainings.name', 'like', "%{$search}%") // Search by training name
                      ->orWhere('organizers.name', 'like', "%{$search}%"); // Search by organizer name
                }
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function createTraining(array $data)
    {
        $countries = $data['countries'] ?? []; // Extract countries if provided
        unset($data['countries']); // Remove countries from the main data array

        if (isset($data['file_link'])) {
            $filePath = $this->uploadFile($data['file_link']); // Store file and get the full path
            $data['file_name'] = basename($filePath); // Extract only the file name
            $data['file_link'] = asset('storage/' . $filePath); // Generate the public URL
            unset($data['file_link']); // Remove file_link from data
        }

        $training = Training::create($data);

        // Sync countries if the training type is 2 (foreign)
        if ((int)$training->type === 2) {
            $training->countries()->sync($countries);
        }

        return $training;
    }

    public function getTrainingById(int $id)
    {
        $training = Training::query()
            ->select('trainings.*', 'organizers.name as organizer_name') // Include organizer_name
            ->join('organizers', 'trainings.organization_id', '=', 'organizers.id') // Join with organizers table
            ->with('countries') // Eager load the countries relation
            ->findOrFail($id);

        return $training;
    }

    public function updateTraining($id, array $data)
    {
        Log::info('Updating training with ID: ' . $id, ['data' => $data]);
        $training = Training::findOrFail($id);

        $countries = $data['countries'] ?? []; // Extract countries if provided
        unset($data['countries']); // Remove countries from the main data array

        if (isset($data['file_link'])) {
            // Delete the old file if it exists
            if ($training->file_name) {
                Storage::disk('public')->delete('training/' . $training->file_name);
            }

            $filePath = $this->uploadFile($data['file_link']); // Store new file and get the full path
            $data['file_name'] = basename($filePath); // Extract only the file name
            $data['file_link'] = asset('storage/' . $filePath); // Generate the public URL
            unset($data['file_link']); // Remove file_link from data
        }

        $training->update($data);

        // Handle countries based on the training type
        if ((int)$training->type === 2) {
            $training->countries()->sync($countries); // Sync countries, removing any that are no longer associated
        } else {
            // Remove all countries if the training type is 1 (local)
            $training->countries()->detach();
        }

        return $training;
    }

    public function deleteTraining($id)
    {
        $training = Training::findOrFail($id);

        // Delete the file if it exists
        if ($training->file_name) {
            Storage::disk('public')->delete('training/' . $training->file_name);
        }

        $training->delete();

        return true;
    }

    private function uploadFile($file)
    {
        try {
            // Store file in the 'training' directory in the 'public' disk
            $path = $file->store('training', 'public');
            if (!$path) {
                throw new \Exception('File upload failed.');
            }
            return $path; // Returns the file path
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error("File upload error: {$e->getMessage()}");
            throw $e;
        }
    }

    public function formatTrainingResponse(Training $training)
    {
        return [
            'id' => $training->id,
            'name' => $training->name,
            'type' => $training->type,
            'organization_id' => $training->organization_id,
            'start_date' => $training->start_date,
            'end_date' => $training->end_date,
            'total_days' => $training->total_days,
            'file_name' => $training->file_name, // Actual file name
            'file_link' => $training->file_name ? asset('storage/' . $training->file_name) : null, // Public URL for the file
        ];
    }
}
