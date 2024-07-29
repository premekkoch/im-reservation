<?php declare(strict_types=1);

namespace App\Model\Reservation;

use Nextras\Orm\Repository\Repository;

class ReservationsRepository extends Repository
{
    public static function getEntityClassNames(): array
    {
        return [Reservation::class];
    }
}
