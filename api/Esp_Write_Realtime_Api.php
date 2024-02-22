<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");

include_once './../connect_db.php';
include_once './../model/nodemcu_write.php';



$conn_DB = new ConnectDB();
$nodemcuaccess = new NODEMCU_WRITE($conn_DB->getConnectionDB());

date_default_timezone_set('Asia/Bangkok');
$date = date("Y-m-d");
$time = date("H:i:s");

$data = json_decode(file_get_contents("php://input"));

$nodemcuaccess->espkey = $data->espkey;
$nodemcuaccess->flowrate = $data->flowrate;
$nodemcuaccess->pressure = $data->pressure;
$nodemcuaccess->solenoid = $data->solenoid;
$nodemcuaccess->ai_status = $data->ai_status;
$nodemcuaccess->time_status = $data->time_status;
$nodemcuaccess->d_totalwater = $data->d_totalwater;
$nodemcuaccess->m_totalwater = $data->m_totalwater;
$nodemcuaccess->alert = $data->alert;
$nodemcuaccess->date = $date;
$nodemcuaccess->time = $time;
$timecontrolstop = $data->timecontrolstop;



if ($stmt_productKey = $nodemcuaccess->NODE_GET_PRODUCT_KEY()) {
    if ($stmt_productKey->rowCount() > 0) {

        if ($stmt_userID = $nodemcuaccess->NODE_MCU_GET_ID()) {

            $result = $stmt_userID->fetch(PDO::FETCH_ASSOC);
            if ($result == true) {
                $result_user_id = $result["user_Id"];
                $nodemcuaccess->userId = $result_user_id;
                if ($stmt_userID->rowCount() > 0) {
                    if ($timecontrolstop == 0) { //การตั้งเวลาสำเร็จ
                        $result_updatetime = $nodemcuaccess->UPDATE_TIME();
                        $stmt_realtime = $nodemcuaccess->NODE_MCU_INSERT_REALTIME();
                        $stmt_insert = $nodemcuaccess->NODE_MCU_INSERT_DATA();
                        if ($stmt_realtime == true && $stmt_insert == true) {
                            http_response_code(200);
                        } else {
                            http_response_code(503);
                        }
                    } else {
                        $stmt_realtime = $nodemcuaccess->NODE_MCU_INSERT_REALTIME();
                        $stmt_insert = $nodemcuaccess->NODE_MCU_INSERT_DATA();
                        if ($stmt_realtime == true && $stmt_insert == true) {
                            http_response_code(200);
                        } else {
                            http_response_code(503);
                        }
                    }
                } else {
                    if ($timecontrolstop == 1) { //กำลังตั้งเวลา
                        $stmt_realtime = $nodemcuaccess->NODE_MCU_INSERT_REALTIME();
                        $stmt_insert = $nodemcuaccess->NODE_MCU_INSERT_DATA();
                        if ($stmt_realtime == true && $stmt_insert == true) {
                            http_response_code(200);
                        } else {
                            http_response_code(503);
                        }
                    } else {
                        $stmt_realtime = $nodemcuaccess->NODE_MCU_INSERT_REALTIME();
                        $stmt_insert = $nodemcuaccess->NODE_MCU_INSERT_DATA();
                        if ($stmt_realtime == true && $stmt_insert == true) {
                            http_response_code(200);
                        } else {
                            http_response_code(503);
                        }
                    }
                }
            } else {
                echo json_encode("not have user from espkey");
            }
        }
    } else {
        $nodemcuaccess->NODE_INSERT_PRODUCT_KEY();
    }
} else {
    http_response_code(503);
}
