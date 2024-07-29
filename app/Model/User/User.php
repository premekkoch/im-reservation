<?php declare(strict_types=1);

namespace App\Model\User;

use App\Model\Reservation\Reservation;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * @property int                      $id          {primary}
 * @property string                   $email
 * @property string                   $password
 * @property OneHasMany|Reservation[] $reservations {1:m Reservation::$user}
 */
class User extends Entity
{
}
