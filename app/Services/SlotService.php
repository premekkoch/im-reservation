<?php declare(strict_types=1);

namespace App\Services;

use App\Model\Orm;
use App\Model\Reservation\Reservation;
use DateInterval;
use Nextras\Dbal\Utils\DateTimeImmutable;

final class SlotService
{
    private const SLOTS_PER_DAY = 48;   // 1 slot duration is 0.5h
    private const FIRST_SLOT = 12;      // First slot for reservation begins at 6:00
    private const LAST_SLOT = 37;       // Last slot for reservation begins at 18:30

    public const TIME_TYPE_BEGIN = 1;
    public const TIME_TYPE_END = 2;

    public function __construct(private readonly Orm $orm) {}

    public static function getSlotTime(int $slotIndex, int $timeType): DateInterval
    {
        $fraction = 24 / self::SLOTS_PER_DAY * $slotIndex;

        if ($timeType === self::TIME_TYPE_END) {
            $fraction += 24 / self::SLOTS_PER_DAY;
        }

        $hour = intval($fraction);
        $minute = fmod($fraction, 1) * 60;

        return new DateInterval('PT' . $hour . 'H' . $minute . 'M');
    }

    public function getStartSlots(DateTimeImmutable $date): array
    {
        $date->setTime(00, 00, 00);
        $slots = $this->getSlotTable(self::FIRST_SLOT, self::TIME_TYPE_BEGIN);
        $reservations = $this->orm->reservations->findBy(['workday' => $date]);

        /** @var Reservation $reservation */
        foreach ($reservations as $reservation) {
            for ($i = 0; $i < $reservation->duration; $i++) {
                unset($slots[$reservation->slot + $i]);
            }
        }

        return $slots;
    }

    public function getNextSlots(DateTimeImmutable $date, int $startIndex): array
    {
        $date->setTime(00, 00, 00);
        $slots = $this->getSlotTable($startIndex, self::TIME_TYPE_END);
        $reservations = $this->orm->reservations->findBy(['workday' => $date, 'slot >=' => $startIndex]);

        /** @var Reservation $reservation */
        foreach ($reservations as $reservation) {
            if (isset($slots[$reservation->slot])) {
                array_splice($slots, $reservation->slot - 1);
            }
        }

        return $slots;
    }

    private function getSlotTable(int $startIndex, int $timeType): array
    {
        $slots = [];

        for ($i = $startIndex; $i <= self::LAST_SLOT; $i++) {
            $time = $this->getSlotTime($i, $timeType);
            $slots[$i] = sprintf('%d:%02d', $time->h, $time->i);
        }

        return $slots;
    }
}
