<?php
require_once 'BaseController.php';
require_once 'models/TripModel.php';

class TripController extends BaseController {
    private $tripModel;
    
    public function __construct() {
        $this->tripModel = new TripModel();
    }
    
    public function index() {
        try {
            $trips = $this->tripModel->getTripsWithDetails();
            $this->renderView('trips/index.php', ['trips' => $trips]);
        } catch (Exception $e) {
            $this->renderView('trips/index.php', [
                'trips' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function active() {
        try {
            $trips = $this->tripModel->getActiveTrips();
            $this->renderView('trips/active.php', ['trips' => $trips]);
        } catch (Exception $e) {
            $this->renderView('trips/active.php', [
                'trips' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
}
?>
