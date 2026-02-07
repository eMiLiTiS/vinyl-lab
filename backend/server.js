const bcrypt = require("bcrypt");

const express = require("express");
const cors = require("cors");
const jwt = require("jsonwebtoken");
const mysql = require("mysql2/promise");
require("dotenv").config();

const app = express();
app.use(express.json());

// CORS: permite tu frontend de Vercel
const allowedOrigins = [
  "http://localhost:8080",
  "http://127.0.0.1:8080",
  "http://localhost:5501",
  "http://127.0.0.1:5501"
];

app.use(
  cors({
    origin: (origin, cb) => {
      // Permite llamadas sin origin (curl/postman)
      if (!origin) return cb(null, true);
      if (allowedOrigins.includes(origin)) return cb(null, true);
      return cb(new Error("Not allowed by CORS"));
    },
    credentials: false
  })
);


// Pool MySQL (Railway o el proveedor que uses)
const pool = mysql.createPool({
  host: process.env.MYSQLHOST,
  user: process.env.MYSQLUSER,
  password: process.env.MYSQLPASSWORD,
  database: process.env.MYSQLDATABASE,
  port: Number(process.env.MYSQLPORT || 3306),
  waitForConnections: true,
  connectionLimit: 5
});

app.get("/health", (req, res) => res.json({ ok: true }));

// TEMP: import SQL one-shot (BORRAR después)
app.post("/api/dev/import-sql", async (req, res) => {
  try {
    const fs = require("fs");
    const path = require("path");

    const sqlPath = path.join(__dirname, "..", "sql", "vinyl_lab.sql");
    const sql = fs.readFileSync(sqlPath, "utf8")
      .replace(/CREATE DATABASE[^;]*;/gi, "")
      .replace(/USE\s+[^;]*;/gi, "");

    const conn = await pool.getConnection();
    try {
      await conn.query("SET FOREIGN_KEY_CHECKS=0");
      await conn.query(sql);
      await conn.query("SET FOREIGN_KEY_CHECKS=1");
    } finally {
      conn.release();
    }

    res.json({ ok: true });
  } catch (e) {
    console.error(e);
    res.status(500).json({ ok: false, message: e.message });
  }
});
// Si tu dump tiene procedimientos o DELIMITER, esta función los maneja mejor.




app.post("/api/auth/login", async (req, res) => {
  const { nombre, pass } = req.body || {};
  if (!nombre || !pass) {
    return res.status(400).json({ message: "Faltan datos" });
  }

  try {
    const [rows] = await pool.execute(
      "SELECT id, nombre, pass FROM usuarios WHERE nombre = ? LIMIT 1",
      [nombre]
    );

    if (!rows.length) {
      return res.status(401).json({ message: "Usuario o contraseña incorrectos" });
    }

    const user = rows[0];

    // Si tu BD aún guarda contraseñas en claro:
    const ok = await bcrypt.compare(pass, user.pass);

    // Si guardas con password_hash, luego lo cambiamos a bcrypt.
    if (!ok) {
      return res.status(401).json({ message: "Usuario o contraseña incorrectos" });
    }

    const token = jwt.sign(
      { uid: user.id, nombre: user.nombre },
      process.env.JWT_SECRET,
      { expiresIn: "2h" }
    );

    return res.json({
      token,
      user: { id: user.id, nombre: user.nombre }
    });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ message: "Error interno" });
  }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log("API running on port", PORT));
