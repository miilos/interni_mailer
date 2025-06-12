<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationFailedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onValidationFailed',
        ];
    }

    public function onValidationFailed(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof UnprocessableEntityHttpException) {
            return;
        }

        $validationException = $exception->getPrevious();
        if (!$validationException instanceof ValidationFailedException) {
            return;
        }

        $violations = $validationException->getViolations();

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        $response = new JsonResponse([
            'status' => 'fail',
            'message' => 'Validation error',
            'errors' => $errors,
        ]);

        $event->setResponse($response);
    }
}
