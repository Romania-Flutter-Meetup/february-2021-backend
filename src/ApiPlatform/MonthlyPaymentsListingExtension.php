<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Apartments;
use App\Entity\MonthlyPayments;
use App\Entity\Users;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class MonthlyPaymentsListingExtension implements QueryCollectionExtensionInterface
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
        if ($resourceClass !== MonthlyPayments::class) return;
        $rootAlias = $queryBuilder->getRootAliases()[0];
        if (null !== $this->security->getUser()) {
            /** @var Users $user */
            $user = $this->security->getUser();
            $queryBuilder->leftJoin(Apartments::class,'aprt','WITH',$rootAlias.'.apartment = aprt.id')
                ->andWhere('aprt.user = ' .$user->getId());
            $queryBuilder->orderBy($rootAlias.'.createdAt', 'ASC');
        }
    }
}