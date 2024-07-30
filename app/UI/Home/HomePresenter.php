<?php declare(strict_types=1);

namespace App\UI\Home;

use App\Model\Orm;
use Nextras\Dbal\Utils\DateTimeImmutable;
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
        private readonly Orm               $orm,
        private readonly FilterFormFactory $filterFormFactory,
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

    protected function createComponentFilterForm(): Form
    {
        $date = (new DateTimeImmutable())->setTimestamp($this->day)->setTime(0, 0);

        $form = $this->filterFormFactory->create($date, $this->roomId);
        $form->onSuccess[] = $this->onFilterFormSuccess(...);

        return $form;
    }

    private function onFilterFormSuccess(Form $form, ArrayHash $values): void
    {
        $this->redirect('Home:default', $values->workday->getTimeStamp(), $values->room);
    }
}
