# Comando para actualizar y migrar en la base de datos

```bash
# migrar una base de datos
mysql -u sgm_user -p sgm < database/sql/11082026150000create_credito_pagoanticipado_historial.sql

# verificar si se subio la base de datos
mysql -u sgm_user -p sgm -e "DESCRIBE credito_pagoanticipado_historial;"
```
