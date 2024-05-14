<?php

namespace App\Services;

use App\Enums\BugReportStatusEnum;
use App\Enums\PaginateEnum;
use App\Jobs\NotiAdminReceiveBugReport;
use App\Jobs\NotiAdminReceiveBugReportJob;
use App\Jobs\NotiReporterBugWasFixedJob;
use App\Models\BugReport;
use App\Models\BugReportImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class BugReportService
{
    public $request = null;
    public $model = null;
    public $bugReportImagesModel = null;

    public function fillDataByFields($fiels = [])
    {
        foreach ($fiels as $value) {
            if ($value == 'ip_address') {
                $this->model->ip_address = $this->request->ip();
            } else {
                $this->model->{$value} = $this->request->{$value};
            }
        }

        $this->model->save();
        return $this->model;
    }

    public function getStoreBugReportAttr()
    {
        return [
            'full_name',
            'email',
            'description',
            'ip_address',
        ];
    }

    public function getSelectBugReportList()
    {
        //
    }

    public function storeBugReportImages($imageFile, $bugReportId)
    {
        $hash = date('YmdHis') . Str::random(10);
        $imageFileName = 'image_' . $hash . '.jpg';
        $bugReportPath = "assets/imgs/bug_report/bug_report_$bugReportId/";
        $imageFullPath = $bugReportPath . $imageFileName;
        if (!Storage::disk('public_path')->exists($bugReportPath)) {
            Storage::disk('public_path')->makeDirectory($bugReportPath);
        }

        $img = Image::make($imageFile);
        $img->save($imageFullPath, 100);

        return [
            'url' => $imageFullPath,
            'bug_report_id' => $bugReportId,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }

    public function store($request)
    {
        $this->request = $request;
        $this->model = new BugReport;
        $this->bugReportImagesModel = new BugReportImage;
        $created = $this->fillDataByFields($this->getStoreBugReportAttr());

        // Store images
        $imageData = [];
        if ($this->request->has('bug_report_images') && sizeof($this->request->bug_report_images) > 0) {
            foreach ($this->request->bug_report_images as $imageFile) {
                $imageData[] = $this->storeBugReportImages($imageFile, $created->id);
            }
        }
        $this->bugReportImagesModel->insert($imageData);
        
        // Noti to admin new bug created
        dispatch(new NotiAdminReceiveBugReportJob($created));

        return $this->getDetail($created->id);
    }

    public function getList($request)
    {
        $this->model = BugReport::class;

        return $this->model::select([
            'id',
            'full_name',
            'email',
            'ip_address',
            'status',
            'created_at',
        ])
        ->with([
            'bugReportImages' => function($q) {
                $q->select(['id', 'bug_report_id', 'url']);
            }
        ])
        ->paginate(PaginateEnum::PAGINATE_10->value);
    }

    public function getDetail($id)
    {
        $this->model = BugReport::class;

        return $this->model::select([
            'id',
            'full_name',
            'email',
            'ip_address',
            'status',
            'description',
            'created_at',
        ])
        ->with([
            'bugReportImages' => function($q) {
                $q->select(['id', 'bug_report_id', 'url']);
            }
        ])
        ->where('id', $id)
        ->first();
    }

    public function status($request)
    {
        $this->request = $request;
        $this->model = BugReport::find($this->request->id);
        $this->model->status = $this->request->status;
        $this->model->save();
        $bugData = $this->getDetail($this->model->id);

        if (BugReportStatusEnum::FIXED->value) {
            dispatch(new NotiReporterBugWasFixedJob($bugData));
        }

        return $bugData;
    }
}
