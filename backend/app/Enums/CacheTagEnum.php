<?php

declare(strict_types=1);

namespace App\Enums;

enum CacheTagEnum: string
{
    case PREFERENCE = 'Preference';
    case PROJECT = 'Project';
    case TIME_FRAME = 'TimeFrame';
    case TIME_ENTRY = 'TimeEntry';
    case MEDIA = 'Media';
}
