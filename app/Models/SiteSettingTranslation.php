<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettingTranslation extends Model
{
    protected $table = 'site_settings_translations';

    protected $fillable = [
        'key',
        'locale',
        'value',
    ];
}
