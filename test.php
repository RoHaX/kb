<?php
include_once 'config/Database.php';

class Records {	
   
	private $recordsTable = 'tblKassa';
	public $id;
    public $mandant;
    public $beleg;
    public $datum;
    public $bezeichnung;
	public $eingang;
	public $ausgang;
	public $konto;
	public $kat;
	public $projekt;
	private $conn;

			
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function updateKategorie(){
		
		if($this->katid) {
	
			$stmt = $this->conn->prepare("
			UPDATE tabKategorie 
			SET katbez_kb = ?, katbez = ?, katart = ?, color = ?
			WHERE katid = ?");
	 
			$stmt->bind_param("ssisi", $this->katbez_kb, $this->katbez, $this->katart, $this->color, $this->katid);
			if($stmt->execute()){ 
				return true;
			}
			
		} else {
			return true;
		}
	}	
}

$database = new Database();
$db = $database->getConnection();

$record = new Records($db);

	$record->updateKategorie();

?>