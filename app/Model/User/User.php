<?php declare(strict_types=1);

namespace App\Model\User;

use Nextras\Orm\Entity\Entity;

/**
 * @property int    $id          {primary}
 * @property string $email
 * @property string $password
 */
class User extends Entity
{
}
