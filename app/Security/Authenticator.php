<?php declare(strict_types=1);

namespace App\Security;

use App\Model\Orm;
use App\Model\User\User;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

class Authenticator implements \Nette\Security\Authenticator
{
    public function __construct(
        private readonly Orm       $orm,
        private readonly Passwords $passwords,
    ) {}

    public function authenticate(string $user, string $password): IIdentity
    {
        /** @var User $userEntity */
        $userEntity = $this->orm->users->getBy(['email' => $user]);

        if (!$userEntity) {
            throw new AuthenticationException('Email was not found.', self::IdentityNotFound);
        }

        if (!$this->passwords->verify($password, $userEntity->password)) {
            throw new AuthenticationException('The password is incorrect.', self::InvalidCredential);
        }

        return new SimpleIdentity($userEntity->id, null, ['Email' => $userEntity->email]);
    }
}
