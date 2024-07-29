<?php declare(strict_types=1);

namespace App\Model\Reservation;

use App\Model\Room\Room;
use App\Model\User\User;
use App\Services\SlotService;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\ManyHasOne;

/**
 * @property int                    $id {primary}
 * @property ManyHasOne|User        $user {m:1 User::$reservations}
 * @property ManyHasOne|Room        $room {m:1 Room::$reservations}
 * @property DateTimeImmutable      $workday
 * @property int                    $slot
 * @property int                    $duration
 * @property-read DateTimeImmutable $start {virtual}
 * @property-read DateTimeImmutable $stop {virtual}
 */
class Reservation extends Entity
{
    protected function getterStart(): DateTimeImmutable
    {
        $slotTime = SlotService::getSlotTime($this->slot, SlotService::TIME_TYPE_BEGIN);

        return (clone $this->workday)->setTime($slotTime->h, $slotTime->i);
    }

    protected function getterStop(): DateTimeImmutable
    {
        $slotTime = SlotService::getSlotTime($this->slot + $this->duration - 1, SlotService::TIME_TYPE_END);

        return (clone $this->workday)->setTime($slotTime->h, $slotTime->i);
    }
}
