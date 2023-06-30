<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Repository;

use App\Common\Domain\Enum\Search;
use App\Common\Domain\ValueObject\BooleanString;
use App\Common\Domain\ValueObject\DateString;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

abstract class AbstractEntityRepository extends ServiceEntityRepository
{
    public const SMALLINT_MAX_VALUE = 32767;

    public function createQueryBuilder(
        mixed $alias,
        mixed $indexBy = null,
        string $searchText = null,
        ?array $searchableFields = null
    ): QueryBuilder {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder()
            ->select($alias)
            ->from($this->getEntityName(), $alias, $indexBy);

        if (!empty($searchText)) {
            $this->addSearchClause($queryBuilder, $searchText, $searchableFields);
        }

        return $queryBuilder;
    }

    private function addSearchClause(QueryBuilder $queryBuilder, string $query, ?array $searchableFields = null): void
    {
        $lowercaseQuery = mb_strtolower($query);
        $isNumericQuery = is_numeric($query);
        $isUuidQuery = Uuid::isValid($query);
        $queryDateString = new DateString($query);
        $queryBooleanString = new BooleanString($query);

        $dqlParameters = [
            'numeric_query' => is_numeric($query) ? 0 + $query : false,
            'uuid_query' => $query,
            'text_query' => '%' . trim($lowercaseQuery) . '%',
            'words_query' => array_filter(explode(' ', $lowercaseQuery)),
            'date_query' => $queryDateString->isValid() ? $queryDateString->getDateTime() : false,
            'boolean_query' => $queryBooleanString->isValid() ? $queryBooleanString->getValue() : null,
        ];

        $searches = [];

        $fields = $searchableFields ?? $this->getSearchableFields();

        foreach ($fields as $propertyName => $propertyType) {
            if ($isNumericQuery && $propertyType === Search::SEARCH_NUMERIC) {
                $searches[] = sprintf('%s = :query_for_numbers', $propertyName);
                $queryBuilder->setParameter('query_for_numbers', $dqlParameters['numeric_query']);
            } elseif ($isUuidQuery && $propertyType === Search::SEARCH_UUID) {
                $searches[] = sprintf('%s = :query_for_uuids', $propertyName);
                $queryBuilder->setParameter('query_for_uuids', $dqlParameters['uuid_query'], 'uuid');
            } elseif ($queryDateString->isValid() && $propertyType === Search::SEARCH_DATE) {
                $searches[] = sprintf('%s = :query_for_dates', $propertyName);
                $queryBuilder->setParameter('query_for_dates', $dqlParameters['date_query']);
            } elseif ($queryBooleanString->isValid() && $propertyType === Search::SEARCH_BOOLEAN) {
                $searches[] = sprintf('%s = :query_for_booleans', $propertyName);
                $queryBuilder->setParameter('query_for_booleans', $dqlParameters['boolean_query']);
            } elseif ($queryDateString->isValid() && $propertyType === Search::SEARCH_DATE_DAY) {
                $searches[] = sprintf(
                    '(%s >= :query_for_dates_begin AND %s <= :query_for_dates_end)',
                    $propertyName,
                    $propertyName
                );
                $queryBuilder->setParameter('query_for_dates_begin', $queryDateString->getStartOfDay());
                $queryBuilder->setParameter('query_for_dates_end', $queryDateString->getEndOfDay());
            } elseif ($propertyType === Search::SEARCH_TEXT) {
                $searches[] = sprintf('LOWER(%s) LIKE :query_for_text', $propertyName);
                $searches[] = sprintf('LOWER(%s) IN (:query_as_words)', $propertyName);
                $queryBuilder
                    ->setParameter('query_for_text', $dqlParameters['text_query'])
                    ->setParameter('query_as_words', $dqlParameters['words_query']);
            } elseif (
                $isNumericQuery && $propertyType === Search::SEARCH_SMALLINT
                && $dqlParameters['numeric_query'] <= self::SMALLINT_MAX_VALUE
            ) {
                $searches[] = sprintf('%s = :query_for_numbers', $propertyName);
                $queryBuilder->setParameter('query_for_numbers', $dqlParameters['numeric_query']);
            }
        }

        $queryBuilder->andWhere($queryBuilder->expr()->orX(...$searches));
    }

    abstract protected function getSearchableFields(): array;
}
