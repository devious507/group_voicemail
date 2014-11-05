DROP TABLE status CASCADE;
DROP TABLE messages_notes CASCADE;
DROP TABLE messages_logfile CASCADE;
DROP TABLE messages CASCADE;
DROP TABLE call_types CASCADE;
DROP TABLE users CASCADE;

CREATE TABLE call_types(
	call_type_id serial unique not null,
	call_type varchar
);
INSERT INTO call_types (call_type,call_type_id) VALUES ('unclassified',0);
INSERT INTO call_types (call_type) VALUES ('Billing Question');
INSERT INTO call_types (call_type) VALUES ('CATV Troubleshooting');
INSERT INTO call_types (call_type) VALUES ('INET Troubleshooting');
INSERT INTO call_types (call_type) VALUES ('PHONE Troubleshooting');
INSERT INTO call_types (call_type) VALUES ('New Account');
INSERT INTO call_types (call_type) VALUES ('Add / Remove Services');
INSERT INTO call_types (call_type) VALUES ('GUESTSUITE');

CREATE TABLE users (
	user_id serial unique not null,
	username varchar,
	password varchar,
	email varchar,
	admin boolean default false,
	active boolean default true
);

INSERT INTO users (username,password,email,user_id) VALUES ('SYSTEM','lskdjflskdj',NULL,0);
INSERT INTO users (username,password,email,admin) VALUES ('paulo','sbob','paulo@visionsystems.tv',true);
INSERT INTO users (username,password,email) VALUES ('paigeh','tor50','paigeh@visionsystems.tv');
INSERT INTO users (username,password,email) VALUES ('robbiew','poohbear','robbiew@visionsystems.tv');
INSERT INTO users (username,password,email) VALUES ('karam','balloon','karam@visionsystems.tv');
INSERT INTO users (username,password,email,admin) VALUES ('darlab','tate2','darlab@visionsystems.tv',true);
INSERT INTO users (username,password,email,admin) VALUES ('daveb','daytime','daveb@visionsystems.tv',true);
INSERT INTO users (username,password,email) VALUES ('johnw','outcast47','johnw@visionsystems.tv');
INSERT INTO users (username,password,email) VALUES ('shanem','remspell','shanem@visionsystems.tv');
INSERT INTO users (username,password,email) VALUES ('doneldak','books','doneldak@visionsystems.tv');

CREATE TABLE status (
	status_id serial unique not null,
	status varchar
);
INSERT INTO status (status_id,status) VALUES (0,'NEW');
INSERT INTO status (status) VALUES ('OPEN');
INSERT INTO status (status) VALUES ('CLOSED');
INSERT INTO status (status) VALUES ('RE-OPEN');
INSERT INTO status (status) VALUES ('FINAL');

CREATE table messages (
	message_id serial unique not null,
	message_create timestamp default now(),
	caller_name varchar,
	caller_address varchar,
	caller_city varchar,
	caller_state varchar,
	caller_zip varchar,
	caller_phone varchar,
	call_type_id integer references call_types(call_type_id) default 0,
	current_owner integer references users(user_id) default 0,
	status_id integer references status(status_id) default 0,
	filename varchar
);


CREATE TABLE messages_notes (
	messages_notes_id serial unique not null,
	entry_time timestamp default now() not null,
	message_id integer references messages(message_id),
	note text
);

CREATE TABLE messages_logfile (
	messages_logfile_id serial,
	message_id integer references messages(message_id),
	action_timestamp timestamp default now(),
	action_description varchar
);

