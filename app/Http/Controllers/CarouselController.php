<?php

namespace App\Http\Controllers;

use App\Models\Carousel;
use Illuminate\Http\Request;
use App\Auth\Permission;

class CarouselController extends Controller
{
    /**
     * 确保用户有权限管理轮播图
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Permission::canAdmin()) {
                return response()->json(['status' => 'error', 'message' => '权限不足'], 403);
            }
            return $next($request);
        });
    }

    /**
     * 获取所有轮播图
     */
    public function index(Request $request)
    {
        $sectionId = $request->input('section_id');
        $carousels = Carousel::getAllCarousels($sectionId);
        return response()->json(['status' => 'success', 'data' => $carousels]);
    }

    /**
     * 获取单个轮播图
     */
    public function show($id)
    {
        $carousel = Carousel::find($id);
        if (!$carousel) {
            return response()->json(['status' => 'error', 'message' => '轮播图不存在'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $carousel]);
    }

    /**
     * 创建新轮播图
     */
    public function store(Request $request)
    {
        $validated = $this->validateCarousel($request);
        
        $carousel = Carousel::create($validated);
        return response()->json(['status' => 'success', 'message' => '创建成功', 'data' => $carousel]);
    }

    /**
     * 更新轮播图
     */
    public function update(Request $request, $id)
    {
        $carousel = Carousel::find($id);
        if (!$carousel) {
            return response()->json(['status' => 'error', 'message' => '轮播图不存在'], 404);
        }

        $validated = $this->validateCarousel($request);
        $carousel->update($validated);
        return response()->json(['status' => 'success', 'message' => '更新成功', 'data' => $carousel]);
    }

    /**
     * 删除轮播图
     */
    public function destroy($id)
    {
        $carousel = Carousel::find($id);
        if (!$carousel) {
            return response()->json(['status' => 'error', 'message' => '轮播图不存在'], 404);
        }

        $carousel->delete();
        return response()->json(['status' => 'success', 'message' => '删除成功']);
    }

    /**
     * 切换轮播图状态
     */
    public function toggleStatus($id)
    {
        $carousel = Carousel::find($id);
        if (!$carousel) {
            return response()->json(['status' => 'error', 'message' => '轮播图不存在'], 404);
        }

        $carousel->active = !$carousel->active;
        $carousel->save();
        return response()->json(['status' => 'success', 'message' => '状态更新成功', 'data' => $carousel]);
    }

    /**
     * 获取活跃的轮播图（无需权限验证）
     */
    public function active(Request $request)
    {
        $sectionId = $request->input('section_id');
        $limit = $request->input('limit', 5);
        $carousels = Carousel::getActiveCarouselsBySection($sectionId, $limit);
        return response()->json(['status' => 'success', 'data' => $carousels]);
    }

    /**
     * 验证轮播图数据
     */
    private function validateCarousel(Request $request)
    {
        $rules = [
            'title' => 'required|max:100',
            'description' => 'max:255',
            'link' => 'max:255',
            'background' => 'max:255',
            'sort_order' => 'required|integer|min:0',
            'active' => 'boolean',
            'section_id' => 'nullable|integer'
        ];

        $validated = $request->validate($rules);
        return $validated;
    }
}