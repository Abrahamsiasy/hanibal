<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fighter extends Model
{
    public $incrementing = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'slug',
        'name',
        'nickname',
        'weight_class',
        'sport',
        'image_path',
    ];

    public function imageUrl(): ?string
    {
        if ($this->image_path === null) {
            return null;
        }

        return asset('storage/'.$this->image_path);
    }
}
