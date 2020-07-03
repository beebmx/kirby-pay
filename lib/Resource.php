<?php

namespace Beebmx\KirbyPay;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kirby\Data\Data;
use Kirby\Toolkit\F;

class Resource
{
    /**
     * Collection of all resources loaded
     *
     * @var Collection
     */
    protected $data;

    /**
     * Determine if files have been read
     *
     * @var bool
     */
    protected $isPopulated = false;

    /**
     * Determine if files have been load
     *
     * @var bool
     */
    protected $isLoaded = false;

    /**
     * Type of the files to read
     *
     * @var string
     */
    protected $type;

    /**
     * The path of the directory
     *
     * @var string
     */
    protected $path;

    /**
     * Instance of the current model
     *
     * @var Model
     */
    protected $model;

    /**
     * Dates attributes can be parse
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Money attributes can be parse
     *
     * @var array
     */
    protected $money = [
        'amount',
        'fee'
    ];


    /**
     * Determine if the money cast should be done
     *
     * @var bool
     */
    protected $castMoney = true;

    /**
     * Sort direction in resoruces
     *
     * @var string
     */
    protected $sort = 'desc';

    /**
     * Create a new resource instance
     * @param string $type
     * @param string $path
     * @param Model $model
     * @return void
     */
    public function __construct(string $type, string $path, Model $model)
    {
        $this->path = $path;
        $this->type = $type;
        $this->model = $model;

        Storage::create($this->path);
        Carbon::setLocale(pay('locale', 'en_US'));

        $this->data = new Collection;
    }

    /**
     * Find a resource with an ID or UUID
     *
     * @param string $value
     * @return Model|bool
     */
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
            return $this->model->fill($this->read($filename)->toArray());
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Return the first resoruce in the data collection
     *
     * @return Model|bool
     */
    public function first()
    {
        $this->loadFiles();

        if ($this->isPopulated) {
            return $this->data->first()
                ? $this->model->fill(
                    $this->data->first()->toArray()
                )
                : false;
        }

        if ($this->data->isNotEmpty()) {
            return $this->model->fill(
                $this->read(
                    $this->data->first()
                )->toArray()
            );
        } else {
            return false;
        }
    }

    /**
     * Returns the last resource in the data collection
     *
     * @return Model|bool
     */
    public function last()
    {
        $this->loadFiles();

        if ($this->isPopulated) {
            return $this->data->last()
                ? $this->model->fill(
                    $this->data->last()->toArray()
                )
                : false;
        }

        if ($this->data->isNotEmpty()) {
            return $this->model->fill(
                $this->read(
                    $this->data->last()
                )->toArray()
            );
        } else {
            return false;
        }
    }

