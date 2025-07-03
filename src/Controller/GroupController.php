<?php

namespace App\Controller;

use App\Dto\GroupDto;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Service\GroupManager\GroupManagerException;
use App\Service\GroupManager\GroupManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class GroupController extends AbstractController
{
    #[Route('/api/groups', methods: ['POST'])]
    public function createGroup(
        GroupRepository  $groupRepository,
        UserRepository $userRepository,
        #[MapRequestPayload]
        GroupDto $groupDto,
    ): JsonResponse
    {
        $users = $userRepository->getUsersFromEmailList($groupDto->getRecipients());
        $groupDto->setRecipients($users);
        $group = $groupRepository->createGroup($groupDto);

        return $this->json([
            'status' => 'success',
            'message' => 'Group created successfully!',
            'data' => [
                'group' => $group,
            ]
        ], context: [ 'groups' => 'groupData' ]);
    }

    #[Route('/api/groups/{groupId}', methods: ['POST'])]
    public function addUserToGroup(
        int $groupId,
        GroupManagerService $groupManagerService,
        DecoderInterface $decoder,
        Request $req
    ): JsonResponse
    {
        $userId = $decoder->decode($req->getContent(), 'json')['userId'];

        try {
            $group = $groupManagerService->addUserToGroup($groupId, $userId);
        }
        catch (GroupManagerException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        return $this->json([
            'status' => 'success',
            'message' => 'User added to group!',
            'data' => [
                'group' => $group,
            ]
        ], context: [ 'groups' => 'groupData' ]);
    }

    #[Route('/api/groups/{groupId}/users/{userId}', methods: ['DELETE'])]
    public function removeUserFromGroup(
        int $groupId,
        int $userId,
        GroupManagerService $groupManagerService
    ): JsonResponse
    {
        try {
            $group = $groupManagerService->removeUserFromGroup($groupId, $userId);
        }
        catch (GroupManagerException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        return $this->json([
            'status' => 'success',
            'message' => 'User removed from group!',
            'data' => [
                'group' => $group,
            ]
        ], context: [ 'groups' => 'groupData' ]);
    }
}
