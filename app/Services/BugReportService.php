<?php

namespace App\Services;

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

        return $created;
    }
}
