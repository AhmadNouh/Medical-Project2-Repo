<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserRegistryResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManageUserController extends Controller
{
    use ApiResponseTrait;

    /**
     * API لوحة التحكم الموحد: جلب وفلترة كافة مستخدمي وطاقم النظام
     * GET /api/dashboard/users-registry
     */
    public function getUsersWithFilter(Request $request): JsonResponse
    {
        // 1. استقبال الفلاتر من الـ Query Parameters
        $filters = $request->only(['role', 'search', 'status']);

        // 2. بناء الاستعلام مع تحميل العلاقات ذكياً بناءً على الدور المطلوب
        $users = User::filter($filters)
            // 💡 إذا كان الدور المطلوب طبيب، قم بتحميل علاقة الـ doctor فوراً لمنع بطء السيرفر
            ->when($request->query('role') === 'doctor', function ($query) {
                $query->with('doctorProfile');
            })
            // ويمكنك إضافة شروط تحميل لعلاقات أخرى مستقبلاً هنا (مثل السائقين)
            ->latest()
            ->paginate(30); // تحديد 15 عنصر في الصفحة

        // 3. إرجاع الرد الاحترافي الموحد للـ Frontend
        return $this->successResponse([
            'users'      => UserRegistryResource::collection($users),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'total'        => $users->total(),
            ]
        ], 'تم جلب سجل المستخدمين المفلتر بنجاح');
    }
}