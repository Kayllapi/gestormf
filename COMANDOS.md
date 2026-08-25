# Comando para actualizar y migrar en la base de datos

```bash
# migrar una base de datos
mysql -u sgm_user -p sgm < database/sql/11082026150000create_credito_pagoanticipado_historial.sql
mysql -u sgm_user -p sgm < database/sql/21082026120000alter_credito_pagoanticipado_historial_add_modalidad.sql

# verificar si se subio la base de datos
mysql -u sgm_user -p sgm -e "DESCRIBE 21082026120000alter_credito_pagoanticipado_historial_add_modalidad;"
```

# Comando para sacar copia de base de datos

```bash
# Sacar copia de base de datos
mysqldump -u sgm_user -p --no-tablespaces sgm | gzip > ~/sgm_$(date +%Y%m%d_%H%M).sql.gz

# Copiar al local la base de datos
scp -i ~/.ssh/akami_vps root@172.237.61.130:/root/sgm_20260818_0120.sql.gz "C:\Users\USER\Downloads\"
```
