<?php

namespace App\EventListener;

use App\Service\EmailParser\BodyParser\ParserException;
use App\Service\EmailParser\BodyParser\UnsupportedTemplateFormatException;
use App\Service\GroupManager\GroupManagerException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
final class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        $error = match ($e::class) {
            BadRequestException::class => $this->getResponse($e, 'Bad data in request!', Response::HTTP_BAD_REQUEST),
            ParserException::class,
            GroupManagerException::class,
            UnsupportedTemplateFormatException::class
                => $this->getResponse($e, $e->getMessage(), $e->getCode()),
            default => $this->getResponse($e, 'Something went wrong!', Response::HTTP_INTERNAL_SERVER_ERROR),
        };

        $event->setResponse($error);
    }

    private function getResponse(\Throwable $exception, string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse([
            'status' => 'fail',
            'message' => $message,
            'details' => $exception::class . ' ' . $exception->getMessage(),
        ], $statusCode);
    }
}
