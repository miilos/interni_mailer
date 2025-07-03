<?php

namespace App\Service\GroupManager;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;

class GroupManagerService
{
    public function __construct(
        private GroupRepository $groupRepository,
        private UserRepository $userRepository,
    ) {}

    public function addUserToGroup(int $groupId, int $userId): Group
    {
        $group = $this->getGroup($groupId);
        $user = $this->getUser($userId);

        return $this->groupRepository->addUserToGroup($group, $user);
    }

    public function removeUserFromGroup(int $groupId, int $userId): Group
    {
        $group = $this->getGroup($groupId);
        $user = $this->getUser($userId);

        return $this->groupRepository->removeUserFromGroup($group, $user);
    }

    private function getGroup(int $groupId): Group
    {
        $group = $this->groupRepository->find([ 'id' => $groupId ]);

        if (!$group) {
            throw new GroupManagerException('Group not found!');
        }

        return $group;
    }

    private function getUser(int $userId): User
    {
        $user = $this->userRepository->find([ 'id' => $userId ]);

        if (!$user) {
            throw new GroupManagerException('User not found!');
        }

        return $user;
    }
}
