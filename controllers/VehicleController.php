<?php
require_once 'BaseController.php';
require_once 'models/VehicleModel.php';

class VehicleController extends BaseController {
    private $vehicleModel;

    public function __construct() {
        $this->vehicleModel = new VehicleModel();
    }

    public function index() {
        try {
            $vehicles = $this->vehicleModel->getVehiclesWithDrivers();
            $this->renderView('vehicles/index.php', ['vehicles' => $vehicles]);
        } catch (Exception $e) {
            $this->renderView('vehicles/index.php', [
                'vehicles' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function create() {
        if ($_POST) {
            $data = [
                'license_plate' => $this->validateInput($_POST['license_plate']),
                'brand' => $this->validateInput($_POST['brand']),
                'model' => $this->validateInput($_POST['model']),
                'year' => (int)$_POST['year'],
                'capacity' => (float)$_POST['capacity']
            ];

            if ($this->vehicleModel->create($data)) {
                $this->redirect('index.php?controller=vehicles');
            }
        }

        $this->renderView('vehicles/create.php');
    }
}
?>