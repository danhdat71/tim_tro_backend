<?php

namespace App\Services;

use App\Enums\BugReportStatusEnum;
use App\Enums\ProductReadEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\UsedTypeEnum;
use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function countUser($userType)
    {
        return DB::table('users')->where('status', UserStatusEnum::ACTIVE->value)
            ->where('user_type', $userType)
            ->count();
    }

    public function reportCount()
    {
        return DB::table('users_report_products')
            ->where('is_read', ProductReadEnum::UN_READ->value)
            ->count();
    }

    public function bugReportCount()
    {
        return DB::table('bug_reports')
            ->where('status', BugReportStatusEnum::WAITING->value)
            ->count();
    }

    public function productCount()
    {
        return DB::table('products')
            ->where('status', ProductStatusEnum::REALITY->value)
            ->count();
    }

    public function countProductWithUsedTypes()
    {
        $usedTypes = UsedTypeEnum::getKeys();
        $result = [];

        foreach ($usedTypes as $usedType) {
            $count = DB::table('products')
                ->where('status', ProductStatusEnum::REALITY->value)
                ->where('used_type', $usedType)
                ->count();

            array_push($result, [
                'label' => UsedTypeEnum::tryFrom($usedType)->getStringValue(),
                'value' => $count,
            ]);
        }

        return $result;
    }

    public function draftRealityCount()
    {
        $currentYear = Carbon::now()->format('Y');
        $result = [];
        for($month = 1; $month <= 12; $month++) {
            $draftCount = DB::table('products')
                ->where('status', ProductStatusEnum::DRAFT->value)
                ->whereYear('posted_at', $currentYear)
                ->whereMonth('posted_at', $month)
                ->count();
            $realityCount = DB::table('products')
                ->where('status', ProductStatusEnum::REALITY->value)
                ->whereYear('posted_at', $currentYear)
                ->whereMonth('posted_at', $month)
                ->count();

            array_push($result, [
                'month' => 'Tháng ' . $month,
                'draft_count' => $draftCount,
                'reality_count' => $realityCount,
            ]);
        }
        
        return $result;
    }

    public function usedTypeMonth()
    {
        $currentYear = Carbon::now()->format('Y');
        $result = [];
        for($month = 1; $month <= 12; $month++) {
            $hostelCount = DB::table('products')
                ->where('used_type', UsedTypeEnum::HOSTEL->value)
                ->whereYear('posted_at', $currentYear)
                ->whereMonth('posted_at', $month)
                ->count();
            $fullHouseCount = DB::table('products')
                ->where('used_type', UsedTypeEnum::FULL_HOUSE->value)
                ->whereYear('posted_at', $currentYear)
                ->whereMonth('posted_at', $month)
                ->count();
            $sleepboxCount = DB::table('products')
                ->where('used_type', UsedTypeEnum::SLEEP_BOX->value)
                ->whereYear('posted_at', $currentYear)
                ->whereMonth('posted_at', $month)
                ->count();
            $apartmentCount = DB::table('products')
                ->where('used_type', UsedTypeEnum::APARTMENT->value)
                ->whereYear('posted_at', $currentYear)
                ->whereMonth('posted_at', $month)
                ->count();
            $officeCount = DB::table('products')
                ->where('used_type', UsedTypeEnum::OFFICE->value)
                ->whereYear('posted_at', $currentYear)
                ->whereMonth('posted_at', $month)
                ->count();
            $togetherCount = DB::table('products')
                ->where('used_type', UsedTypeEnum::TOGETHER->value)
                ->whereYear('posted_at', $currentYear)
                ->whereMonth('posted_at', $month)
                ->count();

            array_push($result, [
                'month' => 'Tháng ' . $month,
                'hostel_count' => $hostelCount,
                'full_house_count' => $fullHouseCount,
                'sleepbox_count' => $sleepboxCount,
                'apartment_count' => $apartmentCount,
                'office_count' => $officeCount,
                'together_count' => $togetherCount,
            ]);
        }
        
        return $result;
    }

    public function registerCountMonth()
    {
        $currentYear = Carbon::now()->format('Y');
        $result = [];

        for($month = 1; $month <= 12; $month++) {
            $providerCount = DB::table('users')
                ->where('user_type', UserTypeEnum::PROVIDER->value)
                ->where('status', UserStatusEnum::ACTIVE->value)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();
            $finderCount = DB::table('users')
                ->where('user_type', UserTypeEnum::FINDER->value)
                ->where('status', UserStatusEnum::ACTIVE->value)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();

            array_push($result, [
                'month' => 'Tháng ' . $month,
                'provider_count' => $providerCount,
                'finder_count' => $finderCount,
            ]);
        }
        
        return $result;
    }

    public function leaveCountMonth()
    {
        $currentYear = Carbon::now()->format('Y');
        $result = [];

        for($month = 1; $month <= 12; $month++) {
            $providerCount = DB::table('users')
                ->where('user_type', UserTypeEnum::PROVIDER->value)
                ->where('status', UserStatusEnum::LEAVE->value)
                ->whereYear('leave_at', $currentYear)
                ->whereMonth('leave_at', $month)
                ->count();
            $finderCount = DB::table('users')
                ->where('user_type', UserTypeEnum::FINDER->value)
                ->where('status', UserStatusEnum::LEAVE->value)
                ->whereYear('leave_at', $currentYear)
                ->whereMonth('leave_at', $month)
                ->count();

            array_push($result, [
                'month' => 'Tháng ' . $month,
                'provider_count' => $providerCount,
                'finder_count' => $finderCount,
            ]);
        }
        
        return $result;
    }

    public function index()
    {
        $userProviderCount = $this->countUser(UserTypeEnum::PROVIDER->value);
        $userFinderCount = $this->countUser(UserTypeEnum::FINDER->value);
        $reportProductCount = $this->reportCount();
        $bugReportCount = $this->bugReportCount();
        $productCount = $this->productCount();
        $countProductWithUsedType = $this->countProductWithUsedTypes();
        $draftRealityCount = $this->draftRealityCount();
        $usedTypeMonth = $this->usedTypeMonth();
        $registerCountMonth = $this->registerCountMonth();
        $leaveCountMonth = $this->leaveCountMonth();

        return [
            'provider_count' => $userProviderCount,
            'finder_count' => $userFinderCount,
            'report_product_count' => $reportProductCount,
            'bug_report_count' => $bugReportCount,
            'product_count' => $productCount,
            'count_product_with_used_types' => $countProductWithUsedType,
            'draft_reality_count' => $draftRealityCount,
            'used_type_month' => $usedTypeMonth,
            'register_count_month' => $registerCountMonth,
            'leave_count_month' => $leaveCountMonth,
        ];
    }
}
