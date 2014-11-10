update status set status='COMPLETED' WHERE status_id=2;
update status set status='CLOSED' WHERE status_id=3;
alter table users add column max_status_id integer;
update users set max_status_id=2;
update users set max_status_id=3 WHERE username='paigeh' OR username='robbiew' OR username='karam';
update users set max_status_id=4 WHERE username='paulo' OR username='darlab' OR username='doneldak' OR username='daveb';
ALTER TABLE users DROP COLUMN admin;
ALTER TABLE users ALTER COLUMN max_status_id SET DEFAULT 2;
