<?php declare(strict_types=1);

namespace App\UI\Register;

use App\Model\Orm;
use App\Model\User\User;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

class RegisterPresenter extends Presenter
{
    public function __construct(
        private readonly RegisterFormFactory $registerFormFactory,
        private readonly Orm                 $orm,
        private readonly Passwords           $passwords,
    )
    {
        parent::__construct();
    }

    public function createComponentRegisterForm(): Form
    {
        $form = $this->registerFormFactory->create();
        $form->onSuccess[] = $this->onRegisterFormSuccess(...);
        $form->onValidate[] = $this->onRegisterFormValidate(...);

        return $form;
    }

    private function onRegisterFormSuccess(Form $form, ArrayHash $values): void
    {
        $user = new User();

        $user->email = $values->email;
        $user->password = $this->passwords->hash($values->password);
        $this->orm->persistAndFlush($user);

        $this->getUser()->login($values->email, $values->password);
        $this->redirect('Home:');
    }

    private function onRegisterFormValidate(Form $form, ArrayHash $values): void
    {
        $user = $this->orm->users->getBy(['email' => $values->email]);

        if ($user) {
            $form['email']->addError("This email is already taken");
        }
    }
}
