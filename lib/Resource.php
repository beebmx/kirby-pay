<?php

namespace Beebmx\KirbyPay;

use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kirby\Data\Data;

class Resource
{
    protected $data;

    protected $files;

    protected $isPopulated = false;

    protected $isLoaded = false;

    protected $type;

    protected $path;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $money = [
        'amount',
        'fee'
    ];

    protected $sort = 'desc';

    public function __construct(string $type, string $path)
    {
        $this->path = $path;
        $this->type = $type;

        Storage::create($this->path);
        Carbon::setLocale(pay('locale', 'en_US'));

        $this->data = new Collection;
        $this->files = new Collection;
    }

    public function find($value = null)
    {
        if (empty($value)) {
            return false;
        }

        if (Str::isUuid($value)) {
            $filename = $this->findFilenameByUuid($value);
        } elseif (is_int($value)) {
            $filename = $this->findFilenameById($value);
        } else {
            return false;
        }

        try {
            return $this->read($filename)->toArray();
        } catch (Exception $e) {
            return false;
        }
    }

    public function first()
    {
        $this->loadFiles();

        if ($this->isPopulated) {
            return $this->data->first()
                ? $this->data->first()->toArray()
                : false;
        }

        return $this->read(
            $this->files->first()
        )->toArray();
    }

    public function last()
    {
        $this->loadFiles();

        if ($this->isPopulated) {
            return $this->data->last()
                ? $this->data->last()->toArray()
                : false;
        }

        return $this->read(
            $this->files->last()
        )->toArray();
    }

    public function write(array $data)
    {
        $uuid = (string) Str::uuid();
        $record = array_merge($data, [
            'pay_id' => $this->getNextId(),
            'id' => $this->getNextId(),
            'uuid' => $uuid,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Data::write(
            Storage::path($this->path) . '/' . $this->getNextId() . '-' . $uuid . $this->type,
            $record
        );

        return $record;
    }

    public function read(string $file = null)
    {
        return $this->cast(new Collection(Data::read(
            Storage::path($this->path) . '/' . $file,
        )));
    }

    public function get()
    {
        $this->populate();
        return $this->data->toArray();
    }

    public function take(int $take = 10)
    {
        $this->loadFiles();

        if ($this->isPopulated) {
            $this->data = $this->data->take($take);
        } else {
            $this->files = $this->files->take($take);
        }

        return $this;
    }

    public function skip(int $count = 10)
    {
        $this->loadFiles();
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
                    return $params->filter(function ($param) use ($value, $query, $key) {
                        if ($param['field'] === $key && $param['type'] === 'string') {
                            return Str::contains($value, $query);
                        } elseif ($param['field'] === $key && $param['type'] === 'int') {
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

    public function page(int $page = 1, int $perPage = 10)
    {
        $this->loadFiles();
        if ($this->isPopulated) {
            $this->data = $this->data->forPage($page, $perPage);
        } else {
            $this->files = $this->files->forPage($page, $perPage);
        }

        return $this;
    }

    public function diffForHumans()
    {
        $this->populate();

        $this->data = $this->data->map(function ($item) {
            return $item->transform(function ($value, $key) {
                if (in_array($key, $this->dates)) {
                    return $value->diffForHumans();
                }

                return $value;
            });
        });

        return $this;
    }

    public function setSort(string $sort = 'desc')
    {
        $this->loadFiles();
        $this->files = $this->sort($this->files, $sort);

        return $this;
    }

    public function count()
    {
        return Storage::count($this->path);
    }

    public function isEmpty(): bool
    {
        return Storage::isEmpty($this->path);
    }

    protected function collection()
    {
        return $this->data;
    }

    protected function loadFiles()
    {
        if (!$this->isLoaded) {
            $this->files = $this->sort(
                new Collection($this->getAllFiles()),
                $this->sort
            );
            $this->isLoaded = true;
        }
    }

    protected function load(string $sort = 'desc')
    {
        return $this->sort(
            new Collection($this->getAllFiles()),
            $sort
        );
    }

    protected function sort(Collection $collection, string $sort = 'desc')
    {
        return $collection->sortBy(function ($file, $index) {
            return $this->getIdByFilename($file);
        }, SORT_REGULAR, $sort === 'desc');
    }

    protected function populate()
    {
        $this->loadFiles();
        if (!$this->isPopulated && $this->data->isEmpty()) {
            foreach ($this->files as $file) {
                $this->data->put(
                    $this->getUuidByFilename($file),
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
        foreach ($this->getAllFiles() as $file) {
            if ($this->getUuidByFilename($file) === $uuid) {
                return $file;
            }
        }
        return false;
    }

    protected function findFilenameById(int $id)
    {
        foreach ($this->getAllFiles() as $file) {
            if ((int) $this->getIdByFilename($file) === $id) {
                return $file;
            }
        }
        return false;
    }

    protected function getNextId()
    {
        $all = $this->load();
        return (int) $this->getIdByFilename($all->first()) + 1;
    }

    protected function getAllFiles()
    {
        return Storage::files($this->path);
    }

    protected function getUuidByFilename(string $file)
    {
        return substr($file, strpos($file, '-') + 1, -($this->getTypeLenght()));
    }

    protected function getTypeLenght()
    {
        return strlen($this->type);
    }

    protected function parseDate($date)
    {
        return Carbon::create($date);
    }

    protected function parseMoney($value)
    {
        return Money::of(
            $value,
            strtoupper(pay('currency', 'usd')),
            new CustomContext(pay('money_precision', 2))
        )->formatTo(
            pay('locale', 'en_US')
        );
    }

    protected function cast(Collection $collection)
    {
        return $collection->map(function ($value, $key) {
            if (is_array($value) || is_object($value)) {
                return $this->cast(new Collection($value));
            }

            if (in_array($key, $this->dates)) {
                return $this->parseDate($value);
            }
            if (in_array($key, $this->money)) {
                return $this->parseMoney($value);
            }
            return $value;
        });
    }
}
