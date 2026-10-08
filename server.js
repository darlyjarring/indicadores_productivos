const express = require('express');
const { Pool } = require('pg');
const cors = require('cors');
const bodyParser = require('body-parser');
const path = require('path');

const app = express();
app.use(cors());
app.use(bodyParser.json());

// Configura los datos de conexión a tu base de datos PostgreSQL
const pool = new Pool({
    user: 'tu_usuario',
    host: 'localhost',
    database: 'tu_base_datos',
    password: 'tu_contraseña',
    port: 5432,
});

// Servir archivos estáticos (tus HTMLs en la misma carpeta)
app.use(express.static(path.join(__dirname)));

// API de Autenticación
app.post('/api/login', async (req, res) => {
    const { username, password } = req.body;

    try {
        // Consulta SQL para validar usuario, contraseña y obtener su Rol
        const query = `
            SELECT u.id, u.nombre_completo, r.nombre_rol 
            FROM usuarios u
            JOIN usuario_roles ur ON u.id = ur.usuario_id
            JOIN roles r ON ur.rol_id = r.id
            WHERE u.username = $1 AND u.password_hash = $2 AND u.activo = TRUE
        `;
        
        const result = await pool.query(query, [username, password]);

        if (result.rows.length > 0) {
            const user = result.rows[0];
            res.json({
                success: true,
                mensaje: "Acceso concedido",
                nombre_completo: user.nombre_completo,
                nombre_rol: user.nombre_rol
            });
        } else {
            res.status(401).json({ success: false, mensaje: "Usuario o contraseña incorrectos" });
        }
    } catch (err) {
        console.error(err);
        res.status(500).json({ success: false, mensaje: "Error en el servidor de base de datos" });
    }
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
