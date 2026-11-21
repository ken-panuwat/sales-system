<?php

namespace App\Support;

/**
 * สร้างข้อความ (payload) สำหรับ QR PromptPay มาตรฐาน EMVCo / Thai QR Payment
 * นำข้อความที่ได้ไปสร้างเป็น QR Code ที่หน้าเว็บ
 */
class PromptPay
{
    public static function payload(string $id, ?float $amount = null): string
    {
        $id = preg_replace('/\D/', '', $id);

        if (strlen($id) >= 13) {
            // เลขบัตรประชาชน / เลขผู้เสียภาษี
            $type = '02';
            $target = $id;
        } else {
            // เบอร์โทรศัพท์: 0812345678 -> 0066812345678
            $type = '01';
            $target = '0066'.substr($id, 1);
        }

        $merchant = self::tlv('00', 'A000000677010111').self::tlv($type, $target);

        $data = self::tlv('00', '01')
            .self::tlv('01', $amount ? '12' : '11')   // 12 = ใช้ครั้งเดียว (มียอดเงิน)
            .self::tlv('29', $merchant)
            .self::tlv('53', '764');                  // สกุลเงินบาท

        if ($amount) {
            $data .= self::tlv('54', number_format($amount, 2, '.', ''));
        }

        $data .= self::tlv('58', 'TH').'6304';

        return $data.self::crc16($data);
    }

    private static function tlv(string $tag, string $value): string
    {
        return $tag.str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT).$value;
    }

    private static function crc16(string $data): string
    {
        $crc = 0xFFFF;

        foreach (str_split($data) as $char) {
            $crc ^= ord($char) << 8;

            for ($i = 0; $i < 8; $i++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
