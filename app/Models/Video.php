<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes, Traits\Uuid;

    const NO_RATING = 'L';
    const RATING_LIST = [self::NO_RATING, '10', '12', '14', '16', '18'];

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration'
    ];

    protected $dates = ['deleted_at'];

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    public static function create(array $attributes = [])
    {
        try {
            \DB::beginTransaction();
            $obj = static::query()->create($attributes);
            // uploads here
            \DB::commit();
            return $obj;
        } catch (\Exception $e) {
            if (isset($obj)) {
                // exclude upload files
            }
            \DB::rollBack();
            throw $e;
        }        
    }

    public function update(array $attributes = [], array $options = [])
    {
        try {
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            if ($saved){
                // uploads here
                // exclude old files
            }            
            \DB::commit();
            return $saved;
        } catch (\Exception $e) {
            // exclude upload files
            \DB::rollBack();
            throw $e;
        }        
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed(); // withTrashed(): exclusion logic for deleted items
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }
}
