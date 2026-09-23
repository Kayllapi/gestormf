-- Agrega el control de intentos de acceso al Login.
-- intentos_maximo: intentos fallidos permitidos antes de deshabilitar al usuario (0 = sin límite).
-- intentos_fallidos: contador de intentos fallidos consecutivos (se reinicia al ingresar correctamente
-- o al reactivar el usuario desde Usuarios > Editar).

ALTER TABLE `users`
  ADD COLUMN `intentos_maximo` INT UNSIGNED NOT NULL DEFAULT 3,
  ADD COLUMN `intentos_fallidos` INT UNSIGNED NOT NULL DEFAULT 0;
