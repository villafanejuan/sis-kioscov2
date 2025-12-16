<?php
/**
 * Clase Controller - Controlador base
 */

abstract class Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Cargar vista
     */
    protected function view($view, $data = [])
    {
        // Extraer datos para usar en la vista
        extract($data);

        // Cargar layout
        $viewFile = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("Vista no encontrada: {$view}");
        }

        require_once APP_PATH . '/Views/layouts/header.php';
        require_once $viewFile;
        require_once APP_PATH . '/Views/layouts/footer.php';
    }

    /**
     * Cargar vista sin layout
     */
    protected function viewOnly($view, $data = [])
    {
        extract($data);

        $viewFile = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("Vista no encontrada: {$view}");
        }

        require_once $viewFile;
    }

    /**
     * Retornar JSON
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirigir
     */
    protected function redirect($url, $message = null, $type = 'info')
    {
        if ($message) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = $type;
        }

        // Si la URL no empieza con http, agregar base URL
        if (strpos($url, 'http') !== 0) {
            $url = $this->url($url);
        }

        header("Location: {$url}");
        exit;
    }

    /**
     * Generar URL
     */
    protected function url($path = '')
    {
        $baseUrl = rtrim(APP_URL, '/');
        $path = ltrim($path, '/');
        return $baseUrl . '/' . $path;
    }

    /**
     * Verificar si es request AJAX
     */
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Verificar si es request POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verificar si es request GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Obtener input POST
     */
    protected function input($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Obtener input GET
     */
    protected function query($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Obtener todos los inputs POST
     */
    protected function all()
    {
        return $_POST;
    }

    /**
     * Validar CSRF
     */
    protected function validateCsrf()
    {
        $token = $this->input(CSRF_TOKEN_NAME);

        if (!$token) {
            throw new SecurityException('Token de seguridad no encontrado');
        }

        Security::validateCsrf($token);
    }

    /**
     * Verificar autenticación
     */
    protected function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjax()) {
                $this->json(['error' => 'No autenticado'], 401);
            } else {
                $this->redirect('index.php', 'Debes iniciar sesión', 'error');
            }
        }
    }

    /**
     * Verificar permiso
     */
    protected function requirePermission($permission)
    {
        $this->requireAuth();

        if (!$this->hasPermission($permission)) {
            Logger::warning('Acceso denegado por falta de permiso', [
                'user_id' => $_SESSION['user_id'],
                'permission' => $permission
            ]);

            if ($this->isAjax()) {
                $this->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
            } else {
                $this->redirect('dashboard.php', 'No tienes permiso para realizar esta acción', 'error');
            }
        }
    }

    /**
     * Verificar si el usuario tiene un permiso
     */
    protected function hasPermission($permission)
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Admin tiene todos los permisos
        if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
            return true;
        }

        // Verificar en caché de sesión
        if (!isset($_SESSION['permissions'])) {
            $this->loadUserPermissions();
        }

        return in_array($permission, $_SESSION['permissions']);
    }

    /**
     * Cargar permisos del usuario
     */
    private function loadUserPermissions()
    {
        $sql = "SELECT DISTINCT p.nombre 
                FROM permissions p
                INNER JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
                UNION
                SELECT p.nombre 
                FROM permissions p
                INNER JOIN user_permissions up ON p.id = up.permission_id
                WHERE up.user_id = ? AND up.granted = TRUE";

        $permissions = $this->db->fetchAll($sql, [$_SESSION['role_id'], $_SESSION['user_id']]);
        $_SESSION['permissions'] = array_column($permissions, 'nombre');
    }

    /**
     * Obtener mensaje flash
     */
    protected function getFlash()
    {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            $type = $_SESSION['flash_type'] ?? 'info';

            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);

            return ['message' => $message, 'type' => $type];
        }

        return null;
    }

    /**
     * Validar datos
     */
    protected function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);

            foreach ($ruleList as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field] = "El campo {$field} es obligatorio";
                    break;
                }

                if (strpos($r, 'min:') === 0 && isset($data[$field])) {
                    $min = (int) substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field] = "El campo {$field} debe tener al menos {$min} caracteres";
                        break;
                    }
                }

                if (strpos($r, 'max:') === 0 && isset($data[$field])) {
                    $max = (int) substr($r, 4);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field] = "El campo {$field} no debe exceder {$max} caracteres";
                        break;
                    }
                }

                if ($r === 'email' && isset($data[$field])) {
                    if (!Security::validateEmail($data[$field])) {
                        $errors[$field] = "El campo {$field} debe ser un email válido";
                        break;
                    }
                }

                if ($r === 'numeric' && isset($data[$field])) {
                    if (!is_numeric($data[$field])) {
                        $errors[$field] = "El campo {$field} debe ser numérico";
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}
