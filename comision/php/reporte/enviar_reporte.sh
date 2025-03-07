#!/bin/bash
# Establecer la zona horaria
export TZ='America/Argentina/Buenos_Aires'

#proceso parametrizado
export TOBA_DIR='/usr/local/proyectos/comision/vendor/siu-toba/framework'
export TOBA_INSTALACION_DIR='/usr/local/proyectos/comision/vendor/siu-toba/framework/instalacion'
export TOBA_INSTANCIA='desarrollo'
export TOBA_PROYECTO='comision'
#export USER=$8

export PATH=$PATH:${TOBA_INSTALACION_DIR}/../bin
cd /usr/local/proyectos/comision/php/reporte

# Crear un archivo de log con la fecha y hora actual
log_file="reporte_$(date +'%Y%m%d_%H%M%S').log"

# Ejecutar el comando y redirigir stdout y stderr al archivo de log
toba item ejecutar -t 1842000005 >> "$log_file" 2>&1

#exit