<?php declare(strict_types=1);

namespace App\UI\Register;

use Nette\Application\UI\Form;

final class RegisterFormFactory
{
    public function create(): Form
    {
        $form = new Form();

        $form->addEmail('email', 'Email:')
            ->setRequired('Enter your email');

        $form->addPassword('password', 'Password:', 25, 255)
            ->setRequired('Enter your password')
            ->addRule(\Nette\Forms\Form::MinLength, 'Password must be at least %d characters length', 8);

        $form->addPassword('passwordCheck', 'Password again:', 25, 255)
            ->setRequired('Repeat your password')
            ->addRule(\Nette\Forms\Form::MinLength, 'Password must be at least %d characters length', 8)
            ->addRule(\Nette\Forms\Form::Equal, 'Passwords do not match', $form['password']);

        $form->addSubmit('send', 'Register');

        return $form;
    }
}
