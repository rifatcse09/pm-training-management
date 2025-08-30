<?php

namespace App\Services\v1;

use App\Models\GroupTraining;
use Illuminate\Support\Facades\Storage;

class GroupTrainingService
{
    /**
     * Create a new group training record.
     *
     * @param array $data
     * @return int The ID of the created group training
     */
    public function createGroupTraining(array $data): int
    {
        // Handle file upload if file_link is provided
        if (isset($data['file_link']) && $data['file_link'] instanceof \Illuminate\Http\UploadedFile) {
            $filePath = $this->uploadFile($data['file_link']); // Store file and get the full path
            $data['file_name'] = basename($filePath); // Extract only the file name
            $data['file_link'] = asset('storage/' . $filePath); // Generate the public URL
        }

        // Create the group training record
        $groupTraining = GroupTraining::create([
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_days' => $data['total_days'],
            'file_link' => $data['file_link'] ?? null,
            'file_name' => $data['file_name'] ?? null,
        ]);

        return $groupTraining->id; // Return the ID of the created group training
    }

    /**
     * Upload a file to storage.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string The file path
     */
    private function uploadFile($file): string
    {
        return $file->store('group_training_files', 'public'); // Store file in the 'public/group_training_files' directory
    }
}
