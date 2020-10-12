<?php

namespace Oforge\Engine\Crud\Controller\Traits;

use Doctrine\ORM\QueryBuilder;
use Exception;
use Oforge\Engine\Core\Annotation\Endpoint\AssetBundlesMode;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Core\Helper\ResponseHelper;
use Oforge\Engine\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Crud\Enum\CrudFilterComparator;
use Oforge\Engine\Crud\Enum\CrudGroupByOrder;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Trait CrudIndexActionTrait
 *
 * @package Oforge\Engine\Crud\Controller\Traits
 */
trait CrudIndexActionTrait {
    /**
     * Configuration of the filters on the index endpoint.
     *      $this->indexFilter = 'ThisClassMethodName' | function(\Doctrine\ORM\QueryBuilder $queryBuilder, array $queryValues) {
     *          // Callable or method name (of this object) with parameters (\Doctrine\ORM\QueryBuilder $queryBuilder, array $queryValues),
     *          // the queryValues parameter contains only existing and not empty query values.
     *          // If this key is contained in one of the filters configs, the filtering must be written completely (also for all other properties).
     *          // Only the first filter callable will be used, all others are ignored.
     *          ...
     *      };
     *      $this->indexFilter = [
     *          'propertyName' => [
     *              'compare' => CrudFilterComparator::..., # Default = equals
     *              'type'    => CrudFilterType::...,# Default = CrudFilterType::EQUALS
     *              'label'   => 'i18n-translated-text' | ['key' => 'label_id', 'default' => 'ID'],
     *          ],
     *      ];
     * If array is empty (not defined), is filtered based on all modelProperties with CrudFilterType::EQUALS.
     *
     * @var array<string,array>|callable|string $indexFilter
     */
    protected $indexFilter = [];
    /**
     * Configuration of the orderBy for index action.
     *      $this->indexOrderBy = [
     *          'propertyName' => CrudGroupByOrder::ASC|DESC,
     *      ];
     *
     * @var array $indexOrderBy
     */
    protected $indexOrderBy = [];
    /**
     * Entities per page value in pagination.
     *
     * @var int $indexPaginationEntityPerPage
     */
    protected $indexPaginationEntityPerPage = 10;
    /**
     * If enabled, only the properties defined in modelProperties could be grouped by.
     *
     * @var bool $strictModeWrite
     */
    protected $strictGroupByMode = true;
    /**
     * If enabled, only the properties defined in modelProperties could be filtered.
     *
     * @var bool $strictModeWrite
     */
    protected $strictFilterMode = true;
    /** @var bool $indexFilterExtended */
    private $indexFilterExtended = false;

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction(path="[/]", method=EndpointMethod::GET, assetBundles="", assetBundlesMode=AssetBundlesMode::NONE)
     */
    public function indexAction(Request $request, Response $response) {
        $queryParams = $request->getQueryParams();
        $filter      = $this->evaluateIndexFilter(ArrayHelper::get($queryParams, 'filter', []));
        $orderBy     = $this->evaluateIndexOrderBy(ArrayHelper::get($queryParams, 'order', []));
        [$offset, $limit] = $this->evaluateIndexPagination(ArrayHelper::get($queryParams, 'pagination', []), $filter);

        if ($this->indexFilterExtended) {
            $entities = $this->crudService->listByExtendedCriteria($this->model, $filter, $orderBy, $offset, $limit);
        } else {
            $entities = $this->crudService->list($this->model, $filter, $orderBy, $offset, $limit);
        }
        foreach ($entities as $index => $entity) {
            try {
                $entities[$index] = $this->prepareEntityDataArray($entity, 'index');
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception, 'crud');
            }
        }