    /**
     * Writes a file with the data attributes and fill the current model
     *
     * @param array $data
     * @param int|null $pay_id
     * @param string|null $uuid
     * @return Model
     */
    public function write(array $data, int $pay_id = null, string $uuid = null)
    {
        $dates = [];

        if (!$pay_id && !$uuid) {
            $dates = [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        } else {
            $dates = ['updated_at' => Carbon::now()];
        }

        $pay_id = $pay_id ?? (int) $this->getNextId();
        $uuid = $uuid ?? (string) Str::uuid();

        $record = array_merge($data, [
            'pay_id' => $pay_id,
            'uuid' => $uuid,
        ], $dates);

        Data::write(
            Storage::path($this->path) . '/' . $pay_id . '-' . $uuid . $this->type,
            $this->uncast(
                (new Collection($record))
            )
        );

        return $this->model->fill($record);
    }

    /**
     * Read a file and returns a collection of it
     *
     * @param string|null $file
     * @return Collection
     */
    public function read(string $file = null)
    {
        return $this->cast(new Collection(Data::read(
            Storage::path($this->path) . '/' . $file,
        )));
    }

    /**
     * Deletes the file with the pay_id and UUID
     *
     * @param int $pay_id
     * @param string $uuid
     * @return bool
     */
    public function destroy(int $pay_id, string $uuid)
    {
        return F::remove(Storage::path($this->path) . '/' . $pay_id . '-' . $uuid . $this->type);
    }

    /**
     * Gets all the data with a new instance of the model
     *
     * @return array
     */
    public function get()
    {
        $this->populate();

        return $this->data->map(function ($resource) {
            return $this->model->newInstance($resource->toArray());
        })->toArray();
    }

    /**
     * Take the number of elements in the data collection
     *
     * @param int $take
     * @return $this
     */
    public function take(int $take = 10)
    {
        $this->loadFiles();

        $this->data = $this->data->take($take);
        return $this;
    }

    /**
     * Skip the number of elements in the data collection
     *
     * @param int $count
     * @return $this
     */
    public function skip(int $count = 10)
    {
        $this->loadFiles();

        if ($this->isPopulated) {
            $this->data = $this->data->skip($count);
        } else {
            $this->data = $this->data->skip($count);
        }

        return $this;
    }

    /**
     * Search into the data collection
     *
     * @param string|null $query
     * @param array $params
     * @return $this|bool
     */
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

    /**
     * Paginate the data collection
     *
     * @param int $page
     * @param int $perPage
     * @return $this
     */
    public function page(int $page = 1, int $perPage = 10)
    {
        $this->loadFiles();

        $this->data = $this->data->forPage($page, $perPage);

        return $this;
    }

    /**
     * Transform the dates into a human format
     *
     * @return $this
     */
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

    /**
     * Set the pay_id with a defined length
     *
     * @return $this
     */
    public function withPayIdFormat()
    {
        $this->populate();

        $this->data = $this->data->map(function ($item) {
            $item['pay_id'] = str_pad($item['pay_id'], pay('pay_id_length', 6), '0', STR_PAD_LEFT);
            return $item;
        });

        return $this;
    }

    /**
     * Disabled the casting for money fields
     *
     * @return $this
     */
    public function withoutMoneyCast()
    {
        $this->castMoney = false;

        return $this;
    }

    /**
     * Change the sort direction of the data collection
     *
     * @param string $sort
     * @return $this
     */
    public function setSort(string $sort = 'desc')
    {
        $this->loadFiles();
        $this->data = $this->sort($this->data, $sort);

        return $this;
    }

    /**
     * Count the number of resources
     *
     * @return int
     */
    public function count(): int
    {
        return Storage::count($this->path);
    }

    /**
     * Determine if the current resource is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return Storage::isEmpty($this->path);
    }

    /**
     * Determine if the current resource is not empty
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Returns the path of the resource
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the Collection of the current data
     *
     * @return Collection
     */
    protected function collection()
    {
        return $this->data;
    }

    /**
     * Load all the files in the current directory
     *
     * @return void
     */
    protected function loadFiles()
    {
        if (!$this->isLoaded) {
            $this->data = $this->sort(
                new Collection($this->getAllFiles()),
                $this->sort
            );
            $this->isLoaded = true;
        }
    }

    /**
     * Load all the files with a sort direction
     *
     * @param string $sort
     * @return Collection
     */
    protected function load(string $sort = 'desc')
    {
        return $this->sort(
            new Collection($this->getAllFiles()),
            $sort
        );
    }

    /**
     * Sort a collection with a direction
     *
     * @param Collection $collection
     * @param string $sort
     * @return Collection
     */
    protected function sort(Collection $collection, string $sort = 'desc')
    {
        return $collection->sortBy(function ($file, $index) {
            return $this->getIdByFilename($file);
        }, SORT_REGULAR, $sort === 'desc');
    }

    /**
     * Populate the data with all the files
     *
     * @return void
     */
    protected function populate()
    {
        $this->loadFiles();
        if (!$this->isPopulated) {
            $data = new Collection;
            foreach ($this->data as $file) {
                $data->put(
                    $this->getUuidByFilename($file),
                    $this->read($file)
                );
            }
            $this->data = $data;
            $this->isPopulated = true;
        }
    }

    /**
     * Get the ID of a filename
     *
     * @param $file
     * @return false|string
     */
    protected function getIdByFilename($file)
    {
        return substr($file, 0, strpos($file, '-'));
    }

    /**
     * Returns the filename through UUID
     *
     * @param string|null $uuid
     * @return bool|mixed
     */
    protected function findFilenameByUuid(string $uuid = null)
    {
        foreach ($this->getAllFiles() as $file) {
            if ($this->getUuidByFilename($file) === $uuid) {
                return $file;
            }
        }
        return false;
    }

    /**
     * Returns the filename through ID
     *
     * @param int $id
     * @return bool|mixed
     */
    protected function findFilenameById(int $id)
    {
        foreach ($this->getAllFiles() as $file) {
            if ((int) $this->getIdByFilename($file) === $id) {
                return $file;
            }
        }
        return false;
    }

    /**
     * Get the next ID in the current resource
     *
     * @return int
     */
    protected function getNextId()
    {
        $all = $this->load();
        return (int) $this->getIdByFilename($all->first()) + 1;
    }

    /**
     * Get all files in the current resource
     *
     * @return array
     */
    protected function getAllFiles()
    {
        return Storage::files($this->path);
    }

    /**
     * Get the filename through UUID
     *
     * @param string $file
     * @return false|string
     */
    protected function getUuidByFilename(string $file)
    {
        return substr($file, strpos($file, '-') + 1, -($this->getTypeLenght()));
    }

    /**
     * Get the lenght of the current resource type
     *
     * @return int
     */
    protected function getTypeLenght()
    {
        return strlen($this->type);
    }

    /**
     * Parse a value to carbon
     *
     * @param $date
     * @return Carbon
     */
    protected function parseDate($date)
    {
        try {
            return Carbon::create($date);
        } catch (Exception $e) {
            return $date;
        }
    }

    /**
     * Parse a value to a money format
     *
     * @param $value
     * @return string
     */
    protected function parseMoney($value)
    {
        return kpParseMoney($value);
    }

    /**
     * Returns a value
     *
     * @param $value
     * @return mixed
     */
    protected function unparseDate($value)
    {
        return $value;
    }

    /**
     * Returns the inverse value of money format
     *
     * @param $value
     * @return float
     */
    protected function unparseMoney($value)
    {
        return (float) preg_replace('/[^0-9.]/', '', $value);
    }

    /**
     * Cast all the values recursively
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function cast(Collection $collection)
    {
        return $collection->map(function ($value, $key) {
            if (is_array($value) || is_object($value)) {
                return $this->cast(new Collection($value));
            }

            if (in_array($key, $this->dates)) {
                return $this->parseDate($value);
            }
            if (in_array($key, $this->money) && $this->castMoney) {
                return $this->parseMoney($value);
            }
            return $value;
        });
    }

    /**
     * Returns the inverse cast ov the values recursively
     *
     * @param Collection $collection
     * @return array
     */
    protected function uncast(Collection $collection): array
    {
        return $collection->map(function ($value, $key) {
            if (is_array($value)) {
                return $this->uncast(new Collection($value));
            }

            if (in_array($key, $this->money)) {
                return $this->unparseMoney($value);
            }
            return $value;
        })->toArray();
    }
}
