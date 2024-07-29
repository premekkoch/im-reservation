<?php declare(strict_types=1);

namespace App\Model\Room;

use App\Model\Reservation\Reservation;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * @property int                      $id {primary}
 * @property string                   $name
 * @property OneHasMany|Reservation[] $reservations  {1:m Reservation::$room}
 */
class Room extends Entity
{

}
