<?php declare(strict_types=1);

namespace App\Model;

use App\Model\User\UsersRepository;
use Nextras\Orm\Model\Model;

/**
 * @property-read UsersRepository $users
 */
class Orm extends Model
{
}
