<?php

namespace Fias;


class DbQuery
{
    /** @var array */
    protected $fields = [];

    /** @var array */
    protected $filter = [];

    /** @var string */
    protected $sort;

    /** @var string */
    protected $order = 'ASC';

    /** @var array */
    protected $orders = [];

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var int */
    protected $totalCount;

    /** @var string */
    private $countSql;

    /** @var string */
    protected $tableName;

    /**
     * return $this;
     *
     * @param array $filter
     */
    public function __construct(array $filter = [])
    {
        $this->setFilter($filter);

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $filter
     *
     * @return $this
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    public function addFilter($field, $value)
    {
        $this->filter[$field] = $value;

        return $this;
    }

    /**
     * @param string $field_name
     *
     * @return array
     */
    public function getFilter($field_name = null)
    {
        if ($field_name) {
            return $this->filter[$field_name];
        } else {
            return $this->filter;
        }
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $page
     */
    public function setOffset($page)
    {
        $this->offset = (int)$page;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string ASC|DESC
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $order ASC|DESC
     */
    public function setOrder($order = 'ASC')
    {
        $this->order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
    }

    /**
     * @return array
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param array $orders
     *
     * @return DbQuery
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * @param int $count
     */
    public function setCountTotal($count)
    {
        $this->totalCount = (int)$count;
    }

    /**
     * @return int
     */
    public function getCountTotal()
    {
        return $this->totalCount;
    }

    /**
     * @return array
     */
    public function asParameters()
    {
        $arData = [];

        if ($this->getFields()) {
            $arData['select'] = $this->getFields();
        }
        if ($this->getFilter()) {
            $arData['filter'] = $this->getFilter();
        }
        if (count($this->getOrders()) > 0) {
            $arData['order'] = $this->getOrders();
        } elseif ($this->getSort()) {
            $arData['order'] = [$this->getSort() => $this->getOrder()];
        }
        if ($this->getLimit()) {
            $arData['limit'] = $this->getLimit();
        }
        if ($this->getOffset()) {
            $arData['offset'] = $this->getOffset();
        }

        return $arData;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function build()
    {
        $arQuery = $this->asParameters();

        if (!isset($arQuery['select'])) {
            $arQuery = array_merge(['select' => ["*"]], $arQuery);
        }

        $arSql = [];
        foreach ($arQuery as $param => $list) {
            switch ($param) {
                case 'select':
                    $arSql['select'] = 'SELECT ' . implode(", ", $list);
                    $arSql['from'] = 'FROM ' . $this->tableName;
                    break;
                case 'filter':
                    $where = [];

                    if (count($list)) {
                        foreach ($list as $param) {
                            $arSql['where'] = 'WHERE ' . implode(" \nAND ", $list);
                        }
                    }
                    break;
                case 'count_total':
                    $tmpSql = [
                        'select' => "SELECT 1 cntholder",
                        'from' => $arSql['from'],
                        'where' => $arSql['where'],
                    ];
                    $countSql = sprintf(
                        "
                        SELECT COUNT(cntholder) AS TMP_ROWS_CNT
                        FROM (%s) xxx
                        ",
                        implode("\n", $tmpSql)
                    );

                    break;
                case 'order';
                    $order = [];
                    foreach ($list as $sort => $by) {
                        $order[] = implode(" ", [$sort, $by]);
                    }
                    $arSql['order'] = 'ORDER BY ' . implode(", ", $order);
                    break;
                case 'limit':
                    $arSql['limit'] = 'LIMIT ' . $list;
                    break;
                case 'offset':
                    $arSql['offset'] = 'OFFSET ' . $list;
                    break;
                default:
                    throw new \Exception("Unknown parameter: " . $param);
            }
        }

        /* сортируем в нужном порядке, иначе могут быть расхождения */
        $arSql = [
            'select' => $arSql['select'],
            'from' => $arSql['from'],
            'where' => $arSql['where'],
            'order' => $arSql['order'],
            'limit' => $arSql['limit'],
            'offset' => $arSql['offset']
        ];

        $sql = implode("\n", $arSql);
        print_r($arQuery);
        print_r($sql);
        return $sql;
    }

    /**
     * @return string
     */
    public function getCountSql(): string
    {
        return $this->countSql;
    }

    /**
     * @param string $countSql
     */
    public function setCountSql(string $countSql): void
    {
        $this->countSql = $countSql;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }
}
