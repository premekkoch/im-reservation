<?php declare(strict_types=1);

namespace App\UI\Login;

use Nette\Application\UI\Form;

final class LoginFormFactory
{
    public function create(): Form
    {
        $form = new Form();

        $form->addEmail('email', 'Email:')
            ->setRequired('Enter your email');

        $form->addPassword('password', 'Password:', 25, 255)
            ->setRequired('Enter your password');

        $form->addSubmit('send', 'Login');

        return $form;
    }
}
