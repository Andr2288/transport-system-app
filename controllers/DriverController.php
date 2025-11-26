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
        $errors = [];

        if ($_POST) {
            // Валідація на стороні сервера
            $name = $this->validateInput($_POST['name']);
            $licenseNumber = $this->validateInput($_POST['license_number']);
            $phone = $this->validateInput($_POST['phone']);
            $experienceYears = (int)$_POST['experience_years'];
            $category = $this->validateInput($_POST['category']);

            // Перевірка імені
            if (empty($name)) {
                $errors['name'] = 'Ім\'я обов\'язкове';
            } elseif (strlen($name) < 2) {
                $errors['name'] = 'Ім\'я має містити мінімум 2 символи';
            } elseif (!preg_match('/^[а-яА-ЯіІїЇєЄ\s]+$/u', $name)) {
                $errors['name'] = 'Ім\'я може містити лише українські літери та пробіли';
            }

            // Перевірка номера посвідчення
            if (empty($licenseNumber)) {
                $errors['license_number'] = 'Номер посвідчення обов\'язковий';
            } elseif (!preg_match('/^[A-Z]{2}\d{6}$/i', $licenseNumber)) {
                $errors['license_number'] = 'Невірний формат номера посвідчення (AA123456)';
            } else {
                // Перевірка на унікальність
                try {
                    $existing = $this->driverModel->getByLicenseNumber(strtoupper($licenseNumber));
                    if ($existing) {
                        $errors['license_number'] = 'Водій з таким номером посвідчення вже існує';
                    }
                } catch (Exception $e) {
                    // Ігноруємо помилки перевірки
                }
            }

            // Перевірка телефону
            if (empty($phone)) {
                $errors['phone'] = 'Номер телефону обов\'язковий';
            } elseif (!preg_match('/^\+380\d{9}$/', $phone)) {
                $errors['phone'] = 'Невірний формат телефону (+380XXXXXXXXX)';
            }

            // Перевірка досвіду
            if ($experienceYears < 0 || $experienceYears > 50) {
                $errors['experience_years'] = 'Досвід має бути від 0 до 50 років';
            }

            // Перевірка категорії
            $validCategories = ['A', 'B', 'C', 'D', 'BE', 'CE', 'DE', 'C+E', 'D+E'];
            if (empty($category)) {
                $errors['category'] = 'Категорія обов\'язкова';
            } elseif (!in_array($category, $validCategories)) {
                $errors['category'] = 'Невірна категорія водійських прав';
            }

            // Якщо немає помилок - створюємо запис
            if (empty($errors)) {
                $data = [
                    'name' => $name,
                    'license_number' => strtoupper($licenseNumber),
                    'phone' => $phone,
                    'experience_years' => $experienceYears,
                    'category' => $category
                ];

                try {
                    if ($this->driverModel->create($data)) {
                        $this->redirect('index.php?controller=drivers');
                    } else {
                        $errors['general'] = 'Помилка створення запису';
                    }
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $errors['license_number'] = 'Водій з таким номером посвідчення вже існує';
                    } else {
                        $errors['general'] = 'Помилка бази даних: ' . $e->getMessage();
                    }
                } catch (Exception $e) {
                    $errors['general'] = 'Помилка: ' . $e->getMessage();
                }
            }
        }

        $this->renderView('drivers/form.php', [
            'errors' => $errors,
            'formData' => $_POST
        ]);
    }

    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $errors = [];

        if (!$id) {
            $this->redirect('index.php?controller=drivers');
        }

        // Отримати дані водія
        $driver = $this->driverModel->getById($id);
        if (!$driver) {
            $this->redirect('index.php?controller=drivers');
        }

        if ($_POST) {
            // Захист від IDOR
            $postedId = isset($_POST['driver_id']) ? (int)$_POST['driver_id'] : 0;
            if ($postedId !== $id) {
                $this->redirect('index.php?controller=drivers');
            }

            // Валідація (аналогічна до create)
            $name = $this->validateInput($_POST['name']);
            $licenseNumber = $this->validateInput($_POST['license_number']);
            $phone = $this->validateInput($_POST['phone']);
            $experienceYears = (int)$_POST['experience_years'];
            $category = $this->validateInput($_POST['category']);

            if (empty($name)) {
                $errors['name'] = 'Ім\'я обов\'язкове';
            } elseif (strlen($name) < 2) {
                $errors['name'] = 'Ім\'я має містити мінімум 2 символи';
            } elseif (!preg_match('/^[а-яА-ЯіІїЇєЄ\s]+$/u', $name)) {
                $errors['name'] = 'Ім\'я може містити лише українські літери та пробіли';
            }

            if (empty($licenseNumber)) {
                $errors['license_number'] = 'Номер посвідчення обов\'язковий';
            } elseif (!preg_match('/^[A-Z]{2}\d{6}$/i', $licenseNumber)) {
                $errors['license_number'] = 'Невірний формат номера посвідчення (AA123456)';
            } else {
                // Перевірка на унікальність (крім поточного запису)
                $existing = $this->driverModel->getByLicenseNumber(strtoupper($licenseNumber));
                if ($existing && $existing['id'] != $id) {
                    $errors['license_number'] = 'Водій з таким номером посвідчення вже існує';
                }
            }

            if (empty($phone)) {
                $errors['phone'] = 'Номер телефону обов\'язковий';
            } elseif (!preg_match('/^\+380\d{9}$/', $phone)) {
                $errors['phone'] = 'Невірний формат телефону (+380XXXXXXXXX)';
            }

            if ($experienceYears < 0 || $experienceYears > 50) {
                $errors['experience_years'] = 'Досвід має бути від 0 до 50 років';
            }

            $validCategories = ['A', 'B', 'C', 'D', 'BE', 'CE', 'DE', 'C+E', 'D+E'];
            if (empty($category)) {
                $errors['category'] = 'Категорія обов\'язкова';
            } elseif (!in_array($category, $validCategories)) {
                $errors['category'] = 'Невірна категорія водійських прав';
            }

            if (empty($errors)) {
                $data = [
                    'name' => $name,
                    'license_number' => strtoupper($licenseNumber),
                    'phone' => $phone,
                    'experience_years' => $experienceYears,
                    'category' => $category
                ];

                try {
                    if ($this->driverModel->update($id, $data)) {
                        $this->redirect('index.php?controller=drivers');
                    } else {
                        $errors['general'] = 'Помилка оновлення запису';
                    }
                } catch (Exception $e) {
                    $errors['general'] = 'Помилка: ' . $e->getMessage();
                }
            }
        }

        // Отримуємо дані про автомобіль водія для відображення
        $driverWithVehicle = $this->driverModel->getDriversWithVehicles();
        foreach ($driverWithVehicle as $driverData) {
            if ($driverData['id'] == $id) {
                $driver = $driverData;
                break;
            }
        }

        $this->renderView('drivers/form.php', [
            'driver' => $driver,
            'errors' => $errors
        ]);
    }

    public function delete() {
        if ($_POST && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($id) {
                try {
                    $this->driverModel->delete($id);
                } catch (Exception $e) {
                    // Помилки ігноруємо для простоти
                }
            }
        }

        $this->redirect('index.php?controller=drivers');
    }
}
?>