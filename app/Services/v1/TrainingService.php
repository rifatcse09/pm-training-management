<?php

namespace App\Services\v1;

use App\Models\Training;
use Illuminate\Support\Facades\Storage;

class TrainingService
{
    public function getAllTrainings($page = 1, $perPage = 10, $search = null)
    {
        $query = Training::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function createTraining(array $data)
    {
        if (isset($data['file_link'])) {
            $filePath = $this->uploadFile($data['file_link']); // Store file and get the full path
            $data['file_name'] = basename($filePath); // Extract only the file name
            $data['file_link'] = asset('storage/' . $filePath); // Generate the public URL
            unset($data['file_link']); // Remove file_link from data
        }

        return Training::create($data);
    }

    public function getTrainingById($id)
    {
        return Training::findOrFail($id);
    }

    public function updateTraining($id, array $data)
    {
        $training = Training::findOrFail($id);

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

        return $training;
    }

    public function deleteTraining($id)
    {
        $training = Training::findOrFail($id);

        // Delete the file if it exists
        if ($training->file_name) {
            Storage::disk('public')->delete($training->file_name);
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
            \Log::error("File upload error: {$e->getMessage()}");
            throw $e;
        }
    }

    public function formatTrainingResponse(Training $training)
    {
        return [
            'id' => $training->id,
            'name' => $training->name,
            'type' => $training->type,
            'start_date' => $training->start_date,
            'end_date' => $training->end_date,
            'file_name' => $training->file_name, // Actual file name
            'file_link' => $training->file_name ? asset('storage/' . $training->file_name) : null, // Public URL for the file
        ];
    }
}
