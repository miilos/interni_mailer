<?php

namespace App\Controller;

use App\Dto\EmailDto;
use App\Message\SendEmail;
use App\Service\EmailBatchDispatcherService;
use App\Service\EmailParser\EmailParserService;
use App\Service\EmailParser\EmailTemplateParserService;
use App\Service\EmailTemplateAssemblerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailController extends AbstractController
{
    #[Route('/api/send', methods: ['POST'])]
    public function send(
        EmailBatchDispatcherService $batchDispatcher,
        DecoderInterface $decoder,
        Request $request,
        ValidatorInterface $validator,
        EmailTemplateParserService $emailTemplateParser,
    ): JsonResponse
    {
        // build the dto without #[MapRequestPayload] because if only the email template name and nothing else is sent,
        // the dto is assembled from the template first, and then validated

        $data = $decoder->decode($request->getContent(), 'json');

        $emailDto = new EmailDto(
            subject: $data['subject'] ?? null,
            from: $data['from'] ?? null,
            to: $data['to'] ?? [],
            cc: $data['cc'] ?? [],
            bcc: $data['bcc'] ?? [],
            body: $data['body'] ?? null,
            bodyTemplate: $data['bodyTemplate'] ?? null,
            emailTemplate: $data['emailTemplate'] ?? null,
            variables: $data['variables'] ?? [],
        );

        if ($emailDto->getEmailTemplate()) {
            $emailDto = $emailTemplateParser->parse($emailDto);
        }

        $violations = $validator->validate($emailDto);
        if (count($violations) > 0) {
            throw new UnprocessableEntityHttpException(
                'Validation failed',
                new ValidationFailedException($emailDto, $violations)
            );
        }

        $batchDispatcher->batchSend($emailDto);

        return $this->json([
            'status' => 'success',
            'message' => 'email sent!'
        ]);
    }
}
