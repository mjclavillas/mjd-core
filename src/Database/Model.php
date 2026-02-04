<?php
namespace Mark\MjdCore\Database;

abstract class Model
{
    protected $db;
    protected $table;
    public $attributes = [];
    protected $timestamps = true;

    public function __construct(array $attributes = [])
    {
        $this->db = DB::getInstance();
        $this->attributes = $attributes;

        if (!$this->table) {
            $class = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($class) . 's';
        }
    }

    public static function __callStatic($method, $parameters)
    {
        $instance = new static();

        if (method_exists($instance, $method)) {
            return $instance->$method(...$parameters);
        }

        $query = $instance->query();
        if (method_exists($query, $method)) {
            return $query->$method(...$parameters);
        }

        throw new \Exception("Method {$method} not found on " . static::class);
    }

    public function __call($method, $parameters)
    {
        return $this->query()->$method(...$parameters);
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function query()
    {
        return new QueryBuilder($this->db, $this->table, static::class);
    }

    protected function create(array $attributes)
    {
        $instance = new static($attributes);
        $instance->save();
        return $instance;
    }

    protected function all()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    protected function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $attributes = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $attributes ? new static($attributes) : null;
    }
    public function delete()
    {
        if (!isset($this->attributes['id'])) {
            return false;
        }

        return $this->query()
                    ->where('id', $this->attributes['id'])
                    ->update(['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function forceDelete()
    {
        return $this->query()
                    ->where('id', $this->attributes['id'])
                    ->delete();
    }

    public function restore()
    {
        return $this->query()
                    ->where('id', $this->attributes['id'])
                    ->update(['deleted_at' => null]);
    }

    public function save()
    {
        $data = $this->attributes;
        $now = date('Y-m-d H:i:s');

        if (!isset($data['id'])) {
            if ($this->timestamps) {
                $data['created_at'] = $now;
                $data['updated_at'] = $now;
            }
            return $this->query()->insert($data);
        } else {
            if ($this->timestamps) {
                $data['updated_at'] = $now;
            }
            return $this->query()->where('id', $data['id'])->update($data);
        }
    }

    protected function hasMany($relatedClass, $foreignKey)
    {
        $relatedModel = new $relatedClass();

        return $relatedModel->query()->where($foreignKey, $this->attributes['id']);
    }

    public function belongsTo($relatedClass, $foreignKey)
    {
        $relatedModel = new $relatedClass();

        return $relatedModel->query()
                            ->where('id', $this->attributes[$foreignKey])
                            ->first();
    }
}