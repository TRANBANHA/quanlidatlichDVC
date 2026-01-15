<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Giấy xác nhận tình trạng hôn nhân</title>

    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ asset('fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        @page {
            margin: 2cm 2.5cm;
        }

        body {
            font-family: 'DejaVu Sans', 'Times New Roman', serif;
            font-size: 14pt;
            line-height: 1.6;
        }

        .top {
            width: 100%;
            margin-bottom: 20px;
        }

        .top-left {
            width: 45%;
            float: left;
            text-align: center;
            font-weight: bold;
        }

        .top-right {
            width: 55%;
            float: right;
            text-align: center;
            font-weight: bold;
        }

        .clear {
            clear: both;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin: 25px 0;
        }

        .title h1 {
            font-size: 18pt;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .title h2 {
            font-size: 16pt;
            margin: 0;
            text-transform: uppercase;
        }

        .content p {
            margin: 8px 0;
            text-align: justify;
        }

        .content .uyban {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .info-line {
            margin: 6px 0;
        }

        .item-line {
            display: flex;
            flex-direction: row;
            min-width: 120px;
        }

        .date{
            font-size: 16px;
            text-align: right;
            margin-top: 10px;
            font-style: italic;
        }

        .signature {
            margin-top: 50px;
            width: 100%;
        }

        .sign-left {
            width: 50%;
            float: left;
            text-align: center;
        }

        .sign-right {
            width: 50%;
            float: right;
            text-align: center;
        }

        .italic {
            font-style: italic;
        }

        .small {
            font-size: 12pt;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="top">
        <div class="top-left">
            ỦY BAN NHÂN DÂN<br>
           {{ $don_vi }}
            <br><br>
        </div>

        <div class="top-right">
            CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>
            Độc lập - Tự do - Hạnh phúc
        </div>
        <div class="date">Đà Nẵng, ngày 17 tháng 1 năm 2026</div>
        <div class="clear"></div>
    </div>

    <!-- TITLE -->
    <div class="title">
        <h1>GIẤY XÁC NHẬN TÌNH TRẠNG HÔN NHÂN</h1>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <p class="uyban">
            ỦY BAN NHÂN DÂN {{ $don_vi }}
        </p>

        <p>
            &nbsp;&nbsp;&nbsp;&nbsp;Căn cứ Luật Tổ chức Hội đồng nhân dân và Ủy ban nhân dân ngày 26 tháng 11 năm 2003;
        </p>

        <p>
            &nbsp;&nbsp;&nbsp;&nbsp;Căn cứ Nghị định số 158/2005/NĐ-CP ngày 27 tháng 12 năm 2005 của Chính phủ về đăng ký và quản lý hộ tịch;
        </p>

        <p>
            &nbsp;&nbsp;&nbsp;&nbsp;Xét đề nghị của ông/bà: <span>{{ $ho_ten }}</span>
        </p>

        <p style="text-align:center; font-weight:bold; margin-top:20px;">
            XÁC NHẬN:
        </p>
        <div class="info-line">
            <span>Ông/bà: {{ $ho_ten }}</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Giới tính: <span style="min-width:100px;">{{ $gioi_tinh }}</span>
        </div>

        <div class="info-line">
            Ngày, tháng, năm sinh: <span>{{ $ngay_sinh }}</span>
        </div>

        <div class="info-line">
            Nơi sinh: <span>{{ $noi_sinh }}</span>
        </div>

        <div class="info-line">
            Dân tộc: <span style="min-width:150px;">{{ $dan_toc }}</span>
            &nbsp;&nbsp;Quốc tịch: <span style="min-width:150px;">{{ $quoc_tich }}</span>
        </div>

        <div class="info-line">
            Số CMND/CCCD/Hộ chiếu: <span>{{ $cccd }}</span>
        </div>

        <div class="info-line">
            Nơi thường trú/tạm trú: <span>{{ $dia_chi }}</span>
        </div>

        <div class="info-line">
            Tình trạng hôn nhân:
            <span>{{ $tinh_trang_hon_nhan }}</span>
        </div>

        <p class="italic small">
            Giấy này có giá trị sử dụng trong thời hạn 06 tháng kể từ ngày cấp.
        </p>

    </div>

    <!-- SIGNATURE -->
    <div class="signature">
        <div class="sign-left">
            <strong>CÁN BỘ TƯ PHÁP – HỘ TỊCH</strong><br>
            <span class="italic">(Ký, ghi rõ họ tên)</span>
        </div>

        <div class="sign-right">
            <strong>TM. ỦY BAN NHÂN DÂN</strong><br>
            <strong>CHỦ TỊCH</strong><br>
            <span class="italic">(Ký, ghi rõ họ tên và đóng dấu)</span>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
