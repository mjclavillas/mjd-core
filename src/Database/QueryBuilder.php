<?php
namespace Mark\MjdCore\Database;

use PDO;

class QueryBuilder
{
    protected $pdo;
    protected $table;
    protected $modelClass;
    protected $where = [];
    protected $params = [];
    protected $orderBy = "";
    protected $limit = "";
    protected $withTrashed = false;

    public function __construct(PDO $pdo, string $table, string $modelClass = null) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->modelClass = $modelClass ?? $this->inferModelClass();
    }

    public function withTrashed() {
        $this->withTrashed = true;
        return $this;
    }

    public function where($column, $value, $operator = '=') {
        $this->where[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy = " ORDER BY {$column} {$direction}";
        return $this;
    }

    public function limit(int $value) {
        $this->limit = " LIMIT {$value}";
        return $this;
    }

    protected function buildWhereClause() {
        if (!$this->withTrashed && !str_contains(implode(' ', $this->where), 'deleted_at')) {
            $this->where[] = "deleted_at IS NULL";
        }

        return !empty($this->where) ? " WHERE " . implode(' AND ', $this->where) : "";
    }

    public function all() {
        return $this->get();
    }

    // --- READ ---
    public function get()
    {
        if (!str_contains($this->buildWhereClause(), 'deleted_at')) {
            $this->where('deleted_at', null);
        }

        $sql = "SELECT * FROM {$this->table}";
        $sql .= $this->buildWhereClause();

        if (!empty($this->orderBy)) $sql .= " " . trim($this->orderBy);
        if (!empty($this->limit))   $sql .= " " . trim($this->limit);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $models = [];

        foreach ($results as $attributes) {
            $models[] = new $this->modelClass($attributes);
        }

        return $models;
    }


    protected function inferModelClass() {
        $name = ucfirst(rtrim($this->table, 's'));
        return "App\\Models\\" . $name;
    }

    public function first() {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }

    // --- CREATE ---
    public function insert(array $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    // --- UPDATE ---
    public function update(array $data) {
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $setStr = implode('=?, ', array_keys($data)) . '=?';
        $sql = "UPDATE {$this->table} SET {$setStr}";
        $sql .= $this->buildWhereClause();

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_merge(array_values($data), $this->params));
    }

    // --- DELETE ---
    public function delete() {
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->buildWhereClause();

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->params);
    }

    // --- PAGINATE ---
    public function paginate(int $perPage = 15)
    {
        $currentPage = (int) ($_GET['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;

        $countSql = "SELECT COUNT(*) FROM {$this->table}";
        if (!empty($this->where)) $countSql .= " WHERE " . implode(' AND ', $this->where);

        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($this->params);
        $totalItems = (int) $stmt->fetchColumn();

        $offset = ($currentPage - 1) * $perPage;

        $this->limit = " LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->get();

        return [
            'data'         => $data,
            'total'        => $totalItems,
            'per_page'     => $perPage,
            'current_page' => $currentPage,
            'last_page'    => ceil($totalItems / $perPage),
            'next_page'    => $currentPage + 1,
            'prev_page'    => $currentPage - 1,
        ];
    }
}