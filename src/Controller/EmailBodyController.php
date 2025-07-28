<?php

namespace App\Controller;

use App\Dto\EmailBodyDto;
use App\Dto\SearchCriteria\BodyTemplateSearchCriteria;
use App\Message\CreateBodyTemplateFile;
use App\Repository\EmailBodyRepository;
use App\Service\EmailParser\BodyParser\BodyParserService;
use App\Service\Search\BodyTemplateSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class EmailBodyController extends AbstractController
{
    #[Route('/api/email-body', methods: ['GET'])]
    public function getBodyTemplates(
        BodyTemplateSearchService $bodyTemplateSearchService,
        #[MapQueryString]
        BodyTemplateSearchCriteria $criteria
    ): JsonResponse
    {
        $templates = $bodyTemplateSearchService->searchByCriteria($criteria);

        return $this->json([
            'status' => 'success',
            'results' => count($templates),
            'data' => [
                'templates' => $templates,
            ]
        ]);
    }

    #[Route('/api/email-body', methods: ['POST'])]
    public function createBodyTemplate(
        EmailBodyRepository $emailBodyRepository,
        MessageBusInterface $messageBus,
        #[MapRequestPayload]
        EmailBodyDto $emailBodyDto
    ): JsonResponse
    {
        $body = $emailBodyRepository->createEmailBody($emailBodyDto);

        $messageBus->dispatch(new CreateBodyTemplateFile($emailBodyDto));

        return $this->json([
            'status' => 'success',
            'message' => 'Email body template created successfully!',
            'data' => [
                'body' => $body,
            ]
        ], 201);
    }

    // called when the 'Test send' button is clicked on the frontend
    // to render any changes made to the template in the editor
    #[Route('/api/email-body/render', methods: ['POST'])]
    public function liveRenderBodyTemplate(
        DecoderInterface $decoder,
        Request $request,
        BodyParserService $bodyParser
    ): JsonResponse
    {
        $reqData = $decoder->decode($request->getContent(), 'json');
        $body = $reqData['body'];
        $extension = $reqData['extension'];

        $parsedBody = $bodyParser->parse($body, $extension);

        return $this->json([
            'status' => 'success',
            'data' => [
                'body' => $parsedBody
            ]
        ]);
    }
}
