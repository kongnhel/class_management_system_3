<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * កំណត់ឈ្មោះ Table
     */
    protected $table = 'chat_messages';

    /**
     * អនុញ្ញាតឱ្យបញ្ចូលទិន្នន័យក្នុង Column ទាំងនេះ
     */
    protected $fillable = [
        'user_id',
        'message',
        'sender',
    ];

    /**
     * បង្ហាប់ message column (សារជាមួយលេខគោលក្រុម សម្រាប់ផ្ទុក)
     * នេះនឹងបង្ហាប់វាដោយស្វ័យប្រវត្តិនៅពេលរក្សាទុក
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Mutator: បង្ហាប់ message មុនពេលរក្សាទុក
     */
    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = encrypt($value);
    }

    /**
     * Accessor: ពន្លាយ message នៅពេលទាញយក
     */
    public function getMessageAttribute($value)
    {
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            // Fallback ប្រសិនបើ decryption ខ្ងាច
            \Illuminate\Support\Facades\Log::warning('Failed to decrypt chat message', [
                'error' => $e->getMessage(),
            ]);

            return $value;
        }
    }

    /**
     * បង្កើត Relationship ជាមួយ User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
