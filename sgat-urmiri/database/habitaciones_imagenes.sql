-- ============================================================
-- Actualización de imágenes de habitaciones
-- Ejecutar después de copiar las imágenes habi1.jpg - habi4.jpg
-- a uploads/habitaciones/
-- ============================================================

UPDATE habitaciones SET imagen = 'habi1.jpg' WHERE nombre LIKE '%Doble%' OR nombre LIKE '%Estándar%';
UPDATE habitaciones SET imagen = 'habi2.jpg' WHERE nombre LIKE '%Romántica%' OR nombre LIKE '%Suite%' AND nombre NOT LIKE '%Premium%';
UPDATE habitaciones SET imagen = 'habi3.jpg' WHERE nombre LIKE '%Familiar%' OR nombre LIKE '%Premium%';
UPDATE habitaciones SET imagen = 'habi4.jpg' WHERE nombre LIKE '%Simple%' OR nombre LIKE '%Ejecutiva%';

-- O reinsertar con imágenes correctas:
-- Suite Termal Premium → habi3.jpg (moderna, camas twin, vista verde)
-- Cabaña Familiar      → habi3.jpg
-- Habitación Doble     → habi1.jpg (doble, camas twin clásico)
-- Suite Romántica      → habi2.jpg (cama king, naranja, jardín)
-- Habitación Simple    → habi4.jpg (cama king, minimalista)
-- Cabaña Ejecutiva     → habi4.jpg
