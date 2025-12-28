<?php

namespace App\Helpers;

class VietQRHelper
{
    /**
     * Tạo QR code theo chuẩn EMV QR Code (VietQR)
     * 
     * @param string $accountNumber Số tài khoản
     * @param string $accountName Tên chủ tài khoản
     * @param float $amount Số tiền
     * @param string $content Nội dung chuyển khoản (mã thanh toán)
     * @return string Chuỗi QR code theo chuẩn EMV
     */
    public static function generateVietQR($accountNumber, $accountName, $amount, $content = '')
    {
        // Payload Format Indicator (00) - Luôn là "01"
        $payload = "000201";
        
        // Point of Initiation Method (01) - "11" = static, "12" = dynamic
        $payload .= "010212";
        
        // Merchant Account Information (38)
        // GUID: "A000000727" là mã của VietQR
        $merchantAccountInfo = "0010A000000727";
        
        // Account Number (00) - ID 01
        $accountNumberLength = str_pad(mb_strlen($accountNumber, 'UTF-8'), 2, '0', STR_PAD_LEFT);
        $merchantAccountInfo .= "01" . $accountNumberLength . $accountNumber;
        
        // Account Name (02) - Tên chủ tài khoản - ID 02
        $accountNameUpper = mb_strtoupper($accountName, 'UTF-8');
        $accountNameLength = str_pad(mb_strlen($accountNameUpper, 'UTF-8'), 2, '0', STR_PAD_LEFT);
        $merchantAccountInfo .= "02" . $accountNameLength . $accountNameUpper;
        
        // Merchant Account Information length
        $merchantAccountInfoLength = str_pad(mb_strlen($merchantAccountInfo, 'UTF-8'), 2, '0', STR_PAD_LEFT);
        $payload .= "38" . $merchantAccountInfoLength . $merchantAccountInfo;
        
        // Merchant Category Code (52) - "0000" = không xác định
        $payload .= "52040000";
        
        // Transaction Currency (53) - "704" = VND
        $payload .= "5303704";
        
        // Transaction Amount (54) - Số tiền (không có dấu phẩy, không có ký tự)
        $amountString = number_format($amount, 0, '', '');
        $amountLength = str_pad(strlen($amountString), 2, '0', STR_PAD_LEFT);
        $payload .= "54" . $amountLength . $amountString;
        
        // Country Code (58) - "VN"
        $payload .= "5802VN";
        
        // Additional Data Field Template (62)
        $additionalData = "";
        
        // Bill Number (01) - Mã thanh toán
        if ($content) {
            $contentLength = str_pad(mb_strlen($content, 'UTF-8'), 2, '0', STR_PAD_LEFT);
            $additionalData .= "01" . $contentLength . $content;
        }
        
        // Additional Data length
        $additionalDataLength = str_pad(mb_strlen($additionalData, 'UTF-8'), 2, '0', STR_PAD_LEFT);
        $payload .= "62" . $additionalDataLength . $additionalData;
        
        // CRC (63) - Checksum
        $crc = self::calculateCRC16($payload);
        $payload .= "6304" . strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
        
        return $payload;
    }
    
    /**
     * Tính CRC16 cho EMV QR Code
     * 
     * @param string $data
     * @return int
     */
    private static function calculateCRC16($data)
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        
        return $crc;
    }
}

