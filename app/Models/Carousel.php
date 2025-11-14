<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Nexus\Database\NexusDB;

class Carousel extends NexusModel
{
    protected $table = 'carousels';

    protected $fillable = [
        'title',
        'description',
        'link',
        'background',
        'sort_order',
        'active',
        'section_id'
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
        'section_id' => 'integer'
    ];

    /**
     * 获取指定section的活跃轮播图，按排序顺序排列
     */
    public static function getActiveCarouselsBySection($sectionId = null, $limit = 5)
    {
        $query = self::query()
            ->where('active', true)
            ->orderBy('sort_order', 'asc');

        if ($sectionId !== null) {
            $query->where('section_id', $sectionId);
        }

        return $query->take($limit)->get();
    }

    /**
     * 获取所有轮播图，按排序顺序和活跃状态排列
     */
    public static function getAllCarousels($sectionId = null)
    {
        $query = self::query()
            ->orderBy('active', 'desc')
            ->orderBy('sort_order', 'asc');

        if ($sectionId !== null) {
            $query->where('section_id', $sectionId);
        }

        return $query->get();
    }
}