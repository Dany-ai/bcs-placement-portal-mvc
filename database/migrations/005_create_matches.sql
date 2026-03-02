PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS matches (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  student_id INTEGER NOT NULL,
  placement_id INTEGER NOT NULL,
  score INTEGER NOT NULL CHECK (score >= 0 AND score <= 100),
  created_at TEXT NOT NULL DEFAULT (datetime('now')),

  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (placement_id) REFERENCES placements(id) ON DELETE CASCADE,

  UNIQUE(student_id, placement_id)
);

CREATE INDEX IF NOT EXISTS idx_matches_student ON matches(student_id);
CREATE INDEX IF NOT EXISTS idx_matches_placement ON matches(placement_id);