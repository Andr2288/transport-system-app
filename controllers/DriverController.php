<?php
require_once 'BaseController.php';
require_once 'models/DriverModel.php';

class DriverController extends BaseController {
    private $driverModel;
    
    public function __construct() {
        $this->driverModel = new DriverModel();
    }
    
    public function index() {
        try {
            $drivers = $this->driverModel->getDriversWithVehicles();
            $this->renderView('drivers/index.php', ['drivers' => $drivers]);
        } catch (Exception $e) {
            $this->renderView('drivers/index.php', [
                'drivers' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function create() {
        if ($_POST) {
            $data = [
                'name' => $this->validateInput($_POST['name']),
                'license_number' => $this->validateInput($_POST['license_number']),
                'phone' => $this->validateInput($_POST['phone']),
                'experience_years' => (int)$_POST['experience_years'],
                'category' => $this->validateInput($_POST['category'])
            ];
            
            if ($this->driverModel->create($data)) {
                $this->redirect('index.php?controller=drivers');
            }
        }
        
        $this->renderView('drivers/create.php');
    }
}
?>
