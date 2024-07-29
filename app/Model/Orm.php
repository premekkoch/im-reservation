<?php declare(strict_types=1);

namespace App\Model;

use App\Model\Reservation\ReservationsRepository;
use App\Model\Room\RoomsRepository;
use App\Model\User\UsersRepository;
use Nextras\Orm\Model\Model;

/**
 * @property-read UsersRepository        $users
 * @property-read RoomsRepository        $rooms
 * @property-read ReservationsRepository $reservations
 */
class Orm extends Model
{
}
