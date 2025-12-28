<?php
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\Notification;
;

function getPaymentStatus($data, $key)
{
    // Kiểm tra nếu phương thức payment() tồn tại và trả về một query builder
    if (method_exists($data, 'payment')) {
        $payment = $data->payment()->where('form_type', $key)->where('record_id', $data->id)->first();

        // Nếu tìm thấy thông tin thanh toán, trả về nó
        if ($payment) {
            return $payment;
        }
    }

    // Trả về object mặc định
    return (object) [
        'payment_status' => null, // Null khi không tìm thấy
    ];
}

if (!function_exists('getSetting')) {
    /**
     * Lấy giá trị setting từ database
     */
    function getSetting($key, $default = null)
    {
        try {
            $setting = \Illuminate\Support\Facades\DB::table('cai_dat')->where('khoa', $key)->first();
            return $setting ? $setting->gia_tri : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
use App\Models\ServiceSchedule;

if (!function_exists('isSelectedService')) {
    /**
     * Kiểm tra xem dịch vụ có được chọn cho thứ tương ứng không
     *
     * @param \Illuminate\Support\Collection|array $arrServiceSchedule
     * @param int $selectedServiceId
     * @param int $idDate (Thứ trong tuần: 2 -> Thứ 2, 6 -> Thứ 6)
     * @return string
     */
    function isSelectedService($arrServiceSchedule, $selectedServiceId, $idDate)
    {
        foreach ($arrServiceSchedule as $serviceSchedule) {
            if (
                isset($serviceSchedule->dich_vu_id) &&
                $serviceSchedule->dich_vu_id == $selectedServiceId &&
                $serviceSchedule->thu_trong_tuan == $idDate
            ) {
                return 'selected';
            }
        }

        return '';
    }
}

function disableExistUserInDate($serviceAssignments, $currentDate, $canBo)
{
    if (isset($serviceAssignments[$currentDate])) {
        $assignment = $serviceAssignments[$currentDate];
        $assignedCanBo = json_decode($assignment->ma_can_bo, true);
        if (in_array($canBo->id, $assignedCanBo)) {
            return 'selected';
        }
    }
    return '';
}

function getActiveNotifications()
{
    try {
        $notifications = \App\Models\Notification::where(function($query) {
            $query->where('ngay_het_han', '>', Carbon::now())
                  ->orWhereNull('ngay_het_han');
        })
        ->where('ngay_dang', '<=', Carbon::now())
        ->where('type', 0)
        ->get();
        return $notifications;
    } catch (\Exception $e) {
        return collect([]);
    }
}
function getCategories($categories, $old = '', $parentId = 0, $char = '')
{
    $id = request()->route()->category;
    if ($categories) {
        foreach ($categories as $key => $category) {
            if ($category->parent_id == $parentId && $id != $category->id) {

                echo '<option value="' . $category->id . '"';
                if ($old == $category->id) {
                    echo 'selected';
                }
                echo '>' . $char . $category->name . '</option>';
                unset($categories[$key]);
                getCategories($categories, $old, $category->id, $char . '|-');
            }
        }
    }
}


function isAdminActive($email)
{
    $count = Admin::where('email', $email)->where('is_active', '=', '1')->count();
    if ($count) {
        return true;
    }
    return false;
}

if (!function_exists('convert_array')) {
    function convert_array($system = null, $keyword = '', $value = '')
    {
        $temp = [];
        if (is_array($system)) {
            foreach ($system as $key => $val) {
                $temp[$val[$keyword]] = $val[$value];
            }
        }
        if (is_object($system)) {
            foreach ($system as $key => $val) {
                $temp[$val->{$keyword}] = $val->{$value};
            }
        }
        return $temp;
    }
}
if (!function_exists('renderSystemInput')) {
    function renderSystemInput(string $name = '', $type = 'text', $system = null)
    {
        return ' <input type="' . $type . '"
        name="config[' . $name . ']"
        value=" ' . old($name, (isset($system[$name])) ? $system[$name] : "") . '"
        class="form-control"
        placeholder="" >';
    }
}
if (!function_exists('renderSystemTextarea')) {
    function renderSystemTextarea(string $name = '', $system = null)
    {
        return '<textarea name="config[' . $name . ']" value="" class="form-control">'
            . old($name, (isset($system[$name])) ? $system[$name]
                : "") . '</textarea>';
    }
}
if (!function_exists('renderSystemSelect')) {
    function renderSystemSelect($items, string $name = '', $system = null)
    {
        $html = '<select name="config[' . $name . ']" class="form-control">';

        foreach ($items as $key => $item) {
            $html .= '<option ' . (old($name, (isset($system[$name])) ? $system[$name] : "")) ? "select" : '' . ' value=' . $key . '>' . $key . '</option>';
        }

        $html .= '</select>';
        return $html;
    }
}

