# Libreria para reportes de curso

_El archivo lib.php del presente repositorio contiene algunas funciones que permite extraer datos acerca de 
los estudiantes mejor calificados, las secciones y el estado de finalización de los módulos de un curso para un estudiante en particular._

Se encuentran las siguientes funciones: 

```
    get_best_students:  Dado un ID de curso y un número entero, retorna los n estudiantes mejor calificados, con su correspondiente
                        posición en la lista.

    get_best_students(32542, 3)

    Retornará;

    Array (
        [98656] => stdClass Object
            (
                [userid] => 98656
                [finalgrade] => 100.00000
                [position] => 1
            )

        [98625] => stdClass Object
            (
                [userid] => 98625
                [finalgrade] => 100.00000
                [position] => 2
            )

        [98641] => stdClass Object
            (
                [userid] => 98641
                [finalgrade] => 100.00000
                [position] => 3
            )
    )
```
```
get_best_students_nosql:  A diferencia de la función anterior (get_best_students) esta no utiliza una función sql diseñada por este desarrollador
                          y hace uso de las funciones propias de las librerias de calificación de Moodle. A continuación un ejemplo de como funciona:
    
    get_best_students_nosql(32542, 3)

    Retornará:

    Array(
        [0] => Array
            (
                [userid] => 98625
                [finalgrade] => 100.00000
                [position] => 1
            )

        [1] => Array
            (
                [userid] => 98641
                [finalgrade] => 100.00000
                [position] => 2
            )

        [2] => Array
            (
                [userid] => 98656
                [finalgrade] => 100.00000
                [position] => 3
            )
    )
```
```
get_info_course_sections_by_user: Dado un ID de curso retorna las secciones del curso y su cantidad correspondiente de recursos y el porcentaje de      
                                  finalización de dichos recursos por sección.
```