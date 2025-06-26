<?php

namespace App\Controller;

use App\Dto\GroupDto;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class GroupController extends AbstractController
{
    #[Route('/api/groups', methods: ['POST'])]
    public function createGroup(
        GroupRepository  $groupRepository,
        #[MapRequestPayload]
        GroupDto $groupDto,
    ): JsonResponse
    {
        $group = $groupRepository->createGroup($groupDto);

        return $this->json([
            'status' => 'success',
            'message' => 'Group created successfully!',
            'data' => [
                'group' => $group,
            ]
        ]);
    }
}
