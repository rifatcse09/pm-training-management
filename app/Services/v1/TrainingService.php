<?php

namespace App\Services\v1;

use App\Models\Training;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    public function createTraining(array $data, $file = null)
    {
        if ($file) {
            $filePath = $this->uploadFile($file); // Store file and get the full path
            $data['file_name'] = basename($filePath); // Extract only the file name
            $data['file_link'] = asset('storage/' . $filePath); // Generate the public URL
        }

        $training = Training::create($data);

        Log::info("Training created successfully: {$training->id}");

        return $training;
    }

    public function getTrainingById($id)
    {
        return Training::findOrFail($id);
    }

    public function updateTraining($id, array $data, $file = null)
    {
        Log::info("Incoming data for training update", ['data' => $data]);

        try {
            $training = Training::findOrFail($id);

            if ($file) {
                // Delete the old file if a new file is uploaded
                if ($training->file_name) {
                    $this->deleteFile($training->file_name);
                }

                $filePath = $this->uploadFile($file); // Store new file and get the full path
                $data['file_name'] = basename($filePath); // Extract only the file name
                $data['file_link'] = asset('storage/' . $filePath); // Generate the public URL
            } else {
                // Retain the existing file if no new file is uploaded
                $data['file_name'] = $training->file_name;
                $data['file_link'] = $training->file_link;
            }

            $training->update($data);

            Log::info("Training updated successfully: {$training->id}");

            return $training;
        } catch (\Exception $e) {
            Log::error("Error updating training with ID: {$id} - {$e->getMessage()}");
            throw $e;
        }
    }

    public function deleteTraining($id)
    {
        $training = Training::findOrFail($id);

        // Delete the file if it exists
        if ($training->file_name) {
            $this->deleteFile($training->file_name);
        }

        $training->delete();

        Log::info("Training deleted successfully: {$training->id}");

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

    private function deleteFile($fileName)
    {
        try {
            $deleted = Storage::disk('public')->delete('training/' . $fileName);
            if (!$deleted) {
                Log::warning("Failed to delete file: training/{$fileName}");
            }
        } catch (\Exception $e) {
            Log::error("Error deleting file: {$e->getMessage()}");
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
            'file_link' => $training->file_name ? asset('storage/training/' . $training->file_name) : null, // Public URL for the file
        ];
    }
}
