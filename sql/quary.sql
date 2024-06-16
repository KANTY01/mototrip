-- Table: users

-- DROP TABLE users;

CREATE TABLE users
(
  id serial NOT NULL,
  username character varying(50) NOT NULL,
  password character varying(255) NOT NULL,
  email character varying(100) NOT NULL,
  created_at timestamp without time zone DEFAULT now(),
  is_admin boolean DEFAULT false,
  CONSTRAINT users_pkey PRIMARY KEY (id),
  CONSTRAINT users_email_key UNIQUE (email),
  CONSTRAINT users_username_key UNIQUE (username)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE users
  OWNER TO postgres;



-- Table: profiles

-- DROP TABLE profiles;

CREATE TABLE profiles
(
  user_id integer NOT NULL,
  bio text,
  profile_picture character varying(255),
  CONSTRAINT profiles_pkey PRIMARY KEY (user_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE profiles
  OWNER TO postgres;


-- Table: meetings

-- DROP TABLE meetings;

CREATE TABLE meetings
(
  id serial NOT NULL,
  title character varying(100) NOT NULL,
  description text NOT NULL,
  date date NOT NULL,
  location character varying(100) NOT NULL,
  created_by integer,
  "time" time with time zone,
  user_id integer,
  image character varying(255),
  CONSTRAINT meetings_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE meetings
  OWNER TO postgres;

-- Trigger: meeting_changes_trigger on meetings

-- DROP TRIGGER meeting_changes_trigger ON meetings;

CREATE TRIGGER meeting_changes_trigger
  AFTER INSERT OR UPDATE OR DELETE
  ON meetings
  FOR EACH ROW
  EXECUTE PROCEDURE log_meeting_changes();

