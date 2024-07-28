<?php declare(strict_types=1);

namespace App\UI\Home;

use Nette;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Login:');
        }
    }
}
