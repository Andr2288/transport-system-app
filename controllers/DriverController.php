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

            // Перевірка на повідомлення після видалення
            $message = isset($_GET['message']) ? $_GET['message'] : null;
            $messageType = isset($_GET['type']) ? $_GET['type'] : 'error';

            $this->renderView('drivers/index.php', [
                'drivers' => $drivers,
                'message' => $message,
                'messageType' => $messageType
            ]);
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

    public function delete() {
        if ($_POST && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($id) {
                try {
                    if ($this->driverModel->delete($id)) {
                        $this->redirect('index.php?controller=drivers&message=' . urlencode('Водія успішно видалено') . '&type=success');
                    }
                } catch (PDOException $e) {
                    // Перевірка на помилку foreign key constraint
                    if (strpos($e->getMessage(), 'foreign key constraint') !== false ||
                        strpos($e->getMessage(), 'Cannot delete') !== false) {
                        $this->redirect('index.php?controller=drivers&message=' . urlencode('Неможливо видалити водія: він призначений на автомобіль або має рейси. Спочатку зніміть його з автомобіля та видаліть його рейси.') . '&type=error');
                    } else {
                        $this->redirect('index.php?controller=drivers&message=' . urlencode('Помилка видалення: ' . $e->getMessage()) . '&type=error');
                    }
                } catch (Exception $e) {
                    $this->redirect('index.php?controller=drivers&message=' . urlencode('Помилка: ' . $e->getMessage()) . '&type=error');
                }
            }
        }

        $this->redirect('index.php?controller=drivers');
    }
}
?>