<?php


class NODEMCU_READ
{
    private $connDB;

    public function __construct($connDB)
    {
        $this->connDB = $connDB;
    }


    public $espkey;
    public $userId;
///////////////////////////////////////
    public $Date_Back_One_Week;
    public $Date_Back_Two_Week;
    public $Date_Back_Three_Week;
    public $Date_Back_Four_Week;
    public $Time_Now;



    //ใช้espkey หาid ผู้ใช้
    public function NODE_MCU_GET_ID()
    {
        $strSQL = "SELECT user_Id FROM user_tb WHERE user_tb.user_Product_ID = :espkey ";
        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":espkey", $this->espkey);

        if ($stmt->execute()) {
            return $stmt;
        }
    }
    //ถ้ามี ดึงข้อมูล
    public function NODE_MCU_GET_REALTIME()
    {
        $strSQL = "SELECT control_Date_On, control_Time_On, control_Date_OFF, control_Time_OFF,
        control_Solenoid, control_Ai
        FROM control_solenoid_tb, control_time_tb, control_ai_tb
        WHERE control_time_tb.user_control_Time_Id= :userId
        AND control_solenoid_tb.user_control_Solenoid_Id = :userId
        AND control_ai_tb.user_control_Ai_Id= :userId";

        $this->userId = intval(htmlspecialchars(strip_tags($this->userId)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":userId", $this->userId);
         
        if ($stmt->execute()) {
            $data[]=$stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        }
    }
    //ถ้ามี ดึงข้อมูลสุดท้ายที่อยูในฐานข้อมูล
    public function NODE_MCU_GET_LAST_TIME()
    {
        $strSQL = "SELECT Product_Details_Day_Water_Use,Product_Details_Month_Water_Use 
        FROM product_details_tb 
        WHERE product_key= :espkey 
        ORDER BY Product_Details_Month_Id 
        DESC LIMIT 1";

        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);
        $stmt->bindParam(":espkey", $this->espkey);

        if ($stmt->execute()) {
            return $stmt;
        }
    }

     
    public function NODE_MCU_GET_DATA_TIME_NOW_ONEWEEK()
    {
        $strSQL = "SELECT AVG(Product_Details_Month_Pressure) AS average_pressure
           FROM product_details_tb 
           WHERE date = :Date_Back_One_Week 
           AND time LIKE CONCAT(:Time_Now, '%')
           AND product_key = :espkey";

        $this->Date_Back_One_Week = htmlspecialchars(strip_tags($this->Date_Back_One_Week));
        $this->Time_Now = htmlspecialchars(strip_tags($this->Time_Now));
        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);

        $stmt->bindParam(":Date_Back_One_Week", $this->Date_Back_One_Week);
        $stmt->bindParam(":Time_Now", $this->Time_Now);
        $stmt->bindParam(":espkey", $this->espkey);

        if ($stmt->execute()) {
            return $stmt;
        }
 }
    public function NODE_MCU_GET_DATA_TIME_NOW_TWOWEEK()
    {
        $strSQL = "SELECT AVG(Product_Details_Month_Pressure) AS average_pressure
           FROM product_details_tb 
           WHERE date = :Date_Back_Two_Week 
           AND time LIKE CONCAT(:Time_Now, '%')
           AND product_key = :espkey";

        $this->Date_Back_Two_Week = htmlspecialchars(strip_tags($this->Date_Back_Two_Week));
        $this->Time_Now = htmlspecialchars(strip_tags($this->Time_Now));
        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);

        $stmt->bindParam(":Date_Back_Two_Week", $this->Date_Back_Two_Week);
        $stmt->bindParam(":Time_Now", $this->Time_Now);
        $stmt->bindParam(":espkey", $this->espkey);

        if ($stmt->execute()) {
            return $stmt;
        }
 }
    public function NODE_MCU_GET_DATA_TIME_NOW_THREEWEEK()
    {
        $strSQL = "SELECT AVG(Product_Details_Month_Pressure) AS average_pressure
           FROM product_details_tb 
           WHERE date = :Date_Back_Three_Week 
           AND time LIKE CONCAT(:Time_Now, '%')
           AND product_key = :espkey";

        $this->Date_Back_Three_Week = htmlspecialchars(strip_tags($this->Date_Back_Three_Week));
        $this->Time_Now = htmlspecialchars(strip_tags($this->Time_Now));
        $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
        $stmt = $this->connDB->prepare($strSQL);

        $stmt->bindParam(":Date_Back_Three_Week", $this->Date_Back_Three_Week);
        $stmt->bindParam(":Time_Now", $this->Time_Now);
        $stmt->bindParam(":espkey", $this->espkey);

        if ($stmt->execute()) {
            return $stmt;
        }
 }
 public function NODE_MCU_GET_DATA_TIME_NOW_FOURWEEK()
 {
     $strSQL = "SELECT AVG(Product_Details_Month_Pressure) AS average_pressure
        FROM product_details_tb 
        WHERE date = :Date_Back_Four_Week 
        AND time LIKE CONCAT(:Time_Now, '%')
        AND product_key = :espkey";

     $this->Date_Back_Four_Week = htmlspecialchars(strip_tags($this->Date_Back_Four_Week));
     $this->Time_Now = htmlspecialchars(strip_tags($this->Time_Now));
     $this->espkey = intval(htmlspecialchars(strip_tags($this->espkey)));
     $stmt = $this->connDB->prepare($strSQL);

     $stmt->bindParam(":Date_Back_Four_Week", $this->Date_Back_Four_Week);
     $stmt->bindParam(":Time_Now", $this->Time_Now);
     $stmt->bindParam(":espkey", $this->espkey);

     if ($stmt->execute()) {
         return $stmt;
     }
 }
}