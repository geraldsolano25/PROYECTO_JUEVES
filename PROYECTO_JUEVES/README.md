# AlertaComunal

AlertaComunal es una aplicacion web para el reporte y seguimiento de incidentes comunitarios. Su proposito es facilitar que los ciudadanos comuniquen problemas urbanos de su zona, como danos en infraestructura, acumulacion de basura, fallas en servicios publicos, situaciones de seguridad o afectaciones ambientales.

El sistema esta construido con PHP, MySQL, HTML, CSS y Bootstrap. La aplicacion permite registrar usuarios, iniciar sesion, crear reportes, clasificarlos por categoria, registrar ubicacion aproximada, agregar evidencia mediante URL y consultar el estado de los casos reportados.

El proyecto contempla dos tipos principales de usuario: ciudadanos y administradores. Los ciudadanos pueden registrar incidentes, revisar sus propios reportes y apoyar reportes comunitarios mediante votos. Los administradores cuentan con herramientas para gestionar usuarios, categorias, reportes, seguimientos y votos, ademas de actualizar el estado y la prioridad de los casos.

La base de datos principal se llama `PROYECTO_JUEVES` y organiza la informacion en tablas para usuarios, categorias, reportes, seguimiento de reportes y votos. El esquema inicial se encuentra en `sql/incidentes_comunitarios.sql`, mientras que la configuracion de conexion y creacion basica de tablas esta en `config/database.php`.

Como idea general, el proyecto busca representar una solucion de participacion ciudadana digital, donde los reportes no solo se registran, sino que tambien pueden ser priorizados, revisados y acompanados durante su proceso de atencion.
