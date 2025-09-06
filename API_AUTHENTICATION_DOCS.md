# API Authentication - Laravel Sanctum

## Configuración de Autenticación

Esta API utiliza **Laravel Sanctum** para la autenticación basada en tokens. Las rutas públicas no requieren autenticación, mientras que las rutas administrativas están protegidas.

## Endpoints de Autenticación

### 1. Registro de Usuario
```http
POST /api/auth/register
Content-Type: application/json
```

**Body:**
```json
{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Validaciones:**
- `name`: requerido, string, máximo 255 caracteres
- `email`: requerido, email válido, único en la base de datos
- `password`: requerido, mínimo 8 caracteres, confirmación requerida

**Respuesta Exitosa (201):**
```json
{
    "success": true,
    "message": "Usuario registrado exitosamente",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan@example.com",
            "created_at": "2025-09-05T22:20:50.000000Z"
        },
        "token": "1|randomTokenString",
        "token_type": "Bearer"
    }
}
```

### 2. Inicio de Sesión
```http
POST /api/auth/login
Content-Type: application/json
```

**Body:**
```json
{
    "email": "juan@example.com",
    "password": "password123"
}
```

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Login exitoso",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan@example.com"
        },
        "token": "2|randomTokenString",
        "token_type": "Bearer"
    }
}
```

**Respuesta de Error (401):**
```json
{
    "success": false,
    "message": "Credenciales incorrectas"
}
```

### 3. Obtener Información del Usuario Autenticado
```http
GET /api/auth/me
Authorization: Bearer {token}
```

**Respuesta (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan@example.com",
            "email_verified_at": null,
            "created_at": "2025-09-05T22:20:50.000000Z",
            "updated_at": "2025-09-05T22:20:50.000000Z"
        }
    }
}
```

### 4. Cerrar Sesión (Revocar Token Actual)
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

**Respuesta (200):**
```json
{
    "success": true,
    "message": "Logout exitoso"
}
```

### 5. Cerrar Sesión en Todos los Dispositivos
```http
POST /api/auth/logout-all
Authorization: Bearer {token}
```

**Respuesta (200):**
```json
{
    "success": true,
    "message": "Logout de todos los dispositivos exitoso"
}
```

### 6. Renovar Token
```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

**Respuesta (200):**
```json
{
    "success": true,
    "message": "Token renovado exitosamente",
    "data": {
        "token": "3|newRandomTokenString",
        "token_type": "Bearer"
    }
}
```

## Uso de Tokens

### Incluir Token en Requests
Todos los endpoints protegidos requieren incluir el token en el header `Authorization`:

```http
Authorization: Bearer {your-token-here}
```

### Ejemplo con cURL:
```bash
curl -X GET "http://your-app.com/api/admin/products" \
     -H "Accept: application/json" \
     -H "Authorization: Bearer 1|randomTokenString"
```

### Ejemplo con JavaScript:
```javascript
const token = localStorage.getItem('auth_token');

fetch('/api/admin/products', {
    method: 'GET',
    headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

## Respuestas de Error de Autenticación

### Token No Proporcionado o Inválido (401):
```json
{
    "success": false,
    "message": "Token no válido o no proporcionado",
    "error": "Unauthorized"
}
```

### Token Expirado o Revocado (401):
```json
{
    "success": false,
    "message": "No autorizado",
    "error": "Unauthorized"
}
```

## Flujo de Autenticación Completo

### 1. Registro/Login
```javascript
// Registro
const register = async (userData) => {
    const response = await fetch('/api/auth/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    });
    
    const data = await response.json();
    
    if (data.success) {
        localStorage.setItem('auth_token', data.data.token);
        localStorage.setItem('user', JSON.stringify(data.data.user));
    }
    
    return data;
};

// Login
const login = async (credentials) => {
    const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(credentials)
    });
    
    const data = await response.json();
    
    if (data.success) {
        localStorage.setItem('auth_token', data.data.token);
        localStorage.setItem('user', JSON.stringify(data.data.user));
    }
    
    return data;
};
```

### 2. Hacer Requests Autenticados
```javascript
const makeAuthenticatedRequest = async (url, options = {}) => {
    const token = localStorage.getItem('auth_token');
    
    const defaultHeaders = {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    };
    
    if (token) {
        defaultHeaders['Authorization'] = `Bearer ${token}`;
    }
    
    const response = await fetch(url, {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    });
    
    // Si el token es inválido, redirigir al login
    if (response.status === 401) {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = '/login';
        return;
    }
    
    return response.json();
};

// Ejemplo de uso
const getProducts = () => makeAuthenticatedRequest('/api/admin/products');
const createProduct = (productData) => makeAuthenticatedRequest('/api/admin/products', {
    method: 'POST',
    body: JSON.stringify(productData)
});
```

### 3. Logout
```javascript
const logout = async () => {
    const token = localStorage.getItem('auth_token');
    
    if (token) {
        await fetch('/api/auth/logout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
    }
    
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    window.location.href = '/login';
};
```

## Usuarios de Prueba

Para facilitar las pruebas, se han creado los siguientes usuarios:

- **Email:** `admin@example.com`
- **Password:** `password123`

- **Email:** `test@example.com`  
- **Password:** `password123`

## Seguridad

### Buenas Prácticas Implementadas:

1. **Tokens Seguros**: Los tokens se generan de forma segura usando Sanctum
2. **Validación Robusta**: Form Requests con validación personalizada
3. **Manejo de Errores**: Respuestas consistentes y informativas
4. **Rate Limiting**: Laravel aplica rate limiting por defecto
5. **CORS**: Configurado para desarrollo y producción
6. **Hash de Contraseñas**: Passwords hasheadas con bcrypt

### Recomendaciones de Producción:

1. Usar HTTPS en producción
2. Configurar CORS apropiadamente
3. Implementar rate limiting adicional
4. Configurar expiración de tokens
5. Implementar refresh tokens para mayor seguridad
6. Añadir logging de actividad de autenticación

## Configuración Adicional

### Variables de Entorno (.env):
```env
# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,your-frontend-domain.com
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
```

### Middleware de Rutas:
- **Rutas Públicas**: Sin middleware de autenticación
- **Rutas Admin**: `auth:sanctum` middleware aplicado
- **Rutas de Auth**: Mixtas (algunas requieren auth, otras no)
