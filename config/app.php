<?php
function getLastName($fullName)
{
    // Kiểm tra và xóa khoảng trắng thừa
    $fullName = trim($fullName);
    // Tách chuỗi thành mảng dựa trên khoảng trắng
    $parts = explode(' ', $fullName);
    // Trả về phần tử cuối cùng, nếu không có thì trả về chuỗi rỗng
    return end($parts) ?: $fullName;
}
function type_serive($type)
{
    switch ($type):
        case "game":
            echo "Tài khoản game";
            break;
        case "mxh":
            echo "Tài khoản MXH";
            break;
        default:
            echo "không xác định";
            break;
    endswitch;
}
function upload_images($file, $name = "web")
{
    // Kiểm tra dữ liệu file
    if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        error_log("File không tồn tại hoặc không hợp lệ: " . print_r($file, true));
        return false;
    }

    // Lấy thông tin file gốc
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    // Đường dẫn gốc upload
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/public/assets/upload/$name/images/";
    $currentYear = date('Y');
    $currentMonth = date('m');

    // Tạo thư mục theo cấu trúc năm/tháng
    $uploadPath = $uploadDir . $currentYear . '/' . $currentMonth . '/';
    if (!is_dir($uploadPath)) {
        if (!mkdir($uploadPath, 0755, true)) {
            error_log("Không thể tạo thư mục: " . $uploadPath);
            return false; // Không thể tạo thư mục
        }
    }

    // Đảm bảo file có extension hợp lệ
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Thêm các định dạng hợp lệ
    if (!in_array(strtolower($extension), $allowedExtensions)) {
        error_log("Định dạng file không hợp lệ: " . $extension);
        return false; // Không đúng định dạng file
    }

    $hashedExtension = substr(md5($extension), 0, 8);
    $timestamp = time();
    $hashedName = substr(md5($originalName . $timestamp), 0, 16);
    $fileName = $hashedName . '-' . $timestamp . '.' . $extension;
    $filePath = $uploadPath . $fileName;

    // Di chuyển file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $relativePath = "/assets/upload/$name/images/" . $currentYear . '/' . $currentMonth . '/' . $fileName;
        return $relativePath;
    } else {
        error_log("Không thể di chuyển file: " . $file['tmp_name'] . " tới " . $filePath);
        return false;
    }
}



function format_number($number)
{
    return str_replace(",", ",", number_format($number));
}

function format_number2($number)
{
    return str_replace(",", ".", number_format($number));
}

function objectToArray($d)
{
    return json_decode(json_encode($d));
}

function tagurl($text)
{
    $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
    $string = preg_replace($url, '<strong>$0</strong>', $text);
    return $string;
}

function str($data)
{
    return str_replace(array('<', "'", '>', '?', '/', "\\", '--', 'eval(', '<php'), array('', '', '', '', '', '', '', '', ''), htmlspecialchars(addslashes(strip_tags($data))));
}
