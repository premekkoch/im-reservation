<?php declare(strict_types=1);

namespace App\UI\Error\Error5xx;

use Nette;
use Nette\Application\Responses;
use Nette\Http;
use Tracy\ILogger;

final class Error5xxPresenter implements Nette\Application\IPresenter
{
    public function __construct(
        private readonly ILogger $logger,
    ) {}


    public function run(Nette\Application\Request $request): Nette\Application\Response
    {
        $exception = $request->getParameter('exception');
        $this->logger->log($exception, ILogger::EXCEPTION);

        return new Responses\CallbackResponse(function (Http\IRequest $httpRequest, Http\IResponse $httpResponse): void {
            if (preg_match('#^text/html(?:;|$)#', (string)$httpResponse->getHeader('Content-Type'))) {
                require __DIR__ . '/500.phtml';
            }
        });
    }
}