        return ResponseHelper::json($response, $entities);
    }

    /**
     *
     */
    protected function __constructCrudIndexActionTrait() {
        if (is_callable($this->indexFilter)) {
            $this->indexFilterExtended = true;
        } elseif (is_string($this->indexFilter) && method_exists($this, $this->indexFilter)) {
            $this->indexFilter         = [$this, $this->indexFilter];
            $this->indexFilterExtended = true;
        } elseif (is_array($this->indexFilter) && !empty($this->indexFilter)) {
            foreach ($this->indexFilter as $property => $filterConfig) {
                if (ArrayHelper::get($filterConfig, 'compare') !== CrudFilterComparator::EQUALS) {
                    $this->indexFilterExtended = true;
                    break;
                }
            }
        }
        foreach ($this->indexOrderBy as $property => $direction) {
            $this->indexOrderBy[$property] = strtoupper($direction);
        }
    }

    /**
     * Evaluates filter query params.
     *
     * @param array $filterParams
     *
     * @return array
     */
    protected function evaluateIndexFilter(array $filterParams) : array {
        if (empty($filterParams)) {
            return [];
        }
        $indexFilter     = $this->indexFilter;
        $modelProperties = $this->modelProperties;
        $filter          = [];
        // remove empty params
        foreach ($filterParams as $key => $value) {
            if ($value === '' || ($this->strictFilterMode && !ArrayHelper::dotExist($modelProperties, $key))) {
                unset($filterParams[$key]);
            }
        }
        if ($this->indexFilterExtended) {
            $callable = null;
            if (is_callable($indexFilter)) {
                // wrap callable to add filter param values
                $filter = function (QueryBuilder $queryBuilder) use ($indexFilter, $filterParams) {
                    $indexFilter($queryBuilder, $filterParams);
                };
            } elseif (is_array($indexFilter) && !empty($indexFilter)) {
                $filterExtended = [];
                foreach ($indexFilter as $property => $filterConfig) {
                    if (!isset($filterParams[$property])) {
                        continue;
                    }
                    $filterValue = $filterParams[$property];
                    $comparator  = ArrayHelper::get($filterConfig, 'compare', CrudFilterComparator::EQUALS);
                    if (!CrudFilterComparator::isValid($comparator)) {
                        $comparator = CrudFilterComparator::EQUALS;
                    }
                    switch ($comparator) {
                        case CrudFilterComparator::BETWEEN:
                            if (!is_array($filterValue) || count($filterValue) !== 2#
                                || !(is_int($filterValue[0]) || is_string($filterValue[0]))#
                                || !(is_int($filterValue[1]) || is_string($filterValue[1]))#
                            ) {
                                continue 2;
                            }
                            break;
                        case CrudFilterComparator::IN:
                        case CrudFilterComparator::NOT_IN:
                            if (!is_array($filterValue)) {
                                continue 2;
                            }
                            break;
                    }
                    $filterExtended[$property] = [
                        'comparator' => $comparator,
                        'value'      => $filterValue,
                    ];
                }
                $filter = $filterExtended;
            }
        } else {
            $filterKeys = array_keys(empty($indexFilter) ? $this->modelProperties : $indexFilter);
            $filter     = ArrayHelper::extractByKey($filterParams, $filterKeys);
        }

        return $filter;
    }

    /**
     * Evaluates order query params.
     *
     * @param array $orderParams
     *
     * @return array
     */
    protected function evaluateIndexOrderBy(array $orderParams) : array {
        $modelProperties = $this->modelProperties;
        $orderBy         = $this->indexOrderBy;
        if (!empty($orderParams)) {
            $orderBy = [];
            foreach ($orderParams as $property => $direction) {
                if ($this->strictGroupByMode && !ArrayHelper::dotExist($modelProperties, $property)) {
                    continue;
                }
                $direction = strtoupper($direction);
                if (CrudGroupByOrder::isValid($direction)) {
                    $orderBy[$property] = $direction;
                }
            }
        }

        return $orderBy;
    }

    /**
     * Evaluates pagination query params.
     *
     * @param array $paginationParams
     * @param array|callable $filter
     *
     * @return array
     */
    protected function evaluateIndexPagination(array $paginationParams, $filter) {
        $offset = null;
        $limit  = $this->indexPaginationEntityPerPage;
        if (!empty($paginationParams)) {
            if (isset($paginationParams['offset']) || isset($paginationParams['limit'])) {
                $offset = ArrayHelper::get($paginationParams, 'offset', null);
                $limit  = ArrayHelper::get($paginationParams, 'limit', $limit);
            } else {
                $limit = ArrayHelper::get($paginationParams, 'entityPerPage', $limit);
                if ($this->indexFilterExtended) {
                    try {
                        $itemsCount = $this->crudService->countByExtendedCriteria($this->model, $filter);
                    } catch (Exception $exception) {
                        Oforge()->Logger()->logException($exception, 'crud');
                        $itemsCount = 0;
                    }
                } else {
                    $itemsCount = $this->crudService->count($this->model, $filter);
                }
                if ($itemsCount > 0) {
                    $currentPage = ArrayHelper::get($paginationParams, 'page', 1);
                    if ($currentPage > 1) {
                        $offset = ($currentPage - 1) * $limit;
                    }
                }
            }
        }

        return [$offset, $limit];
    }

}
