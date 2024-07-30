<?php declare(strict_types=1);

namespace App\UI\Home;

use App\Services\SlotService;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Form;
use Nextras\Dbal\Utils\DateTimeImmutable;

final class ReservationFormFactory
{
    public function __construct(private readonly SlotService $slotService, private readonly LinkGenerator $linkGenerator) {}

    public function create(DateTimeImmutable $date, int $roomId): Form
    {
        $form = new Form();

        $start = $form->addSelect('start', 'From');
        $stop = $form->addSelect('stop', 'To');
        $form->addSubmit('send', 'Save');

        $start->setRequired('Please select reservation start time')
            ->setItems($this->slotService->getStartSlots($date, $roomId))
            ->setPrompt('Reservation from')
            ->setHtmlAttribute('data-url', $this->linkGenerator->link('Home:getData', [$date->getTimestamp(), $roomId, '#']))
            ->setHtmlAttribute('data-dependent', $stop->getHtmlName());

        $stop->setPrompt('Reservation to')
            ->addConditionOn($start, Form::Filled)
            ->setRequired('Please select reservation end time');

        $form->onAnchor[] = fn() => $stop->setItems(
            $start->getValue()
                ? $this->slotService->getNextSlots($date, $roomId, $start->getValue())
                : []
        );

        return $form;
    }
}
