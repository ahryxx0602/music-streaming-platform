<?php

namespace Modules\Administration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Administration\Http\Requests\StoreBannerRequest;
use Modules\Administration\Http\Requests\UpdateBannerRequest;
use Modules\Administration\Http\Requests\ReorderBannerRequest;
use Modules\Administration\Models\Banner;

class AdminBannerController extends Controller
{
    /**
     * [API-ADM-xx] Lấy danh sách banner (sắp xếp theo order)
     */
    public function index(): JsonResponse
    {
        $banners = Banner::orderBy('order', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    /**
     * [API-ADM-xx] Tạo Banner mới
     */
    public function store(StoreBannerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_url'] = $path;
        }

        $maxOrder = Banner::max('order');
        $validated['order'] = $maxOrder !== null ? $maxOrder + 1 : 0;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $banner = Banner::create($validated);

        // Xóa Cache [RULE-ADM-BNR-02]
        Cache::forget('explore_banners');

        return response()->json([
            'success' => true,
            'message' => 'Tạo Banner thành công.',
            'data' => [
                'banner' => $banner
            ]
        ], 201);
    }

    /**
     * [API-ADM-xx] Cập nhật Banner
     */
    public function update(UpdateBannerRequest $request, $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old file
            if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
                Storage::disk('public')->delete($banner->image_url);
            }
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_url'] = $path;
        }

        $banner->update($validated);

        // Xóa Cache [RULE-ADM-BNR-02]
        Cache::forget('explore_banners');

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Banner thành công.',
            'data' => [
                'banner' => $banner
            ]
        ]);
    }

    /**
     * [API-ADM-xx] Cập nhật thứ tự các Banner
     */
    public function reorder(ReorderBannerRequest $request): JsonResponse
    {
        $bannerIds = $request->banner_ids;

        DB::transaction(function () use ($bannerIds) {
            foreach ($bannerIds as $index => $id) {
                Banner::where('id', $id)->update(['order' => $index]);
            }
        });

        // Xóa Cache [RULE-ADM-BNR-02]
        Cache::forget('explore_banners');

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật vị trí Banner thành công.'
        ]);
    }

    /**
     * [API-ADM-xx] Xóa Banner
     */
    public function destroy($id): JsonResponse
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
            Storage::disk('public')->delete($banner->image_url);
        }

        $banner->delete();

        // Xóa Cache [RULE-ADM-BNR-02]
        Cache::forget('explore_banners');

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa Banner.'
        ]);
    }
}
