<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

trait FirebaseSyncTrait
{
    private function getFirebaseDatabase()
    {
        $credentialPath = storage_path('app/firebase/classmanagementsystem.json');

        if (! is_file($credentialPath)) {
            throw new \Exception('Firebase JSON file not found. Please check storage/app/firebase directory.');
        }

        $factory = (new Factory)
            ->withServiceAccount($credentialPath)
            ->withDatabaseUri('https://classmanagementsystem-cd57f-default-rtdb.firebaseio.com/');

        return $factory->createDatabase();
    }

    private function syncWithFirebase($reference, $message = 'Data updated')
    {
        try {
            $this->getFirebaseDatabase()
                ->getReference($reference)
                ->set([
                    'updated_at' => now()->timestamp,
                    'message' => $message,
                ]);
        } catch (\Exception $e) {
            Log::error('Firebase Sync Error: '.$e->getMessage());
        }
    }

    private function syncFirebaseNode($path, $data)
    {
        try {
            $this->getFirebaseDatabase()
                ->getReference($path)
                ->set($data);
        } catch (\Exception $e) {
            Log::error('Firebase Node Sync Error: '.$e->getMessage());
        }
    }

    private function updateFirebaseNode($path, $data)
    {
        try {
            $this->getFirebaseDatabase()
                ->getReference($path)
                ->update($data);
        } catch (\Exception $e) {
            Log::error('Firebase Update Error: '.$e->getMessage());
        }
    }

    private function removeFirebaseNode($path)
    {
        try {
            $this->getFirebaseDatabase()
                ->getReference($path)
                ->remove();
        } catch (\Exception $e) {
            Log::error('Firebase Remove Error: '.$e->getMessage());
        }
    }
}
