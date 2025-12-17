<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        // Cast value based on type
        return match($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $type = 'string', $description = null)
    {
        // Convert value to string for storage
        $storedValue = match($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Check if auto cleanup is enabled
     */
    public static function isAutoCleanupEnabled()
    {
        return self::get('activity_log_auto_cleanup', true);
    }

    /**
     * Get retention days for activity logs
     */
    public static function getActivityLogRetentionDays()
    {
        return self::get('activity_log_retention_days', 30);
    }

    /**
     * Check if document auto cleanup is enabled
     */
    public static function isDocumentAutoCleanupEnabled()
    {
        return self::get('document_auto_cleanup', false);
    }

    /**
     * Get retention days for documents
     */
    public static function getDocumentRetentionDays()
    {
        return self::get('document_retention_days', 90);
    }
}