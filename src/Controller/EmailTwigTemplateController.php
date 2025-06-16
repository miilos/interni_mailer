<?php

namespace App\Controller;

use App\Dto\EmailTwigTemplateDto;
use App\Repository\EmailTwigTemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class EmailTwigTemplateController extends AbstractController
{
    #[Route('/api/twig-templates', methods: ['POST'])]
    public function createTwigTemplate(
        EmailTwigTemplateRepository $emailTwigTemplateRepository,
        #[MapRequestPayload]
        EmailTwigTemplateDto $twigTemplateDto,
    ): JsonResponse
    {
        $twigTemplate = $emailTwigTemplateRepository->createTemplate($twigTemplateDto);

        return $this->json([
            'status' => 'success',
            'data' => [
                'twigTemplate' => $twigTemplate,
            ]
        ]);
    }
}
