<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    // Example dummy static method to return nearby area IDs
    public static function getNearbyAreaIds($areaId)
    {
        // Static nearby areas mapping
        $areaGroups = [
            5 => [5, 6, 7, 8],  // Example: Area ID 5 is near 5,6,7,8
            10 => [10, 11, 12], // Example: Area ID 10 is near 10,11,12
        ];

        // Return nearby area IDs if exists, else return itself
        return $areaGroups[$areaId] ?? [$areaId];
    }
}
