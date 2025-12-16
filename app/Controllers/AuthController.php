<?php
/**
 * Controlador de Autenticación
 */

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController extends Controller
{

    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard.php');
        }

        // Generar CSRF token
        Security::generateCsrf();

        $this->viewOnly('auth/login', [
            'csrf_token' => $_SESSION[CSRF_TOKEN_NAME],
            'error' => $this->getFlash()
        ]);
    }

    /**
     * Procesar login
     */
    public function login()
    {
        try {
            // Validar CSRF
            $this->validateCsrf();

            $username = Security::sanitize($this->input('username'));
            $password = $this->input('password');

            // Validar campos
            $errors = $this->validate([
                'username' => $username,
                'password' => $password
            ], [
                'username' => 'required|min:3',
                'password' => 'required|min:6'
            ]);

            if (!empty($errors)) {
                throw new Exception(implode(', ', $errors));
            }

            // Verificar intentos de login
            Security::checkLoginAttempts($username);

            // Verificar credenciales
            $user = $this->userModel->verifyCredentials($username, $password);

            if (!$user) {
                Security::logLoginAttempt($username, false);
                throw new Exception('Usuario o contraseña incorrectos');
            }

            // Verificar si está activo
            if (!$user['is_active']) {
                throw new Exception('Usuario inactivo. Contacte al administrador');
            }

            // Verificar si está bloqueado
            if ($this->userModel->isLocked($user['id'])) {
                throw new Exception('Usuario bloqueado temporalmente. Intenta más tarde');
            }

            // Login exitoso
            Security::logLoginAttempt($username, true);

            // Crear sesión
            $this->createSession($user);

            Logger::info('Usuario inició sesión', [
                'user_id' => $user['id'],
                'username' => $username
            ]);

            // Regenerar CSRF token
            Security::regenerateCsrf();

            $this->redirect('dashboard.php');

        } catch (Exception $e) {
            Logger::warning('Intento de login fallido', [
                'username' => $username ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            $this->redirect('index.php', $e->getMessage(), 'error');
        }
    }

    /**
     * Crear sesión de usuario
     */
    private function createSession($user)
    {
        // Regenerar ID de sesión para prevenir session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        // Obtener nombre del rol para checkAdmin()
        if ($user['role_id']) {
            $db = Database::getInstance();
            $stmt = $db->getConnection()->prepare("SELECT nombre FROM roles WHERE id = ?");
            $stmt->execute([$user['role_id']]);
            $role = $stmt->fetchColumn();
            $_SESSION['role'] = $role ? strtolower($role) : null;
        }

        // Cargar permisos
        $this->loadUserPermissions($user['id'], $user['role_id']);
    }

    /**
     * Cargar permisos del usuario
     */
    private function loadUserPermissions($userId, $roleId)
    {
        $permissions = $this->userModel->getPermissions($userId);
        $_SESSION['permissions'] = array_column($permissions, 'nombre');

        // Determinar si es admin
        $_SESSION['is_admin'] = ($roleId == 1);
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        Logger::info('Usuario cerró sesión', [
            'user_id' => $_SESSION['user_id'] ?? 'unknown'
        ]);

        // Destruir sesión
        session_unset();
        session_destroy();

        // Iniciar nueva sesión para mensaje flash
        session_start();

        $this->redirect('index.php', 'Sesión cerrada exitosamente', 'success');
    }

    /**
     * Verificar sesión (middleware)
     */
    public function checkSession()
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Verificar timeout de sesión
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];

            if ($inactive > SESSION_LIFETIME) {
                $this->logout();
                return false;
            }
        }

        // Actualizar última actividad
        $_SESSION['last_activity'] = time();

        return true;
    }
}
