<?php

namespace App\Controller;

use App\Dto\EmailVariableDto;
use App\Repository\EmailVariableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class EmailVariableController extends AbstractController
{
    #[Route('/api/email-variable', methods: ['POST'])]
    public function createVariable(
        EmailVariableRepository $emailVariableRepository,
        #[MapRequestPayload] EmailVariableDto $emailVariableDto
    ): JsonResponse
    {
        $variable = $emailVariableRepository->createEmailVariable($emailVariableDto);

        return $this->json([
            'status' => 'success',
            'message' => 'variable created!',
            'data' => [
                'variable' => $variable,
            ]
        ], 201);
    }
}
