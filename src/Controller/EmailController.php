<?php

namespace App\Controller;

use App\Dto\EmailDto;
use App\Message\SendEmail;
use App\Service\EmailParser\EmailParserService;
use App\Service\EmailTemplateAssemblerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class EmailController extends AbstractController
{
    #[Route('/api/send', methods: ['POST'])]
    public function send(
        MessageBusInterface $messageBus,
        EmailParserService $parser,
        #[MapRequestPayload]
        EmailDto $emailDto,
    ): JsonResponse
    {
        $emailDtosToSend = $parser->parse($emailDto);
        foreach ($emailDtosToSend as $email) {
            $messageBus->dispatch(new SendEmail($email));
        }

        return $this->json([
            'status' => 'success',
            'message' => 'email sent!'
        ]);
    }

    #[Route('/api/{templateName}/send', methods: ['POST'])]
    public function sendFromTemplate(
        string $templateName,
        EmailTemplateAssemblerService $templateAssembler,
        MessageBusInterface $messageBus,
        EmailParserService $parser,
        Request $request,
        DecoderInterface $decoder
    ): JsonResponse
    {
        $valuesToChange = $decoder->decode($request->getContent(), 'json');

        $emailDto = $parser->parse(
            $templateAssembler->createEmailFromTemplate($templateName,  $valuesToChange)
        );

        $messageBus->dispatch(new SendEmail($emailDto));

        return $this->json([
            'status' => 'success',
            'message' => 'email sent!'
        ]);
    }
}
