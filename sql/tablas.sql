CREATE TABLE usuarios(
    id TINYINT unsigned PRIMARY KEY AUTO_INCREMENT,
    correo VARCHAR(50) NOT NULL UNIQUE,
    pw VARCHAR(80) NOT NULL,
    nombre VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE tareas(
    idTar SMALLINT unsigned PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(50) NOT NULL,
    detalle VARCHAR(80) NULL,
    fecha DATE NULL,
    archivo BLOB NULL,
    idUsuario TINYINT unsigned,
    CONSTRAINT FK_usuario_tareas FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
);

CREATE TABLE subtareas(
    idSub SMALLINT unsigned PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(50) NOT NULL,
    detalle VARCHAR(80) NULL,
    fecha DATE NULL,
    completada BOOLEAN NULL,
    idTar SMALLINT unsigned,
    CONSTRAINT FK_subtareas_tareas FOREIGN KEY (idTar) REFERENCES tareas(idTar) ON DELETE CASCADE ON UPDATE CASCADE
);