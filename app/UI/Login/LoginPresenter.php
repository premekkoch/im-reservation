<?php declare(strict_types=1);

namespace App\UI\Login;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

class LoginPresenter extends Presenter
{
    public function __construct(private readonly LoginFormFactory $loginFormFactory)
    {
        parent::__construct();
    }

    public function actionLogout(): void
    {
        $this->getUser()->logout();
        $this->flashMessage('You have been logged out.');

        $this->redirect('Login:');
    }

    public function createComponentLoginForm(): Form
    {
        $form = $this->loginFormFactory->create();
        $form->onSuccess[] = $this->onLoginSuccess(...);

        return $form;
    }

    private function onLoginSuccess(Form $form, ArrayHash $values): void
    {
        try {
            $this->getUser()->login($values->email, $values->password);

            $this->redirect('Home:');
        } catch (AuthenticationException $e) {
            $this->flashMessage($e->getMessage());
        }
    }
}
