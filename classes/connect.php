<?php
date_default_timezone_set("Africa/Lusaka");
$time = time();


class connectCloud{
  //api data time getters variables

  private $day;
  private $year;
  private $month;
  private $hour;
  public function getCurrent_time() {
      return $this->current_time=time();
  }

    public function getDay() {
      return $this->day=date("d");
  }

  public function getMonth() {
      return $this->month=date("m");
  }

  public function getYear() {
      return $this->year=date("Y");
  }

  public function getHour() {
      return $this->hour=date("H");
  }

    //end
  //Database connection variables
  private $database;
  private $hostname;
  private $username;
  private $password;
  public $con;

  //JSON variable to hold the json file been returned from the api
  public $data;
  public $airport;


  public function __construct($hostname, $username, $password, $database){

    $this->username = $username;
    $this->password = $password;
    $this->hostname = $hostname;
    $this->database = $database;

    try {
        $this->con = new PDO('mysql:host='.$this->hostname.'; dbname='.$this->database, $this->username, $this->password);
        $this->con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        //echo "Connection Successful";
        $this->con->exec("SET CHARACTER SET utf8");  //  return all sql requests as UTF-8
    }
    catch (PDOException $err) {
        echo "Database connectin failed! Contact server admin!";
        $err->getMessage() . "<br/>";
        die();  //  terminate connection
    }
  }

  public function insertData($current_time){
      $this->time = $current_time;
      //SQL Query
      $insert = $this->con->prepare("INSERT INTO time (time_id, time) VALUES (0, :time)");
      $insert->bindParam(':time', $this->time); //Insert Data
      $insert->execute();
  }

  public function readData(){
    //SQL Query
    $read = $this->con->prepare("SELECT * FROM time WHERE time_id = :id");
    $read->bindValue(':id', 0);
    $read->execute();
    if ($read->rowCount() > 0){
        $check = $read->fetch(PDO::FETCH_ASSOC);
        return $old_time = $check['time'];
    } else {
      return $old_time = false;
    }
  }

  public function updateData($current_time){
    //SQL Query
    $update = $this->con->prepare("UPDATE time SET time = $current_time WHERE id = :id");
    $update->bindValue(':id', 0);
    $update->execute();
    echo $current_time.'<br>';
  }
  
  public function getApiData($year,$month,$day,$hour){
        $data = file_get_contents("https://api.flightstats.com/flex/flightstatus/rest/v2/json/airport/status/LUN/arr/$year/$month/$day/$hour?appId=f2fada9e&appKey=b3b6ad43212e524752691e4f5e2496ff&utc=false&numHours=5&codeType=FS&maxFlights=10");
        $mydata = json_decode($data);
        //writing to a file
        $myfile = fopen("flightdata.txt", "w") or die("Unable to open file!");
        fwrite($myfile,$data);
        fclose($myfile);
        //end
  }
}
?>



