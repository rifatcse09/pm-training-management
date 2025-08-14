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
        if (isset($data['file_link']) && $data['file_link'] !== null) {
            $data['file_link'] = $this->uploadFile($data['file_link']);
        }

        return Training::create($data);
    }

    public function getTrainingById($id)
    {
        $training = Training::find($id);

        if (!$training) {
            throw new \Exception('Training not found.');
        }

        return $training;
    }

    public function updateTraining($id, array $data)
    {
        $training = Training::find($id);

        if (!$training) {
            throw new \Exception('Training not found.');
        }

        if (isset($data['file_link']) && $data['file_link'] !== null) {
            // Delete the old file if it exists
            if ($training->file_link) {
                Storage::delete($training->file_link);
            }

            $data['file_link'] = $this->uploadFile($data['file_link']);
        }

        $training->update($data);

        return $training;
    }

    public function deleteTraining($id)
    {
        $training = Training::findOrFail($id);

        // Delete the file if it exists
        if ($training->file_link) {
            Storage::delete($training->file_link);
        }

        $training->delete();
    }

    private function uploadFile($file)
    {
        return $file->store('trainings', 'public'); // Store file in the 'trainings' directory in the 'public' disk
    }
}
