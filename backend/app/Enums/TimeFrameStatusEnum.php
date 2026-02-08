<?php

declare(strict_types=1);

namespace App\Enums;

enum TimeFrameStatusEnum: string
{
    case DONE = 'done';
    case IN_PROGRESS = 'in_progress';
    case CANCELED = 'canceled';

}
