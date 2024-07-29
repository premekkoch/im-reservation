<?php declare(strict_types=1);

namespace App\Model\Room;

use Nextras\Orm\Repository\Repository;

class RoomsRepository extends Repository
{
    public static function getEntityClassNames(): array
    {
        return [Room::class];
    }
}
