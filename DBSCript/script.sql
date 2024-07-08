DROP TABLE IF EXISTS producto;

-- Crear la tabla
CREATE TABLE producto (
    codigo_producto SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio INTEGER NOT NULL
);

-- INSERTAR ELEMENTOS EN LA TABLA PRODUCTO
INSERT INTO producto (nombre, precio) VALUES ('Cable eléctrico', 50000);
INSERT INTO producto (nombre, precio) VALUES ('Interruptor', 20000);
INSERT INTO producto (nombre, precio) VALUES ('Enchufe', 15000);
INSERT INTO producto (nombre, precio) VALUES ('Caja de fusibles', 100000);
INSERT INTO producto (nombre, precio) VALUES ('Lámpara LED', 30000);
INSERT INTO producto (nombre, precio) VALUES ('Transformador', 200000);
INSERT INTO producto (nombre, precio) VALUES ('Conector rápido', 5000);
INSERT INTO producto (nombre, precio) VALUES ('Canaleta', 25000);
INSERT INTO producto (nombre, precio) VALUES ('Detector de movimiento', 60000);
INSERT INTO producto (nombre, precio) VALUES ('Toma de corriente', 35000);

select * from producto;

DROP TABLE IF EXISTS inventario;

CREATE TABLE inventario (
    codigo_producto INTEGER PRIMARY key,
    FOREIGN KEY (codigo_producto) REFERENCES producto(codigo_producto),
    cantidad INTEGER NOT NULL
);

-- Insertar datos de ejemplo en inventario
INSERT INTO inventario (codigo_producto, cantidad) VALUES (1, 100);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (2, 50);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (3, 200);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (4, 30);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (5, 150);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (6, 20);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (7, 300);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (8, 120);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (9, 60);
INSERT INTO inventario (codigo_producto, cantidad) VALUES (10, 80);

select *from inventario ;

-- Ejemplo de Update para usar luego 
--UPDATE inventario
--SET cantidad = 120
--WHERE codigo_producto = 1;

DROP TABLE IF EXISTS factura;

CREATE TABLE factura (
    factura_id SERIAL PRIMARY KEY,
    total INTEGER NOT null,
    nombre_cliente VARCHAR (100) not null,
    cedula_cliente VARCHAR (100) not null
);

-- Insertar facturas de ejemplo
INSERT INTO factura (total, nombre_cliente, cedula_cliente) VALUES (946050, 'Andres Giron', '123456789');

select * from factura;

DROP TABLE IF EXISTS venta;

CREATE TABLE venta (
    venta_id SERIAL PRIMARY KEY,
    codigo_producto INTEGER not null,
    FOREIGN KEY (codigo_producto) REFERENCES producto(codigo_producto),
    factura_id INTEGER not null,
    FOREIGN KEY (factura_id) REFERENCES factura(factura_id),
    cantidad INTEGER NOT null,
    total_a_pagar INTEGER not null,
    total_a_pagar_con_iva INTEGER not null,
    fecha_de_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar datos de ejemplo en venta
INSERT INTO venta (codigo_producto, factura_id, cantidad, total_a_pagar, total_a_pagar_con_iva) VALUES (1, 1, 2, 100000, 119000);
INSERT INTO venta (codigo_producto, factura_id, cantidad, total_a_pagar, total_a_pagar_con_iva) VALUES (2, 1, 3, 60000, 71400);
INSERT INTO venta (codigo_producto, factura_id, cantidad, total_a_pagar, total_a_pagar_con_iva) VALUES (3, 1, 1, 15000, 17850);
INSERT INTO venta (codigo_producto, factura_id, cantidad, total_a_pagar, total_a_pagar_con_iva) VALUES (4, 1, 5, 500000, 595000);
INSERT INTO venta (codigo_producto, factura_id, cantidad, total_a_pagar, total_a_pagar_con_iva) VALUES (5, 1, 4, 120000, 142800);


select * from venta;

DROP TABLE IF exists iva;

CREATE TABLE iva (
    iva_id SERIAL PRIMARY KEY,
    iva_porcentaje INTEGER not null,
    fecha_iva TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

insert into IVA (iva_porcentaje) values (19);

select *from iva;




