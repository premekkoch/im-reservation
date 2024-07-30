<?php declare(strict_types=1);

namespace App\UI\Home;

use App\Model\Orm;
use Nette\Application\UI\Form;
use Nextras\Dbal\Utils\DateTimeImmutable;

final class FilterFormFactory
{
    public function __construct(private readonly Orm $orm) {}

    public function create(DateTimeImmutable $date, int $room): Form
    {
        $form = new Form();
        $form->addDate('workday', 'Date')
            ->setRequired('Please fill the date')
            ->setDefaultValue($date->format('d.m.Y'));

        $form->addSelect('room', 'Meeting room')
            ->setRequired('Please select a room')
            ->setItems($this->getRooms())
            ->setDefaultValue($room);

        $form->addSubmit('send', 'Filter');

        return $form;
    }

    private function getRooms(): array
    {
        return $this->orm->rooms->findAll()->orderBy('name')->fetchPairs('id', 'name');
    }
}
