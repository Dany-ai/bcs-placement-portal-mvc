PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS placements (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  employer_id INTEGER NOT NULL,

  title TEXT NOT NULL,
  description TEXT NOT NULL,
  skills_required TEXT NULL,
  salary TEXT NULL,
  location TEXT NULL,
  start_date TEXT NULL,
  end_date TEXT NULL,

  status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','approved','rejected')),

  reviewed_by INTEGER NULL,
  reviewed_at TEXT NULL,
  approved_at TEXT NULL,
  rejected_at TEXT NULL,

  created_at TEXT NOT NULL DEFAULT (datetime('now')),

  FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_placements_status ON placements(status);
CREATE INDEX IF NOT EXISTS idx_placements_employer ON placements(employer_id);