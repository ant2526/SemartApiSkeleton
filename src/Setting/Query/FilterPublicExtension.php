<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Setting\Query;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class FilterPublicExtension extends AbstractQueryExtension
{
    public function apply(QueryBuilder $queryBuilder, Request $request): void
    {
        if (false === strpos($request->getPathInfo(), '/settings/public')) {
            return;
        }

        $queryBuilder->andWhere($queryBuilder->expr()->eq(sprintf('%s.public', $this->aliasHelper->findAlias('root')), $queryBuilder->expr()->literal(true)));
    }
}
