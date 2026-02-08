const bcrypt = require("bcrypt");
const express = require("express");
const cors = require("cors");
const jwt = require("jsonwebtoken");
const mysql = require("mysql2/promise");
require("dotenv").config();

const app = express();
app.use(express.json());

// =========================
// CORS (local ahora, Vercel después)
// =========================
const allowedOrigins = [
  "http://localhost:8080",
  "http://127.0.0.1:8080",
  "http://localhost:5501",
  "http://127.0.0.1:5501"
  // luego añadimos tu dominio de Vercel aquí
];

app.use(
  cors({
    origin: (origin, cb) => {
      if (!origin) return cb(null, true); // curl/postman
      if (allowedOrigins.includes(origin)) return cb(null, true);
      return cb(new Error("Not allowed by CORS"));
    },
    credentials: false
  })
);

// =========================
// MySQL Pool (Railway)
// =========================
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

// TEMP: listar tablas (BORRAR después)
app.get("/api/dev/tables", async (req, res) => {
  try {
    const [rows] = await pool.query("SHOW TABLES");
    res.json({ ok: true, tables: rows });
  } catch (e) {
    res.status(500).json({ ok: false, message: e?.message || "error" });
  }
});
// TEMP: crear/actualizar password bcrypt para un usuario (BORRAR después)
app.post("/api/dev/setpass", async (req, res) => {
  try {
    const key = req.headers["x-import-key"];
    if (!process.env.IMPORT_KEY || key !== process.env.IMPORT_KEY) {
      return res.status(401).json({ ok: false, message: "unauthorized" });
    }

    const { nombre, newPass } = req.body || {};
    if (!nombre || !newPass) {
      return res.status(400).json({ ok: false, message: "faltan datos" });
    }

    const hash = await bcrypt.hash(newPass, 10);

    // intenta update; si no existe, lo crea
    const [r] = await pool.execute(
      "UPDATE usuarios SET pass = ? WHERE nombre = ?",
      [hash, nombre]
    );

    if (r.affectedRows === 0) {
      await pool.execute(
        "INSERT INTO usuarios (nombre, pass) VALUES (?, ?)",
        [nombre, hash]
      );
    }

    res.json({ ok: true });
  } catch (e) {
    res.status(500).json({ ok: false, message: e?.message || "error" });
  }
});


// =====================================================
// TEMP (BORRAR DESPUÉS): Import SQL dump con clave
// Endpoint: POST /api/dev/import-sql
// Header:   x-import-key: <IMPORT_KEY>
// =====================================================
app.post("/api/dev/import-sql", async (req, res) => {
  try {
    const key = req.headers["x-import-key"];
    if (!process.env.IMPORT_KEY || key !== process.env.IMPORT_KEY) {
      return res.status(401).json({ ok: false, message: "unauthorized" });
    }

    const fs = require("fs");
    const path = require("path");

    const sqlPath = path.join(__dirname, "sql", "vinyl_lab.sql");
    if (!fs.existsSync(sqlPath)) {
      return res.status(500).json({ ok: false, message: "sql file not found", sqlPath });
    }

    // lee dump y limpia CREATE DATABASE / USE
    const raw = fs.readFileSync(sqlPath, "utf8")
      .replace(/CREATE DATABASE[^;]*;/gi, "")
      .replace(/USE\s+[^;]*;/gi, "");

    // 1) quita comentarios tipo "-- ..."
    let cleaned = raw.replace(/^\s*--.*$/gm, "");

    // 2) quita bloques /*! ... */ (directivas de MySQL)
    cleaned = cleaned.replace(/\/\*![\s\S]*?\*\//g, "");

    // 3) quita bloques /* ... */ normales
    cleaned = cleaned.replace(/\/\*[\s\S]*?\*\//g, "");
    cleaned = cleaned.replace(/^\s*SET\s+time_zone\s*=.*$/gmi, "");
    cleaned = cleaned.replace(/^\s*START\s+TRANSACTION\s*;?$/gmi, "");


    // 4) ahora sí: split robusto por ";" al final de statement
    const stmts = cleaned
      .split(/;\s*(?:\r?\n|$)/)
      .map(s => s.trim())
      .filter(Boolean)
      .map(s => s + ";");


    // split por ; + salto de línea (dump típico phpMyAdmin)   

    const conn = await pool.getConnection();
    try {
      await conn.query("SET FOREIGN_KEY_CHECKS=0");

      let executed = 0;
      for (let i = 0; i < stmts.length; i++) {
        const q = stmts[i];
        await conn.query(q);
        executed++;
        if (executed % 50 === 0) console.log("[import]", executed, "statements");
      }


      await conn.query("SET FOREIGN_KEY_CHECKS=1");
      return res.json({ ok: true, statements: executed });
    } finally {
      conn.release();
    }
  } catch (e) {
    console.error(e);
    return res.status(500).json({
      ok: false,
      message: e?.message || "unknown error",
      code: e?.code || null,
      sqlMessage: e?.sqlMessage || null
    });
  }
});

// =========================
// AUTH: Login
// =========================
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
    const ok = await bcrypt.compare(pass, user.pass);

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
