<?php

namespace App\Service\Search;

use App\Dto\AddressSearchResultDto;
use App\Dto\SearchCriteria\AddressSearchCriteria;
use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;

class AddressSearchService
{
    public function __construct(
        private UserRepository $userRepository,
        private GroupRepository $groupRepository,
    ) {}

    /**
     * @return AddressSearchResultDto[]
     */
    public function search(AddressSearchCriteria $criteria): array
    {
        $users = $this->userRepository->getUsersByEmail($criteria->getEmail());
        $groups = $this->groupRepository->getGroupsByAddress($criteria->getEmail());
        $resEntities = array_merge($users, $groups);

        $resDtos = [];
        foreach ($resEntities as $entity) {
            if ($entity instanceof User) {
                $resDtos[] = new AddressSearchResultDto(
                    type: 'user',
                    address: $entity->getEmail(),
                    name: $entity->getFirstname() . ' ' . $entity->getLastname(),
                );
            }
            elseif ($entity instanceof Group) {
                $resDtos[] = new AddressSearchResultDto(
                    type: 'group',
                    address: $entity->getAddress(),
                    name: $entity->getName(),
                );
            }
        }

        return $resDtos;
    }
}
