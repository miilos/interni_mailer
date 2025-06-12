<?php

namespace App\Controller;

use App\Command\SendEmailCommand;
use App\Dto\EmailDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class EmailController extends AbstractController
{
    #[Route('/api/send', methods: ['POST'])]
    public function send(
        Request $request,
        SendEmailCommand $sendEmailCommand,
        #[MapRequestPayload]
        EmailDto $emailDto,
    ): JsonResponse
    {
        $template = $request->get('template');

        if (!$template) {
            $sendEmailCommand->setEmail($emailDto);
            $sendEmailCommand->execute();
        }

        return $this->json([
            'status' => 'success',
            'message' => 'email sent!'
        ]);
    }
}
