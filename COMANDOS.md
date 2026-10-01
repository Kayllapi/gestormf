# Comando para actualizar y migrar en la base de datos

```bash
# migrar una base de datos
mysql -u sgm_user -p sgm < database/sql/01102027090000alter_tarifario_producto_agencia.sql

# verificar si se subio la base de datos
mysql -u sgm_user -p sgm -e "DESCRIBE tipo_garantia_penalidad; DESCRIBE subtipo_garantia_noprendaria_ii_penalidad;"
```

# Comando para sacar copia de base de datos

```bash
# Sacar copia de base de datos
mysqldump -u sgm_user -p --no-tablespaces sgm | gzip > ~/sgm_$(date +%Y%m%d_%H%M).sql.gz

# Copiar al local la base de datos
scp -i ~/.ssh/akami_vps root@172.237.61.130:/root/sgm_20260818_0120.sql.gz "C:\Users\USER\Downloads\"
```
