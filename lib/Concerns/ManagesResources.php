<?php

namespace Beebmx\KirbyPay\Concerns;

use Beebmx\KirbyPay\Drivers\Factory;
use Beebmx\KirbyPay\Storage;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kirby\Data\Data;

trait ManagesResources
{
    protected $driver;

    protected $data;

    protected $files;

    protected $isPopulated = false;

    protected $isLoaded = false;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $sort = 'desc';

    public function __construct()
    {
        $this->driver = (new Factory)->find();
        Storage::create(static::$path);

        $this->data = new Collection;
        $this->files = new Collection;

        $this->boot();
    }

    public function boot()
    {
    }

    public function find(string $uuid = null)
    {
        try {
            return $this->read(
                $this->findFilenameByUuid($uuid)
            )->toArray();
        } catch (Exception $e) {
            return false;
        }
    }

    public function findById(int $id)
    {
        try {
            return $this->read(
                $this->findFilenameById($id)
            )->toArray();
        } catch (Exception $e) {
            return false;
        }
    }

    public function first()
    {
        $this->load();
        return $this->read(
            $this->files->first()
        )->toArray();
    }

    public function last()
    {
        $this->load();
        return $this->read(
            $this->files->last()
        )->toArray();
    }

    public function write(array $data)
    {
        $uuid = (string) Str::uuid();
        $record = array_merge($data, [
            'pay_id' => static::getNextId(),
            'uuid' => $uuid,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Data::write(
            Storage::path(static::$path) . '/' . static::getNextId() . '-' . $uuid . static::$type,
            $record
        );

        return $record;
    }

    public function read(string $file = null)
    {
        return (new Collection(Data::read(
            Storage::path(static::$path) . '/' . $file,
        )))->map(function ($value, $key) {
            if (in_array($key, $this->dates)) {
                return Carbon::create($value);
            }
            return $value;
        });
    }

    public function get()
    {
        $this->populate();
        return $this->data->toArray();
    }

    public function take(int $take = 10)
    {
        $this->load();
        if ($this->isPopulated) {
            $this->data = $this->data->take($take);
        } else {
            $this->files = $this->files->take($take);
        }

        return $this;
    }

    public function skip(int $count = 10)
    {
        $this->load();
        if ($this->isPopulated) {
            $this->data = $this->data->skip($count);
        } else {
            $this->files = $this->files->skip($count);
        }

        return $this;
    }

    public function search(string $query = null, $params = [])
    {
        $this->populate();
        if (empty(trim($query)) === true) {
            return false;
        }

        if (is_string($params) === true) {
            $params = Str::of($params)->explode('|')->map(function ($param) {
                $field = explode(':', $param);

                return [
                    'field' => $field[0],
                    'type' => $field[1] ?? 'string',
                ];
            });
        }

        $this->data = $this->data->filter(function ($record, $key) use ($query, $params) {
            return $record->filter(function ($value, $key) use ($query, $params) {
                if (in_array($key, $params->pluck('field')->toArray())) {
                    return $params->filter(function($param) use ($value, $query, $key){
                        if ($param['field'] === $key && $param['type'] === 'string') {
                            return Str::contains($value, $query);
                        }
                        else if ($param['field'] === $key && $param['type'] === 'int') {
                            return (int) $value === (int) $query;
                        }
                        return false;
                    })->isNotEmpty();
                }
                return false;
            })->isNotEmpty();
        });

        return $this;
    }

    public function setSort(string $sort = 'desc')
    {
        $this->load();
        $this->files = $this->sort($this->files, $sort);

        return $this;
    }

    public static function count()
    {
        return Storage::count(static::$path);
    }

    protected function getDriver()
    {
        return $this->driver;
    }

    protected function collection()
    {
        return $this->data;
    }

    protected function load()
    {
        if (!$this->isLoaded) {
            $this->files = $this->sort(
                new Collection(static::getAllFiles()),
                $this->sort
            );
            $this->isLoaded = true;
        }
    }

    protected function sort(Collection $collection, string $sort = 'desc')
    {
        return $collection->sortBy(function ($file, $index) {
            return $this->getIdByFilename($file);
        }, SORT_REGULAR, $sort === 'desc');
    }

    protected function populate()
    {
        $this->load();
        if (!$this->isPopulated && $this->data->isEmpty()) {
            foreach ($this->files as $file) {
                $this->data->put(
                    static::getUuidByFilename($file),
                    $this->read($file)
                );
            }
            $this->isPopulated = true;
        }
    }

    protected function getIdByFilename($file)
    {
        return substr($file, 0, strpos($file, '-'));
    }

    protected function findFilenameByUuid(string $uuid = null)
    {
        foreach (static::getAllFiles() as $file) {
            if (static::getUuidByFilename($file) === $uuid) {
                return $file;
            }
        }
        return false;
    }

    protected function findFilenameById(int $id)
    {
        foreach (static::getAllFiles() as $file) {
            if ((int) static::getIdByFilename($file) === $id) {
                return $file;
            }
        }
        return false;
    }

    protected static function getNextId()
    {
        return static::count() + 1;
    }

    protected static function getAllFiles()
    {
        return Storage::index(static::$path);
    }

    protected static function getUuidByFilename(string $file)
    {
        return substr($file, strpos($file, '-') + 1, -(static::getTypeLenght()));
    }

    protected static function getTypeLenght()
    {
        return strlen(static::$type);
    }
}
