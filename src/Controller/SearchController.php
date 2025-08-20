<?php

namespace App\Controller;

use App\Dto\SearchCriteria\AddressSearchCriteria;
use App\Service\Search\AddressSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/api/search/addresses', methods: ['GET'])]
    public function searchAddresses(
        #[MapQueryString]
        AddressSearchCriteria $addressSearchCriteria,
        AddressSearchService $addressSearchService
    ): JsonResponse
    {
        $results = $addressSearchService->search($addressSearchCriteria);

        return $this->json([
            'status' => 'success',
            'numResults' => count($results),
            'data' => [
                'results' => $results,
            ]
        ]);
    }
}
