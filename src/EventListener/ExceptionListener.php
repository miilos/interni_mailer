<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

// #[AsEventListener]
final class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        $error = match ($e::class) {
            BadRequestException::class => $this->getResponse($e, 'Bad data in request!', Response::HTTP_BAD_REQUEST),
            default => $this->getResponse($e, 'Something went wrong!', Response::HTTP_INTERNAL_SERVER_ERROR),
        };

        $event->setResponse($error);
    }

    private function getResponse(\Throwable $exception, string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
            'details' => $exception::class . ' ' . $exception->getMessage(),
        ], $statusCode);
    }
}
