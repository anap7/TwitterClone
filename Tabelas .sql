create table usuarios(
	id int not null primary key auto_increment,
    nome varchar(100) not null,
    senha varchar(32) not null
);

create table tweets(
	id int not null primary key auto_increment,
    id_usuario int not null,
    tweet varchar(140) not null,
    data datetime default current_timestamp 
);

create table usuarios_seguidores (
	id int not null primary key auto_increment,
    id_usuario int not null,
    id_usuario_seguido int not null
);

