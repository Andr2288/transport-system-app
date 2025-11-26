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

            // Перевірка на повідомлення після видалення
            $message = isset($_GET['message']) ? $_GET['message'] : null;
            $messageType = isset($_GET['type']) ? $_GET['type'] : 'error';

            $this->renderView('trips/index.php', [
                'trips' => $trips,
                'message' => $message,
                'messageType' => $messageType
            ]);
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

    public function delete() {
        if ($_POST && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($id) {
                try {
                    if ($this->tripModel->delete($id)) {
                        $this->redirect('index.php?controller=trips&message=' . urlencode('Рейс успішно видалено') . '&type=success');
                    }
                } catch (PDOException $e) {
                    $this->redirect('index.php?controller=trips&message=' . urlencode('Помилка видалення: ' . $e->getMessage()) . '&type=error');
                } catch (Exception $e) {
                    $this->redirect('index.php?controller=trips&message=' . urlencode('Помилка: ' . $e->getMessage()) . '&type=error');
                }
            }
        }

        $this->redirect('index.php?controller=trips');
    }
}
?>