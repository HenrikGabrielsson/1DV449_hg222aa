DROP TABLE IF EXISTS "message";
CREATE TABLE "message" ("id" INTEGER PRIMARY KEY  NOT NULL , "priority " INTEGER NOT NULL , "createddate " INTEGER NOT NULL , "title" VARCHAR NOT NULL , "exactlocation " VARCHAR NOT NULL , "description " VARCHAR NOT NULL , "latitude" REAL NOT NULL , "longitude" REAL NOT NULL , "category " INTEGER NOT NULL , "subcategory " VARCHAR NOT NULL );
