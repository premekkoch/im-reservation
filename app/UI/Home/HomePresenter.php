<?php declare(strict_types=1);

namespace App\UI\Home;

use App\Model\Orm;
use Nextras\Dbal\Utils\DateTimeImmutable;
use App\Model\Reservation\Reservation;
use App\Services\SlotService;
use Nette;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * @property-read HomeTemplate $template
 */
final class HomePresenter extends Nette\Application\UI\Presenter
{
    #[Persistent]
    public int $day;
    #[Persistent]
    public int $roomId;

    public function __construct(
        private readonly Orm                    $orm,
        private readonly FilterFormFactory      $filterFormFactory,
        private readonly ReservationFormFactory $reservationFormFactory,
        private readonly SlotService            $slotService,
    )
    {
        parent::__construct();
    }

    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Login:');
        }
    }

    public function actionDefault(?int $day, ?int $roomId): void
    {
        $this->day = $day ?? (new DateTimeImmutable('now'))->getTimestamp();
        $this->roomId = $roomId ?? 1;
    }

    public function renderDefault(): void
    {
        $this->template->date = (new DateTimeImmutable())->setTimestamp($this->day);
        $this->template->room = $this->orm->rooms->getByIdChecked($this->roomId);

        $this->template->reservations = $this->orm->reservations->findBy([
            'room' => $this->roomId,
            'workday' => (new DateTimeImmutable())->setTimestamp($this->day)->setTime(0, 0),
        ])->orderBy('slot');
    }

    public function actionDelete(int $id): void
    {
        $reservation = $this->orm->reservations->getBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if ($reservation) {
            $this->orm->removeAndFlush($reservation);
            $this->flashMessage('Reservation was deleted', 'success');
        }

        $this->redirect('Home:default');
    }

    public function actionGetData(int $day, int $roomId, string $startIndex): void
    {
        $date = (new DateTimeImmutable())->setTimestamp($day)->setTime(0, 0);
        $items = $this->slotService->getNextSlots($date, $roomId, intval($startIndex));

        $this->sendJson($items);
    }

    protected function createComponentFilterForm(): Form
    {
        $date = (new DateTimeImmutable())->setTimestamp($this->day)->setTime(0, 0);

        $form = $this->filterFormFactory->create($date, $this->roomId);
        $form->onSuccess[] = $this->onFilterFormSuccess(...);

        return $form;
    }

    protected function createComponentReservationForm(): Form
    {
        $date = (new DateTimeImmutable())->setTimestamp($this->day)->setTime(0, 0);

        $form = $this->reservationFormFactory->create($date, $this->roomId);
        $form->onSuccess[] = $this->onReservationFormSuccess(...);

        return $form;
    }

    private function onFilterFormSuccess(Form $form, ArrayHash $values): void
    {
        $this->redirect('Home:default', $values->workday->getTimeStamp(), $values->room);
    }

    private function onReservationFormSuccess(Form $form, ArrayHash $values): void
    {
        $date = (new DateTimeImmutable())->setTimestamp($this->day)->setTime(0, 0);

        if ($this->slotService->isSlotFree($date, $this->roomId, $values->start, $values->stop) === false) {
            $this->flashMessage('Sorry, this slot is not available for this room.', 'danger');
            $this->redirect('Home:default');
        }

        $user = $this->orm->users->getByIdChecked($this->getUser()->getId());
        $room = $this->orm->rooms->getByIdChecked($this->roomId);

        $reservation = new Reservation();

        $reservation->user = $user;
        $reservation->room = $room;
        $reservation->workday = $date;
        $reservation->slot = $values->start;
        $reservation->duration = $values->stop - $values->start +1;

        $this->orm->persistAndFlush($reservation);

        $this->flashMessage('Reservation was created', 'success');
        $this->redirect('Home:default');
    }
}
