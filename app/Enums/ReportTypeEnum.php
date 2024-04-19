<?php

namespace App\Enums;

enum ReportTypeEnum: int
{
    case INFO_INVALID = 1;
    case ADDRESS_NOT_FOUND = 2;
    case SCAM = 3;
    case IMAGE_NOT_ALLOW = 4;
    case COPYRIGHT = 5;
    case OTHER = 6;

    public static function getKeys()
    {
        return [
            self::INFO_INVALID->value,
            self::ADDRESS_NOT_FOUND->value,
            self::SCAM->value,
            self::IMAGE_NOT_ALLOW->value,
            self::COPYRIGHT->value,
            self::OTHER->value,
        ];
    }

    public function getStringValue(): string
    {
        return match($this) {
            self::INFO_INVALID => 'Thông tin sai sự thật',
            self::ADDRESS_NOT_FOUND => 'Địa chỉ không tồn tại',
            self::SCAM => 'Lừa đảo, đa cấp, ...',
            self::IMAGE_NOT_ALLOW => 'Ảnh khoả thân, máu, tự sát, ...',
            self::COPYRIGHT => 'Bài viết sao chép từ nơi khác',
            self::OTHER => 'Khác',
        };
    }
}
