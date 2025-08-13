<?php

namespace App\Services\v1;

use App\Models\Training;

class TrainingService
{
    public function getAllTrainings()
    {
        return Training::all();
    }

    public function createTraining(array $data)
    {
        return Training::create($data);
    }

    public function getTrainingById($id)
    {
        return Training::findOrFail($id);
    }

    public function updateTraining($id, array $data)
    {
        $training = Training::findOrFail($id);
        $training->update($data);
        return $training;
    }

    public function deleteTraining($id)
    {
        $training = Training::findOrFail($id);
        $training->delete();
    }
}
