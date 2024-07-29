<?php declare(strict_types=1);

namespace App\UI\Home;

use App\Model\Room\Room;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\User;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;

class HomeTemplate extends Template
{
    public array $flashes;
    public ICollection $reservations;
    public IEntity|Room $room;
    public User $user;
}
