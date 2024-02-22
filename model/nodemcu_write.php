<?php
class NODEMCU_WRITE
{
    private $connDB;
    public function __construct($connDB)
    {
        $this->connDB = $connDB;
    }
    public $userId;
    public $espkey;
    public $flowrate;
    public $pressure;
    public $solenoid;
    public $ai_status;
    public $alert;
    public $time_status;
    public $d_totalwater;
    public $m_totalwater;
    public $date;
    public $time;
 

    public function NODE_MCU_GET_ID(){
        $strSQL = "SELECT user_Id FROM user_tb WHERE user_tb.user_Product_ID = :espkey ";
        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":espkey", $this->espkey);

        if ($stmt->execute()) {
            return $stmt;
        }
    }
    public function NODE_INSERT_PRODUCT_KEY(){
        $strSQL = "INSERT INTO product_tb(product_Id) VALUES (:espkey)";
        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":espkey", $this->espkey);
        if ($stmt->execute()) {
            return true;
        }else{
            return false;
        }
    }
    public function NODE_GET_PRODUCT_KEY(){
        $strSQL = "SELECT product_Id FROM product_tb WHERE product_Id=:espkey";
        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":espkey", $this->espkey);
        if ($stmt->execute()) {
            return $stmt;
        }
    }

    public function UPDATE_TIME(){
        $strSQL = "UPDATE control_time_tb SET control_Date_On='0000-00-00', control_Time_On='00:00:00', `control_Date_OFF`='0000-00-00', control_Time_OFF='00:00:00' WHERE user_control_Time_Id=:userId";
        $this->userId = intval(htmlspecialchars(strip_tags($this->userId)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":userId", $this->userId);
        if ($stmt->execute()) {
            $strSQL = "UPDATE realtime_tb SET realtime_Time='0' WHERE product_key=:espkey";
            $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
            $stmt = $this->connDB->prepare($strSQL);
            $stmt->bindParam(":espkey", $this->espkey);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
 
    public function NODE_MCU_INSERT_REALTIME() {
        $strSQL = "SELECT product_tb.product_Id, realtime_tb.product_key
                   FROM product_tb
                   INNER JOIN realtime_tb ON product_tb.product_Id = realtime_tb.product_key
                   WHERE product_tb.product_Id = :espkey AND realtime_tb.product_key = :espkey";
        
        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":espkey", $this->espkey);
        $stmt->execute();
    
        if ($stmt->rowCount() == 0) {
            $strSQL = "INSERT INTO realtime_tb (realtime_Solenoid, realtime_AI, realtime_Time, realtime_Flowrate, realtime_Pressure, product_key, alert) 
                       VALUES (:solenoid, :ai_status, :time_status, :flowrate, :pressure, :espkey ,:alert)";
    
            $this->solenoid = floatval(htmlspecialchars(strip_tags($this->solenoid)));
            $this->ai_status = intval(htmlspecialchars(strip_tags($this->ai_status)));
            $this->time_status = intval(htmlspecialchars(strip_tags($this->time_status)));
            $this->flowrate = floatval(htmlspecialchars(strip_tags($this->flowrate)));
            $this->pressure = floatval(htmlspecialchars(strip_tags($this->pressure)));
            $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
            $this->alert = intval(htmlspecialchars(strip_tags($this->alert)));
            $stmt = $this->connDB->prepare($strSQL);
            $stmt->bindParam(":solenoid", $this->solenoid);
            $stmt->bindParam(":ai_status", $this->ai_status);
            $stmt->bindParam(":time_status", $this->time_status);
            $stmt->bindParam(":flowrate", $this->flowrate);
            $stmt->bindParam(":pressure", $this->pressure);
            $stmt->bindParam(":espkey", $this->espkey);
            $stmt->bindParam(":alert", $this->alert);
    
            return $stmt->execute();
        } else {
            $strSQL = "UPDATE realtime_tb SET 
                       realtime_Solenoid=:solenoid,
                       realtime_AI=:ai_status,
                       realtime_Time=:time_status,
                       product_key=:espkey,
                       realtime_Flowrate=:flowrate,
                       realtime_Pressure=:pressure,
                       alert=:alert";
    
            $this->solenoid = floatval(htmlspecialchars(strip_tags($this->solenoid)));
            $this->ai_status = intval(htmlspecialchars(strip_tags($this->ai_status)));
            $this->time_status = intval(htmlspecialchars(strip_tags($this->time_status)));
            $this->flowrate = floatval(htmlspecialchars(strip_tags($this->flowrate)));
            $this->pressure = floatval(htmlspecialchars(strip_tags($this->pressure)));
            $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
            $this->alert = intval(htmlspecialchars(strip_tags($this->alert)));
            $stmt = $this->connDB->prepare($strSQL);
            $stmt->bindParam(":solenoid", $this->solenoid);
            $stmt->bindParam(":ai_status", $this->ai_status);
            $stmt->bindParam(":time_status", $this->time_status);
            $stmt->bindParam(":flowrate", $this->flowrate);
            $stmt->bindParam(":pressure", $this->pressure);
            $stmt->bindParam(":espkey", $this->espkey);
            $stmt->bindParam(":alert", $this->alert);
    
            return $stmt->execute();
        }
    }
    


    public function NODE_MCU_INSERT_DATA(){
        $strSQL="INSERT INTO product_details_tb(
        Product_Details_Month_Flowrate,
        Product_Details_Month_Pressure,
        Product_Details_Month_Water_Use,
        Product_Details_Result_Solenoid,
        Product_Details_Result_Time,
        Product_Details_Result_Ai,
        date,
        time,
        product_key,
        Product_Details_Day_Water_Use) 
        VALUES(:flowrate,:pressure,:m_totalwater,:solenoid,:time_status,:ai_status,:date,:time,:espkey,:d_totalwater)";

        $this->flowrate=floatval(htmlspecialchars(strip_tags($this->flowrate)));
        $this->pressure=floatval(htmlspecialchars(strip_tags($this->pressure)));
        $this->m_totalwater=floatval(htmlspecialchars(strip_tags($this->m_totalwater)));
        $this->solenoid=intval(htmlspecialchars(strip_tags($this->solenoid)));
        $this->time_status=intval(htmlspecialchars(strip_tags($this->time_status)));
        $this->ai_status=intval(htmlspecialchars(strip_tags($this->ai_status)));
        $this->date=htmlspecialchars(strip_tags($this->date));
        $this->time=htmlspecialchars(strip_tags($this->time));
        $this->espkey=intval(htmlspecialchars(strip_tags($this->espkey)));
        $this->d_totalwater=floatval(htmlspecialchars(strip_tags($this->d_totalwater)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":flowrate", $this->flowrate);
        $stmt->bindParam(":pressure", $this->pressure);
        $stmt->bindParam(":m_totalwater", $this->m_totalwater);
        $stmt->bindParam(":solenoid", $this->solenoid);
        $stmt->bindParam(":time_status", $this->time_status);
        $stmt->bindParam(":ai_status", $this->ai_status);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":time", $this->time);
        $stmt->bindParam(":espkey", $this->espkey);
        $stmt->bindParam(":d_totalwater", $this->d_totalwater);
        if(  $stmt->execute()){
            return true;
          }else{
            return false;
          }

        
    }

}
