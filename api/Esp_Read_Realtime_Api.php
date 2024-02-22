<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");

include_once './../connect_db.php';
include_once './../model/nodemcu_read.php';

$conn_DB = new ConnectDB();
$nodemcuaccess = new NODEMCU_READ($conn_DB->getConnectionDB());

$data = json_decode(file_get_contents("php://input"));

date_default_timezone_set('Asia/Bangkok');
//วันย้อนหลัง
date_default_timezone_set('Asia/Bangkok');
$Date_Back_One_Week = date("Y-m-d", strtotime("-7 day"));
$Date_Back_Two_Week = date("Y-m-d", strtotime("-14 day"));
$Date_Back_Three_Week = date("Y-m-d", strtotime("-21 day"));
$Date_Back_Four_Week = date("Y-m-d", strtotime("-28 day"));

//เวลาย้อนหลัง
$Time_Now = date("H:i");
$data_array = array();
//แปลงวันที่เป็นข้อความ
$Date_Back_One_Week = (string)$Date_Back_One_Week;
$Date_Back_Two_Week = (string)$Date_Back_Two_Week;
$Date_Back_Three_Week = (string)$Date_Back_Three_Week;
$Date_Back_Four_Week = (string)$Date_Back_Four_Week;
//แปลงเวลาเป็นข้อความ
$Time_Now = (string)$Time_Now;

//นำตัวแปรที่เป็น JSON ส่งไปให้ตัวแปรที่อยู่ใน Class
$nodemcuaccess->espkey = $data->espkey;

//นำตัวแปรจาก TIME SERVER ส่งไปให้ตัวแปรที่อยู่ใน Class
$nodemcuaccess->Date_Back_One_Week = $Date_Back_One_Week;
$nodemcuaccess->Date_Back_Two_Week = $Date_Back_Two_Week;
$nodemcuaccess->Date_Back_Three_Week = $Date_Back_Three_Week;
$nodemcuaccess->Date_Back_Four_Week = $Date_Back_Four_Week;

$nodemcuaccess->Time_Now = $Time_Now;

///////////////////////////////////////////////////////////////////////

$stmt_TIME_NOW_ONEWEEK = $nodemcuaccess->NODE_MCU_GET_DATA_TIME_NOW_ONEWEEK();
$stmt_TIME_NOW_TWOWEEK = $nodemcuaccess->NODE_MCU_GET_DATA_TIME_NOW_TWOWEEK();
$stmt_TIME_NOW_THREEWEEK = $nodemcuaccess->NODE_MCU_GET_DATA_TIME_NOW_THREEWEEK();
$stmt_TIME_NOW_FOURWEEK = $nodemcuaccess->NODE_MCU_GET_DATA_TIME_NOW_THREEWEEK();

$fetch_stmt_average_time_now_ONEWEEK = $stmt_TIME_NOW_ONEWEEK->fetch(PDO::FETCH_ASSOC);
$fetch_stmt_average_time_now_TWOWEEK = $stmt_TIME_NOW_TWOWEEK->fetch(PDO::FETCH_ASSOC);
$fetch_stmt_average_time_now_THREEWEEK = $stmt_TIME_NOW_THREEWEEK->fetch(PDO::FETCH_ASSOC);
$fetch_stmt_average_time_now_FOURWEEK = $stmt_TIME_NOW_FOURWEEK->fetch(PDO::FETCH_ASSOC);

$average_four_week_time_back_now = array(
    $fetch_stmt_average_time_now_ONEWEEK["average_pressure"] > 0 ? $fetch_stmt_average_time_now_ONEWEEK["average_pressure"] : 0,
    $fetch_stmt_average_time_now_TWOWEEK["average_pressure"] > 0 ? $fetch_stmt_average_time_now_TWOWEEK["average_pressure"] : 0,
    $fetch_stmt_average_time_now_THREEWEEK["average_pressure"] > 0 ? $fetch_stmt_average_time_now_THREEWEEK["average_pressure"] : 0,
    $fetch_stmt_average_time_now_FOURWEEK["average_pressure"] > 0 ? $fetch_stmt_average_time_now_FOURWEEK["average_pressure"] : 0,
);

