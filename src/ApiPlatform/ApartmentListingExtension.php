<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Apartments;
use App\Entity\Users;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class ApartmentListingExtension implements QueryCollectionExtensionInterface
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($resourceClass !== Apartments::class) return;
        $rootAlias = $queryBuilder->getRootAliases()[0];
        if (null !== $this->security->getUser()) {
            /** @var Users $user */
            $user = $this->security->getUser();
            if ($user->getRoles()[0] === 'ROLE_ADMIN') {
                /** @var Users $user */
                $queryBuilder->orderBy($rootAlias.'.entry', 'ASC');
            } else {
                $queryBuilder->andWhere($rootAlias.'.user = ' .$user->getId());
            }
        }
    }
}