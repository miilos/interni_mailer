<?php

namespace App\Controller;

use App\Dto\SearchCriteria\UserSearchCriteria;
use App\Service\Search\UserSearchService;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/api/users', methods: ['GET'])]
    public function getAllUsers(
        UserSearchService $userSearchService,
        #[MapQueryString]
        UserSearchCriteria $criteria
    ): JsonResponse
    {
        $users = $userSearchService->searchByCriteria($criteria);

        return $this->json([
            'status' => 'success',
            'results' => count($users),
            'data' => [
                'users' => $users,
            ]
        ], context: ['groups' => 'userData']);
    }
}
