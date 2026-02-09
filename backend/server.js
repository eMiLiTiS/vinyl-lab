const express = require("express");
const cors = require("cors");
const jwt = require("jsonwebtoken");
const mysql = require("mysql2/promise");
const bcrypt = require("bcrypt");
const path = require("path");
require("dotenv").config();

const app = express();

// =========================
// Middlewares base
// =========================
app.use(express.json());

// =========================
// Static files
// /uploads -> public/uploads
// (server.js está en /backend)
// =========================
app.use(
  "/uploads",
  express.static(path.join(__dirname, "..", "public", "uploads"))
);

// =========================
// CORS
// =========================
const allowedOrigins = [
  process.env.CORS_ORIGIN,
  "http://localhost:8080",
  "http://127.0.0.1:8080",
  "http://localhost:5501",
  "http://127.0.0.1:5501",
  "https://vinyl-lab.vercel.app",
].filter(Boolean);

app.use(
  cors({
    origin: (origin, cb) => {
      if (!origin) return cb(null, true); // Postman / curl
      if (allowedOrigins.includes(origin)) return cb(null, true);
      return cb(new Error(`Not allowed by CORS: ${origin}`));
    },
    credentials: false,
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
  connectionLimit: 5,
});

// =========================
// Routes
// =========================
app.get("/", (req, res) => {
  res.json({
    ok: true,
    service: "vinyl-lab api",
    endpoints: [
      "/health",
      "/api/vinilos",
      "/api/auth/login",
      "/uploads/<archivo>",
    ],
  });
});

app.get("/health", (req, res) => {
  res.json({ ok: true });
});

app.get("/debug/uploads", (req, res) => {
  const fs = require("fs");
  const dir = path.join(__dirname, "..", "public", "uploads");

  try {
    const files = fs.readdirSync(dir);
    return res.json({ ok: true, dir, files });
  } catch (e) {
    return res.status(500).json({ ok: false, dir, error: e.message });
  }
});


app.get("/api/vinilos", async (req, res) => {
  try {
    const [rows] = await pool.query(
      "SELECT id, nombre, artista, precio, imagen FROM vinilos ORDER BY id DESC"
    );

    res.json({ ok: true, vinilos: rows });
  } catch (err) {
    console.error("[api/vinilos]", err);
    res.status(500).json({ ok: false, message: "Error interno" });
  }
});

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

    res.json({
      token,
      user: { id: user.id, nombre: user.nombre },
    });
  } catch (err) {
    console.error("[login]", err);
    res.status(500).json({ message: "Error interno" });
  }
});

// =========================
// Start server
// =========================
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`API running on port ${PORT}`);
});
