<?php

namespace App\Service\EmailParser;

use App\Entity\Group;
use App\Repository\GroupRepository;

class GroupResolverService
{
    private array $groups;

    public function __construct(
        private GroupRepository $groupRepository,
    ) {
        $this->groups = $this->groupRepository->getAllGroups();
    }

    // checks if any of the addresses in the email dto are actually group addresses
    // if they are, it replaces the group address with all the recipient addresses for that group
    // if not, it just returns the address list unchanged
    public function resolveGroupAddresses(array $addressList): array
    {
        $finalAddressList = [];

        $groupAddresses = $this->getGroupAddresses();
        foreach ($addressList as $address) {
            if (in_array($address, $groupAddresses)) {
                $groupMembers = $this->getGroup($address)->getUsers();

                $memberAddresses = [];
                foreach ($groupMembers as $groupMember) {
                    $memberAddresses[] = $groupMember->getEmail();
                }

                array_push($finalAddressList, ...$memberAddresses);
            }
            else {
                $finalAddressList[] = $address;
            }
        }

        return $finalAddressList;
    }

    // gets the addresses of all the groups in the db
    private function getGroupAddresses(): array
    {
        $addresses = [];

        foreach ($this->groups as $group) {
            $addresses[] = $group->getAddress();
        }

        return $addresses;
    }

    // gets the specific group by the address
    public function getGroup(string $address): ?Group
    {
        $group = null;

        foreach ($this->groups as $currGroup) {
            if ($currGroup->getAddress() === $address) {
                $group = $currGroup;
            }
        }

        return $group;
    }
}
