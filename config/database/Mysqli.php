<?php

namespace Database;

use Database\Sqli;

class Mysqli
{
    protected $sqli;
    public $table;
    public $tableAlias;
    public $values;
    public $columns;
    public $visible;
    public $fillable;
    public $dates;
    public $softDelete;

    public $having;

    public $join;
    public $with;
    public $where;
    public $orderBy;
    public $groupBy;
    public $limit;
    public $subQuery;
    public $debug;
    public $tableName;
    public $ASC = '';
    public $offset;


    function __construct()
    {
        $this->sqli = (isset($GLOBALS['Sqli'])) ? $GLOBALS['Sqli'] : new Sqli();
        $this->init();
    }

    function init()
    {
        $this->table = '';
        $this->tableAlias = null;
        $this->values = [];
        $this->columns = [];
        $this->visible = [];
        $this->fillable = [];
        $this->dates = [];
        $this->softDelete = false;
        $this->join = [];
        $this->with = [];
        $this->where = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->limit = [];
        $this->subQuery = [];
        $this->debug = false;
    }

    public static function instance()
    {
        return new Mysqli();
    }

    /*
     * Getters
     */

    function get(
        $conditions = null
    ) {
        if (is_null($conditions)) {
            return [];
        }

        if (!is_array($conditions)) {
            $conditions = ['id' => $conditions];
        }

        if ($this->softDelete) {
            $conditions['deleted_at : IS'] = null;
        }

        return $this->where($conditions)->first();
    }

    function getAll(
        array $conditions = []
    ) {
        if ($this->softDelete) {
            $conditions['deleted_at : IS'] = null;
        }
        return $this->where($conditions)->list();
    }

    function getGrouped(
        string $column,
        array $conditions = []
    ) {
        if ($this->softDelete) {
            $conditions['deleted_at : IS'] = null;
        }
        return $this->where($conditions)->groupId($column);
    }

    function with(array $tables)
    {
        $this->with = $tables;
        return $this;
    }

    /*
     * Setters
     */

    function table($table)
    {
        if (is_array($table)) {
            $this->tableAlias = key($table);
            $this->tableName = current($table);
            $this->table = $this->tableName . ' ' . $this->tableAlias;
        } else {
            $this->table = $this->tableName = $table;
        }
        return $this;
    }

    function dates($dates = [])
    {
        if (!is_array($dates)) {
            $dates = [$dates];
        }
        $this->dates = $dates;
        return $this;
    }

