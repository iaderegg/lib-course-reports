Libreria para reportes de curso

El archivo lib.php del presente repositorio contiene algunas funciones que permite extraer datos acerca de 
los estudiantes mejor calificados, las secciones y el estado de finalización de los módulos de un curso.

Se encuentran las siguientes funciones: 

    get_best_students: Dado un ID de curso y un número entero, retorna los n estudiantes mejor calificados, con su correspondiente
                        posición en la lista.

    get_info_course_sections: Dado un ID de curso retorna las secciones del curso y su cantidad correspondiente de recursos y el porcentaje de      
                              finalización de dichos recursos por sección.