//นำค่าที่ได้มาหาค่าเฉลี่ย
//$x_bar_all_week = array_sum($average_four_week_time_back_now) / count($average_four_week_time_back_now);


// Step 1: Fillter 0
$non_zero_values = array_filter($average_four_week_time_back_now, function ($value) {
    return $value > 0;
});
// Step 2: Calculate the mean
$x_bar_all_week = count($non_zero_values)>=2 ? array_sum($non_zero_values) / count($non_zero_values): 0;

// Step 3: Calculate squared differences
$squaredDifferences = array_map(function ($value) use ($x_bar_all_week) {
    return ($value - $x_bar_all_week) ** 2;
}, count($non_zero_values) >= 2 ? $non_zero_values : [0]);


// Step 4: Calculate variance
$variance = count($non_zero_values) >= 2 ? array_sum($squaredDifferences) / (count($non_zero_values) - 1) : 0;

// Step 5: Calculate standard deviation
$standardDeviation = sqrt($variance);

// Step 6: Calculate upper and lower bounds
$resultSDMinus = $standardDeviation > 0 ? $x_bar_all_week - $standardDeviation : 0;

/*
echo "------------------------------------------------------\n";
echo "Count of non-zero values: " . count($non_zero_values) . "\n";
echo "------------------------------------------------------\n";
echo "value average pressure one week: $average_four_week_time_back_now[0]\n";
echo "value average pressure two week: $average_four_week_time_back_now[1]\n";
echo "value average pressure three week: $average_four_week_time_back_now[2]\n";
echo "value average pressure four week: $average_four_week_time_back_now[3]\n";
echo "------------------------------------------------------\n";
echo "Mean: $x_bar_all_week\n";
echo "Squared Differences: " . implode(', ', $squaredDifferences) . "\n";
echo "Variance: $variance\n";
echo "Standard Deviation: $standardDeviation\n";
echo "predict pressure : $resultSDMinus\n";
echo "------------------------------------------------------\n"; */



//ทศนิยมสองตำแห่น่ง
$resultSDMinus = round($resultSDMinus, 2);

//นำesp ไปหา ว่ามีID ผู้ใช้หร้อไม่
$stmt_USERID = $nodemcuaccess->NODE_MCU_GET_ID();
$stmt_LASTTIME = $nodemcuaccess->NODE_MCU_GET_LAST_TIME();


//ดึงข้อมูลมาใช้
$result_USERID = $stmt_USERID->fetch(PDO::FETCH_ASSOC);
$result_LASTTIME = $stmt_LASTTIME->fetch(PDO::FETCH_ASSOC);


if ($result_USERID != null) { //ถ้ามีID ผู้ใช้
    $result_user_id = $result_USERID["user_Id"]; //ID ผู้ใช้เก็บไว้ในตัวแปร
    $nodemcuaccess->userId = $result_user_id; //นำตัวแปรส่งไปไปให้ตัวแปรในโม

    if ($stmt_REALTIME = $nodemcuaccess->NODE_MCU_GET_REALTIME()) {
        $data_item = array(
            "control_Date_On" => $stmt_REALTIME[0]['control_Date_On'],
            "control_Date_OFF" => $stmt_REALTIME[0]['control_Date_OFF'],
            "control_Time_On" => $stmt_REALTIME[0]['control_Time_On'],
            "control_Time_OFF" => $stmt_REALTIME[0]['control_Time_OFF'],
            "control_Solenoid" => $stmt_REALTIME[0]['control_Solenoid'],
            "control_Ai" => $stmt_REALTIME[0]['control_Ai'],
            "resultSDMinus" => $resultSDMinus,
            "Product_Details_Day_Water_Use" => $result_LASTTIME["Product_Details_Day_Water_Use"],
            "Product_Details_Month_Water_Use" => $result_LASTTIME["Product_Details_Month_Water_Use"],
            

        );
        
    } else {
        $data_item = array(
            "message" => "0"
        );
        echo json_encode("massge error");
    }
    array_push($data_array, $data_item);
} else {
    echo json_encode("NO_USER_ID");
}


echo json_encode($data_array);
http_response_code(200);
