const fs = require("fs");
const path = require("path");
require("dotenv").config({ path: path.join(__dirname, ".env") });
const mysql = require("mysql2/promise");

function splitSql(sql) {
  // quita CREATE DATABASE / USE que molestan en managed DBs
  const cleaned = sql
    .replace(/CREATE DATABASE[^;]*;/gi, "")
    .replace(/USE\s+[^;]*;/gi, "");

  // split simple por ; (suficiente para dumps típicos de phpMyAdmin)
  // OJO: si tu dump tiene procedimientos/DELIMITER, me avisas.
  return cleaned
    .split(/;\s*\n/)
    .map(s => s.trim())
    .filter(Boolean)
    .map(s => s + ";");
}

async function main() {
  const sqlPath = path.join(__dirname, "sql", "vinyl_lab.sql");
  const sql = fs.readFileSync(sqlPath, "utf8");
  const stmts = splitSql(sql);

  const conn = await mysql.createConnection({
    host: process.env.MYSQLHOST,
    user: process.env.MYSQLUSER,
    password: process.env.MYSQLPASSWORD,
    database: process.env.MYSQLDATABASE,
    port: Number(process.env.MYSQLPORT),
    connectTimeout: 30000
  });

  try {
    // evita que la sesión se caiga en imports largos
    await conn.query("SET SESSION sql_mode = ''");
    await conn.query("SET SESSION foreign_key_checks = 0");

    let ok = 0;
    for (let i = 0; i < stmts.length; i++) {
      const q = stmts[i];
      try {
        await conn.query(q);
        ok++;
      } catch (e) {
        // muestra el statement que falla (primeros 200 chars)
        console.error("❌ Statement failed #", i + 1, e.message);
        console.error(q.slice(0, 200));
        throw e;
      }

      // progreso cada 50
      if ((i + 1) % 50 === 0) {
        console.log(`... ${i + 1}/${stmts.length} statements`);
      }
    }

    await conn.query("SET SESSION foreign_key_checks = 1");
    console.log("✅ Import OK:", ok, "statements");
  } finally {
    await conn.end();
  }
}

main().catch((e) => {
  console.error("❌ Import failed:", e.message);
  process.exit(1);
});
