create table Askare (
	AskareID	serial not null primary key,
	Nimi		varchar(20) not null,
	Kirjaushetki	timestamp(0) not null,
	Tärkeysaste	integer,
	DL		timestamp(0),
	Luokka	integer references Luokka (LuokkaID),
);

create table Luokka (
	LuokkaID	serial not null primary key,
	Nimi		varchar(10) not null,
	Ylaluokka	integer references Luokka (LuokkaID),
);