    function columns($columns = [])
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }

    function fillable($columns = [])
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->fillable = array_merge($this->fillable, $columns);
        return $this;
    }

    function visible($columns = [])
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        foreach ($columns as $column) {
            if (preg_match('/([a-z_]+)$/i', $column, $match)) {
                $this->visible[$match[0]] = $column;
            }
        }

        return $this;
    }

    function values($values = [])
    {
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    function where($conditions = [])
    {
        $this->where[] = ['operator' => 'AND', 'conditions' => $conditions];
        return $this;
    }

    function orWhere($conditions = [])
    {
        $this->where[] = ['operator' => 'OR', 'conditions' => $conditions];
        return $this;
    }

    function orderBy($orderBy = [])
    {
        if (!is_array($orderBy)) {
            $orderBy = [$orderBy];
        }
        $this->orderBy = array_merge($this->orderBy, $orderBy);
        return $this;
    }

    function groupBy($groupBy = [])
    {
        if (!is_array($groupBy)) {
            $groupBy = [$groupBy];
        }
        $this->groupBy = array_merge($this->groupBy, $groupBy);
        return $this;
    }

    function having($having = '')
    {
        $this->having = $having;
        return $this;
    }

    function limit(int $limit = 0, int $offset = 0): Mysqli
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }
    function ASC(): Mysqli
    {
        $this->ASC = 'ASC';
        return $this;
    }

    function offset(int $offset = 0): Mysqli
    {
        $this->offset = $offset;
        return $this;
    }

    function join($join = [])
    {
        $this->join = $join;
        return $this;
    }

    function subQuery($key = 'id', $query = '')
    {
        $this->subQuery[$key] = $query;
        return $this;
    }

    function debug()
    {
        $this->sqli->debug = $this->debug = true;
        return $this;
    }

    /*
     * Assemble query
     */

     
    function makeSelectQuery2()
    {
        $sql = sprintf(
            'SELECT EXISTS (SELECT * FROM %s %s %s %s %s %s)',
            $this->table,
            $this->getJoin(),
            $this->getWhere(),
            $this->getGroup(),
            $this->getOrder(),
            $this->getLimit()
        );

        return trim($sql);
    }

    function makeSelectQuery()
    {
        $sql = sprintf(
            'SELECT %s FROM %s %s %s %s %s %s',
            $this->getColumns(),
            $this->table,
            $this->getJoin(),
            $this->getWhere(),
            $this->getGroup(),
            $this->getOrder(),
            $this->getLimit()
        );

        return trim($sql);
    }

    function getValues()
    {
        $values = $this->values;

        foreach ($values as $column => $value) {
            if ($this->fillable && !in_array($column, $this->fillable)) {
                unset($values[$column]);
                continue;
            }
            if (!in_array($value, ['NOW()', 'CURDATE()'])) {
                if (!is_null($value)) {
                    $value = $this->sqli->val($value);
                } else {
                    $value = 'null';
                }
            }
            $values[$column] = $column . '=' . $value;
        }

        if (count($values)) {
            return 'SET ' . implode(', ', array_unique($values));
        }
    }

    function getColumns()
    {
        $columns = $this->columns;

        if (!count($columns)) {
            $columns = array_keys($this->visible);
        }

        if (!count($columns) && !count($this->visible)) {
            $columns = ['*'];
            if (count($this->join) > 0) {
                foreach ($this->join as $table => $join) {
                    $columns[] = $table . '.*';
                }
            }
        }

        foreach ($columns as $key => $value) {
            if (count($this->visible)) {
                if (isset($this->visible[$value])) {
                    $columns[$key] = $this->visible[$value];
                } else {
                    unset($columns[$key]);
                }
                continue;
            }
            if (!strpos($value, '.') && !strpos($value, '(')) {
                $columns[$key] = ($this->tableAlias ?? $this->table) . '.' . $value;
            }
        }

        return implode(', ', array_unique($columns));
    }

    function getJoin()
    {
        if (empty($this->join)) {
            return false;
        }
        $joins = [];
        foreach ($this->join as $table => $join) {
            $joins[] = $join;
        }
        return implode(' ', $joins);
    }

    function getWhere()
    {
        if (!$this->where) {
            return false;
        }

        foreach ($this->where as $n => $where) {

            $filter = [];
            foreach ($where['conditions'] as $key => $value) {
                if (!is_array($value)) {


                    unset($operator);

                    if (strpos($key, ':')) {                 
                        $arg = explode(':', $key);
                        if (count($arg) == 2) {
                            $key = trim($arg[0]);
                            $operator = strtoupper(trim($arg[1]));
                
}
                    }

                    if (!isset($operator)) {
                        $operator = '=';
                    }

                    if (in_array($operator, ['=', '>=', '<=', '>', '<', 'LIKE', 'NOT LIKE', '!=', 'IS', 'IS NOT'])) {
                        if (!preg_match('/^[a-z_]+\(.*\)$/i', $value) && !is_null($value)) {
                            $value = $this->sqli->val($value);
                        }                               

                    } else {
                        $operator = '=';
                    }

                    if (is_null($value)) {
                        $value = 'null';
                    }

                    $filter[] = (!strpos($key, '.') ? ($this->tableAlias ?? $this->table) . '.' : '') . $key . ' ' . $operator . ' ' . $value;

                } else {

                    $filter[] = (!strpos($key, '.') ? ($this->tableAlias ?? $this->table) . '.' : '') . $key . ' IN (' . $this->sqli->implodeVal($value) . ')';

                }
            }

            if (!empty($this->subQuery)) {
                foreach ($this->subQuery as $key => $query) {
                    $filter[] = ($this->tableAlias ?? $this->table) . '.' . $key . ' IN (' . $query . ')';
                }
            }

            if (count($filter) > 0) {
                $filterSet[] = ($n > 0 ? $where['operator'] : '') . ' (' . implode(' AND ', $filter) . ')';
            }
        }

        if (count($filterSet)) {
            return 'WHERE ' . implode(' ', $filterSet);
        }
    }

    function getGroup()
    {
        if (empty($this->groupBy)) {
            return false;
        }
        $str = 'GROUP BY ' . implode(', ', $this->groupBy);
        if (!empty($this->having)) {
            $having = (array)$this->having;
            $str .= ' HAVING ' . implode(', ', $having);
        }
        return $str;
    }

    function getOrder()
    {
        if (empty($this->orderBy)) {
            return false;
        }
        return 'ORDER BY ' . implode(', ', $this->orderBy).' '.$this->ASC;

    }

    function getLimit()
    {
        if (empty($this->limit)) {
            return false;
        }
        return 'LIMIT ' . (isset($this->offset) ? $this->offset . ',' : '') . $this->limit;
    }

    /*
     * Create / replace
     */

    function create()
    {
        if (!count($this->values)) {
            return false;
        }
        if (count($this->dates) && in_array('created_at', $this->dates)) {
            $this->values(['created_at' => 'NOW()']);
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('INSERT INTO %s %s', $this->tableName, $this->getValues());
        $this->sqli->query($sql);

        return ['id' => $this->sqli->insert_id() ?? null];
    }

    function replace()
    {
        if (!count($this->values)) {
            return false;
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('REPLACE INTO %s %s', $this->table, $this->getValues());
        $this->sqli->query($sql);

        return ['id' => $this->sqli->insert_id()];
    }

    /*
     * Update
     */

    function updateById(Int $id, array $values = [])
    {
        if (count($values)) {
            $this->values($values);
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('UPDATE %s %s WHERE id=%d', $this->table, $this->getValues(), $id);
        $this->sqli->query($sql);

        return ['affected_rows' => $this->sqli->affected_rows()];
    }

    function update($where = false)
    {
        if ($this->dates && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }

        if ($where) {
            if (is_array($where)) {
                $this->where($where);
            } else {
                $this->where(['id' => $where]);
            }
        }
        $sql = sprintf('UPDATE %s %s %s', $this->table, $this->getValues(), $this->getWhere());
        $this->sqli->query($sql);

        return ['affected_rows' => $this->sqli->affected_rows()];
    }

    /*
     * Delete
     */

    function delete()
    {
        $sql = sprintf('DELETE FROM %s %s', $this->table, $this->getWhere());
        $this->sqli->query($sql);

        return ['affected_rows' => $this->sqli->affected_rows()];
    }

    function truncate()
    {
        $sql = sprintf('TRUNCATE %s', $this->table);
        $this->sqli->query($sql);

        return ['affected_rows' => $this->sqli->affected_rows()];
    }

    /*
     * Fetch rows
     */

    function first($where = false)
    {

        if ($where) {
            if (is_array($where)) {
                $this->where($where);
            } else {
                $this->where(['id' => $where]);
            }
        }

        return $this->sqli->first($this->makeSelectQuery());
    }

    function exists($where = false)
    {

        if ($where) {
            if (is_array($where)) {
                $this->where($where);
            } else {
                $this->where(['id' => $where]);
            }
        }

        return $this->sqli->exists($this->makeSelectQuery2());
    }
    
    
    function count($where = false)
    {

        if ($where) {
            if (is_array($where)) {
                $this->where($where);
            } else {
                $this->where(['id' => $where]);
            }
        }

        return $this->sqli->num($this->makeSelectQuery());
    }



    function rowKeys()
    {
        return $this->sqli->keys($this->makeSelectQuery());
    }

    function rowValues()
    {
        return $this->sqli->values($this->makeSelectQuery());
    }

    /*
     * Fetch lists
     */

    function list($id = null)
    {
        if ($id != null) {
            return $this->sqli->listId($this->makeSelectQuery(), $id);
        } else {
            return $this->sqli->list($this->makeSelectQuery());
        }
    }

    function groupId($id = 'id')
    {
        return $this->sqli->groupId($this->makeSelectQuery(), $id);
    }

    function listId($id = 'id')
    {
        return $this->list($id);
    }

    function listFlat()
    {
        return $this->sqli->listFlat($this->makeSelectQuery());
    }

    // Aliases

    function idList($id = 'id')
    {
        return $this->listId($id);
    }

    function groupedList($id = 'id')
    {
        return $this->groupId($id);
    }

    function flatList()
    {
        return $this->listFlat();
    }

    function insert()
    {
        return $this->create();
    }

    function set($values = [])
    {
        return $this->values((array)$values);
    }

    function rowId(int $id)
    {
        return $this->first($id);
    }

}
