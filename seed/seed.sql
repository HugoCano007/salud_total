USE salud_total;

-- Usuario admin con clave: Admin123!
INSERT INTO usuarios (nombre, email, clave, rol)
VALUES (
    'Administrador',
    'admin@saludtotal.com',
    '$2y$10$QwL3O4fC1eW1kz0f2YCAUeVUpS9m6hC6Qp2y3n2P1b2vXw5aE6C5a', -- genera con password_hash
    'admin'
);

-- Proveedores
INSERT INTO proveedores (nombre, telefono, direccion) VALUES
('Farmacéutica MX', '9981234567', 'Av. Salud 123, Cancún'),
('Distribuidora Médica', '9989876543', 'Calle Hospital 45, Cancún');

-- Medicamentos
INSERT INTO medicamentos (nombre, categoria, cantidad, precio, proveedor_id) VALUES
('Amoxicilina', 'Antibiótico', 50, 120.00, 1),
('Ibuprofeno', 'Analgésico', 80, 75.50, 2);
