<?php

namespace App\Controller;

use App\Dto\EmailDto;
use App\Event\SendEmailEvent;
use App\Service\EmailTemplateAssemblerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class EmailController extends AbstractController
{
    #[Route('/api/send', methods: ['POST'])]
    public function send(
        EventDispatcherInterface $dispatcher,
        #[MapRequestPayload]
        EmailDto $emailDto,
    ): JsonResponse
    {
        $dispatcher->dispatch(new SendEmailEvent($emailDto));

        return $this->json([
            'status' => 'success',
            'message' => 'email sent!'
        ]);
    }

    #[Route('/api/{templateName}/send', methods: ['POST'])]
    public function sendFromTemplate(
        string $templateName,
        EmailTemplateAssemblerService $templateAssembler,
        EventDispatcherInterface $dispatcher,
        Request $request,
        DecoderInterface $serializer
    ): JsonResponse
    {
        $valuesToChange = $serializer->decode($request->getContent(), 'json');

        $emailDto = $templateAssembler->createEmailFromTemplate($templateName,  $valuesToChange);

        $dispatcher->dispatch(new SendEmailEvent($emailDto));

        return $this->json([
            'status' => 'success',
            'message' => 'email sent!'
        ]);
    }
}
