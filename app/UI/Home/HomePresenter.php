<?php declare(strict_types=1);

namespace App\UI\Home;

use App\Model\Orm;
use DateTimeImmutable;
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
    public string $workday;
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

    public function actionDefault(?string $workday, ?int $roomId): void
    {
        $this->workday = $workday ?? (new DateTimeImmutable('now'))->format('Y-m-d');
        $this->roomId = $roomId ?? 1;
    }

    public function renderDefault(): void
    {
        $this->template->room = $this->orm->rooms->getByIdChecked($this->roomId);
        $this->template->reservations = $this->orm->reservations->findBy([
            'room' => $this->roomId,
            'workday' => DateTimeImmutable::createFromFormat('Y-m-d', $this->workday)->setTime(0, 0),
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
        $form = $this->filterFormFactory->create($this->workday, $this->roomId);
        $form->onSuccess[] = $this->onFilterFormSuccess(...);

        return $form;
    }

    private function onFilterFormSuccess(Form $form, ArrayHash $values): void
    {
        $this->redirect('Home:default', $values->workday->format('Y-m-d'), $values->room);
    }
}
