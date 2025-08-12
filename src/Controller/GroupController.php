<?php

namespace App\Controller;

use App\Dto\GroupDto;
use App\Dto\SearchCriteria\GroupSearchCriteria;
use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Service\GroupManager\GroupManagerException;
use App\Service\GroupManager\GroupManagerService;
use App\Service\Search\GroupSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class GroupController extends AbstractController
{
    #[Route('/api/groups', methods: ['GET'])]
    public function getAllGroups(
        #[MapQueryString]
        GroupSearchCriteria $criteria,
        GroupSearchService $searchService,
    ): JsonResponse
    {
        $groups = $searchService->searchByCriteria($criteria);

        return $this->json([
            'status' => 'success',
            'results' => count($groups),
            'data' => [
                'groups' => $groups
            ]
        ], context: [ 'groups' => 'groupData' ]);
    }

    #[Route('/api/groups', methods: ['POST'])]
    public function createGroup(
        GroupRepository  $groupRepository,
        UserRepository $userRepository,
        #[MapRequestPayload]
        GroupDto $groupDto,
    ): JsonResponse
    {
        $users = $userRepository->getUsersFromList($groupDto->getRecipients());
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

    #[Route('/api/groups/{groupId}/users', methods: ['POST'])]
    public function addUserToGroup(
        int $groupId,
        GroupManagerService $groupManagerService,
        DecoderInterface $decoder,
        Request $req
    ): JsonResponse
    {
        $userId = $decoder->decode($req->getContent(), 'json')['userId'];
        $group = $groupManagerService->addUserToGroup($groupId, $userId);

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
        $group = $groupManagerService->removeUserFromGroup($groupId, $userId);

        return $this->json([
            'status' => 'success',
            'message' => 'User removed from group!',
            'data' => [
                'group' => $group,
            ]
        ], context: [ 'groups' => 'groupData' ]);
    }

    #[Route('/api/groups/{id}', methods: ['DELETE'])]
    public function deleteGroup(
        Group $group,
        GroupRepository $groupRepository,
    ): JsonResponse
    {
        $groupRepository->deleteGroup($group);

        return $this->json([], 204);
    }
}
