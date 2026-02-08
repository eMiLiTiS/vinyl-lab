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
  "http://127.0.0.1:5501",

  // Vercel (producción + previews)
  "https://vinyl-lab.vercel.app",
  "https://vinyl-lab-git-main-emilitiss-projects.vercel.app",

  // (opcional) si quieres dejar el antiguo mientras pruebas:
  "https://vinyl-l8hckwlw9-emilitiss-projects.vercel.app"
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

app.get("/version", (req, res) => {
  res.json({
    ok: true,
    cwd: process.cwd(),
    dirname: __dirname,
    hasLoginRoute: true,
    railwayCommit: process.env.RAILWAY_GIT_COMMIT_SHA || null,
    nodeEnv: process.env.NODE_ENV || null
  });
});

app.get("/routes", (req, res) => {
  const routes = [];

  // Express 4 suele usar app._router; Express 5 puede usar app.router
  const router = app._router || app.router;
  const stack = router?.stack || [];

  for (const layer of stack) {
    if (!layer.route) continue;

    const path = layer.route.path;
    const methods = Object.keys(layer.route.methods || {})
      .filter((m) => layer.route.methods[m])
      .map((m) => m.toUpperCase());

    routes.push({ path, methods });
  }

  res.json({
    ok: true,
    routerType: app._router ? "_router" : (app.router ? "router" : "none"),
    count: routes.length,
    routes
  });
});




const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log("API running on port", PORT));
