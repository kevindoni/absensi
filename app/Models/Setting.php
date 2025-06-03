<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    
    protected $fillable = ['key', 'value'];
    
    /**
     * Get a setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSetting($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->value;
    }

    /**
     * Set a setting by key
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function setSetting($key, $value)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->value = $value;
            return $setting->save();
        } else {
            return self::create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }
    
    /**
     * Update multiple settings at once
     *
     * @param array $settings
     * @return bool
     */
    public static function setMultipleSettings($settings)
    {
        try {
            foreach ($settings as $key => $value) {
                self::setSetting($key, $value);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Delete a setting by key
     *
     * @param string $key
     * @return bool
     */
    public static function deleteSetting($key)
    {
        return self::where('key', $key)->delete();
    }
    
    /**
     * Get all settings as key-value pairs
     *
     * @return array
     */
    public static function getAllSettings()
    {
        return self::pluck('value', 'key')->toArray();
    }
}
