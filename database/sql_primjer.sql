BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "animes" ("id" integer primary key autoincrement not null, "item_id" integer not null, "mal_id" integer not null, "episode_number" integer not null, "is_watched" tinyint(1) not null default '0', "created_at" datetime, "updated_at" datetime, foreign key("item_id") references "items"("id"));
CREATE TABLE IF NOT EXISTS "collections" ("id" integer primary key autoincrement not null, "user_id" integer not null, "name" varchar not null, "type" varchar not null, "description" text, "is_finished" tinyint(1) not null default 'false', "default" tinyint(1) not null default 'false', "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id"));
CREATE TABLE IF NOT EXISTS "items" ("id" integer primary key autoincrement not null, "collection_id" integer not null, "mal_id" integer not null, "title" varchar not null, "type" varchar not null, "status" varchar not null default 'planned', "created_at" datetime, "updated_at" datetime, foreign key("collection_id") references "collections"("id"));
CREATE TABLE IF NOT EXISTS "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);
CREATE TABLE IF NOT EXISTS "mangas" ("id" integer primary key autoincrement not null, "item_id" integer not null, "mal_id" integer not null, "is_read" tinyint(1) not null default '0', "created_at" datetime, "updated_at" datetime, foreign key("item_id") references "items"("id"));
CREATE TABLE IF NOT EXISTS "roles" ("id" integer primary key autoincrement not null, "name" varchar not null, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "first_name" varchar not null, "last_name" varchar not null, "email" varchar, "password" varchar not null, "role_id" integer not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "avatar" varchar, foreign key("role_id") references "roles"("id"));
INSERT INTO "roles" ("id","name","created_at","updated_at") VALUES (1,'Admin','2025-04-10 15:43:00','2025-04-10 15:43:00'),
 (2,'User','2025-04-10 15:43:00','2025-04-10 15:43:00');
INSERT INTO "users" ("id","name","first_name","last_name","email","password","role_id","remember_token","created_at","updated_at","avatar") VALUES (1,'AmerBaja','Amer','Bajić','amer.bajic@mvp.ba','$2y$12$x3E6odT6CZi867SErbZUieBCQwwPZSjv6HZ6zugdl3.c1CpYfR0aq',2,NULL,'2025-04-10 16:02:18','2025-06-07 21:51:14','https://cdn.myanimelist.net/images/characters/3/276473.webp');
CREATE INDEX "jobs_queue_index" on "jobs" ("queue");
CREATE UNIQUE INDEX "users_email_unique" on "users" ("email");
CREATE UNIQUE INDEX "users_name_unique" on "users" ("name");
COMMIT;
