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

    public function getStartSlots(DateTimeImmutable $date, int $roomId): array
    {
        $date->setTime(00, 00, 00);
        $slots = $this->getSlotTable(self::FIRST_SLOT, self::TIME_TYPE_BEGIN);
        $reservations = $this->orm->reservations->findBy(['workday' => $date, 'room' => $roomId]);

        /** @var Reservation $reservation */
        foreach ($reservations as $reservation) {
            for ($i = 0; $i < $reservation->duration; $i++) {
                unset($slots[$reservation->slot + $i]);
            }
        }

        return $slots;
    }

    public function getNextSlots(DateTimeImmutable $date, int $roomId, int $startIndex): array
    {
        if ($this->isSlotFree($date, $roomId, $startIndex) === false) {
            return [];
        }

        $date->setTime(00, 00, 00);
        $slots = $this->getSlotTable($startIndex, self::TIME_TYPE_END);

        /** @var Reservation $nextReservation */
        $nextReservation = $this->orm->reservations
            ->findBy(['workday' => $date, 'room' => $roomId, 'slot>=' => $startIndex])
            ->orderBy('slot')
            ->limitBy(1)
            ->fetch();

        if ($nextReservation) {
            $this->cropTable($slots, $nextReservation->slot);
        }

        return $slots;
    }

    public function isSlotFree(DateTimeImmutable $date, int $roomId, int $startIndex, ?int $stopIndex = null): bool
    {
        $slots = $this->getStartSlots($date, $roomId);

        if ($stopIndex === null) {
            return isset($slots[$startIndex]);
        }

        if (!isset($slots[$startIndex])) {
            return false;
        }

        $slots = $this->getNextSlots($date, $roomId, $startIndex);

        for ($i = $startIndex; $i <= $stopIndex; $i++) {
            if (!isset($slots[$i])) {
                return false;
            }
        }

        return true;
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

    private function cropTable(array &$slots, int $index): void
    {
        $maxIndex = max(array_keys($slots));

        if ($index <= $maxIndex) {
            for ($i = $index; $i <= $maxIndex; $i++) {
                unset($slots[$i]);
            }
        }
    }
}
