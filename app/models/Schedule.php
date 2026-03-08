<?php
class Schedule {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function groupExists($teacher_id, $subject_id, $grupo, $periodo) {
        $this->db->query('SELECT id FROM schedules 
                          WHERE teacher_id = :tid 
                          AND subject_id = :sid 
                          AND grupo = :grp 
                          AND periodo = :per');
        $this->db->bind(':tid', $teacher_id);
        $this->db->bind(':sid', $subject_id);
        $this->db->bind(':grp', $grupo);
        $this->db->bind(':per', $periodo);
        
        $row = $this->db->single();
        return ($row) ? true : false;
    }

    public function addFullStructure($data) {
        try {
            $this->db->beginTransaction();

            // 1. Insertar el Grupo
            $this->db->query('INSERT INTO schedules (teacher_id, subject_id, grupo, turno, aula, periodo, especialidad) 
                              VALUES (:tid, :sid, :grp, :trn, :aul, :per, :esp)');
            $this->db->bind(':tid', $data['teacher_id']);
            $this->db->bind(':sid', $data['subject_id']);
            $this->db->bind(':grp', $data['grupo']);
            $this->db->bind(':trn', $data['turno']);
            $this->db->bind(':aul', $data['aula']);
            $this->db->bind(':per', $data['periodo']);
            $this->db->bind(':esp', $data['especialidad']);
            $this->db->execute();

            $schedule_id = $this->db->lastInsertId();

            // 2. Insertar Unidades y Actividades
            if (!empty($data['units'])) {
                foreach ($data['units'] as $u_index => $unit) {

                    // Nombre de unidad: usar el que escribió el usuario o generar uno
                    $nombreUnidad = !empty(trim($unit['unit_name'] ?? ''))
                        ? trim($unit['unit_name'])
                        : "Unidad $u_index";

                    $this->db->query('INSERT INTO unidades (schedule_id, nombre, orden) VALUES (:sid, :nom, :ord)');
                    $this->db->bind(':sid', $schedule_id);
                    $this->db->bind(':nom', $nombreUnidad);
                    $this->db->bind(':ord', intval($u_index));
                    $this->db->execute();

                    $unidad_id = $this->db->lastInsertId();

                    // 3. Insertar Actividades
                    foreach ($unit['activities'] as $act) {
                        $nombre = trim($act['name'] ?? '');
                        $peso   = intval($act['weight'] ?? 0);   // <-- siempre INT, nunca string vacío
                        $fecha  = !empty($act['due_date']) ? $act['due_date'] : null;

                        if (empty($nombre)) continue; // saltar filas sin nombre

                        $this->db->query('INSERT INTO actividades (unidad_id, nombre, ponderacion, fecha_entrega) 
                                          VALUES (:uid, :nom, :pon, :fecha)');
                        $this->db->bind(':uid',   $unidad_id);
                        $this->db->bind(':nom',   $nombre);
                        $this->db->bind(':pon',   $peso);
                        $this->db->bind(':fecha', $fecha);
                        $this->db->execute();
                    }
                }
            }

            return $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Schedule::addFullStructure error: ' . $e->getMessage());
            return false;
        }
    }

    public function getSchedulesWithNames()
    {
        $this->db->query('SELECT s.*, su.subject_name AS subject_name, s.grupo
                          FROM schedules s
                          LEFT JOIN subjects su ON s.subject_id = su.id');
        return $this->db->resultSet();
    }

    public function getScheduleById($id)
    {
        $this->db->query('SELECT s.*, su.subject_name AS subject_name, s.grupo
                          FROM schedules s
                          LEFT JOIN subjects su ON s.subject_id = su.id
                          WHERE s.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getActivitiesBySchedule($schedule_id)
    {
        $this->db->query('SELECT a.*, u.nombre AS unidad_nombre, u.id AS unidad_id
                          FROM actividades a
                          JOIN unidades u ON a.unidad_id = u.id
                          WHERE u.schedule_id = :sid
                          ORDER BY u.orden, a.id');
        $this->db->bind(':sid', $schedule_id);
        return $this->db->resultSet();
    }

    public function getUnitsWithActivities($schedule_id)
    {
        $this->db->query('SELECT * FROM unidades WHERE schedule_id = :sid ORDER BY orden');
        $this->db->bind(':sid', $schedule_id);
        $units = $this->db->resultSet();

        foreach ($units as $unit) {
            $this->db->query('SELECT * FROM actividades WHERE unidad_id = :uid');
            $this->db->bind(':uid', $unit->id);
            $unit->actividades = $this->db->resultSet();
        }

        return $units;
    }

    public function getStudentsBySchedule($schedule_id)
    {
        $this->db->query('SELECT u.id, u.name
                          FROM inscripciones i
                          JOIN users u ON i.user_id = u.id
                          WHERE i.schedule_id = :sid');
        $this->db->bind(':sid', $schedule_id);
        return $this->db->resultSet();
    }

    public function getUnidadBySchedule($schedule_id)
    {
        $this->db->query('SELECT * FROM unidades WHERE schedule_id = :sid LIMIT 1');
        $this->db->bind(':sid', $schedule_id);
        return $this->db->single();
    }

    public function getSchedulesByTeacher($teacher_id)
    {
        $this->db->query('SELECT * FROM schedules WHERE teacher_id = :tid');
        $this->db->bind(':tid', $teacher_id);
        return $this->db->resultSet();
    }

    public function getSubjects()
    {
        $this->db->query('SELECT id, subject_name FROM subjects ORDER BY subject_name');
        return $this->db->resultSet();
    }

    public function getUnitsBySchedule($scheduleId)
    {
        $this->db->query('SELECT * FROM unidades WHERE schedule_id = :scheduleId ORDER BY orden ASC');
        $this->db->bind(':scheduleId', $scheduleId);
        return $this->db->resultSet();
    }

    public function getActivitiesByUnit($scheduleId)
    {
        $this->db->query('
            SELECT 
                u.id AS unidad_id,
                u.nombre AS unidad_nombre,
                u.orden AS unidad_orden,
                a.id AS actividad_id,
                a.nombre AS actividad_nombre,
                a.ponderacion
            FROM unidades u
            INNER JOIN actividades a ON u.id = a.unidad_id
            WHERE u.schedule_id = :scheduleId
            ORDER BY u.orden ASC, a.id ASC
        ');
        $this->db->bind(':scheduleId', $scheduleId);
        $results = $this->db->resultSet();

        $actividadesPorUnidad = [];
        foreach ($results as $row) {
            $unidadId = $row->unidad_id;
            if (!isset($actividadesPorUnidad[$unidadId])) {
                $actividadesPorUnidad[$unidadId] = [
                    'id'         => $unidadId,
                    'nombre'     => $row->unidad_nombre,
                    'orden'      => $row->unidad_orden,
                    'actividades'=> []
                ];
            }
            $actividadesPorUnidad[$unidadId]['actividades'][] = (object)[
                'id'          => $row->actividad_id,
                'nombre'      => $row->actividad_nombre,
                'ponderacion' => $row->ponderacion
            ];
        }

        return array_values($actividadesPorUnidad);
    }

    public function getActivitiesWithGlobalWeight($scheduleId)
    {
        $this->db->query('
            SELECT a.*, u.id AS unidad_id, u.nombre AS unidad_nombre,
                   u.orden AS unidad_orden, a.ponderacion AS peso_global
            FROM actividades a
            INNER JOIN unidades u ON a.unidad_id = u.id
            WHERE u.schedule_id = :scheduleId
            ORDER BY u.orden ASC, a.id ASC
        ');
        $this->db->bind(':scheduleId', $scheduleId);
        return $this->db->resultSet();
    }

    public function addUnit($data)
    {
        $this->db->query('INSERT INTO unidades (schedule_id, nombre, orden) VALUES (:schedule_id, :nombre, :orden)');
        $this->db->bind(':schedule_id', $data['schedule_id']);
        $this->db->bind(':nombre',      $data['nombre']);
        $this->db->bind(':orden',       $data['orden']);
        return $this->db->execute();
    }

    public function updateUnit($data)
    {
        $this->db->query('UPDATE unidades SET nombre = :nombre, orden = :orden WHERE id = :id');
        $this->db->bind(':id',     $data['id']);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':orden',  $data['orden']);
        return $this->db->execute();
    }

    public function deleteUnit($unitId)
    {
        $this->db->query('DELETE FROM unidades WHERE id = :id');
        $this->db->bind(':id', $unitId);
        return $this->db->execute();
    }

    public function addActivity($data)
    {
        $this->db->query('INSERT INTO actividades (unidad_id, nombre, ponderacion, fecha_entrega) 
                          VALUES (:unidad_id, :nombre, :ponderacion, :fecha_entrega)');
        $this->db->bind(':unidad_id',    $data['unidad_id']);
        $this->db->bind(':nombre',       $data['nombre']);
        $this->db->bind(':ponderacion',  intval($data['ponderacion']));  // <-- intval
        $this->db->bind(':fecha_entrega',$data['fecha_entrega']);
        return $this->db->execute();
    }

    public function updateActivity($data)
    {
        $this->db->query('UPDATE actividades SET nombre = :nombre, ponderacion = :ponderacion, 
                          fecha_entrega = :fecha_entrega WHERE id = :id');
        $this->db->bind(':id',           $data['id']);
        $this->db->bind(':nombre',       $data['nombre']);
        $this->db->bind(':ponderacion',  intval($data['ponderacion']));  // <-- intval
        $this->db->bind(':fecha_entrega',$data['fecha_entrega']);
        return $this->db->execute();
    }

    public function deleteActivity($activityId)
    {
        $this->db->query('DELETE FROM actividades WHERE id = :id');
        $this->db->bind(':id', $activityId);
        return $this->db->execute();
    }

    public function getUnitById($unitId)
    {
        $this->db->query('SELECT * FROM unidades WHERE id = :id');
        $this->db->bind(':id', $unitId);
        return $this->db->single();
    }

    public function getNextUnitOrder($scheduleId)
    {
        $this->db->query('SELECT MAX(orden) as max_orden FROM unidades WHERE schedule_id = :scheduleId');
        $this->db->bind(':scheduleId', $scheduleId);
        $result = $this->db->single();
        return $result ? $result->max_orden + 1 : 1;
    }

    public function editSchedule($id, $data)
    {
        $this->db->query('UPDATE schedules SET grupo = :grupo, turno = :turno, aula = :aula, 
                          periodo = :periodo, especialidad = :especialidad WHERE id = :id');
        $this->db->bind(':id',          $id);
        $this->db->bind(':grupo',       $data['grupo']);
        $this->db->bind(':turno',       $data['turno']);
        $this->db->bind(':aula',        $data['aula']);
        $this->db->bind(':periodo',     $data['periodo']);
        $this->db->bind(':especialidad',$data['especialidad']);
        return $this->db->execute();
    }

    public function resetGrades($scheduleId)
    {
        $this->db->query('SELECT id FROM inscripciones WHERE schedule_id = :schedule_id');
        $this->db->bind(':schedule_id', $scheduleId);
        $inscripciones = $this->db->resultSet();

        if (empty($inscripciones)) return true;

        $ids = implode(',', array_map(fn($i) => intval($i->id), $inscripciones));

        $this->db->query("DELETE FROM resultados WHERE inscripcion_id IN ($ids)");
        $this->db->execute();

        $this->db->query("DELETE FROM bonus WHERE inscripcion_id IN ($ids)");
        return $this->db->execute();
    }

    public function countActivitiesBySchedule($scheduleId)
    {
        $this->db->query('SELECT COUNT(a.id) as total
                          FROM actividades a
                          INNER JOIN unidades u ON a.unidad_id = u.id
                          WHERE u.schedule_id = :scheduleId');
        $this->db->bind(':scheduleId', $scheduleId);
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }

    public function archiveSchedule($scheduleId)
    {
        $this->db->query('DELETE FROM schedules WHERE id = :id');
        $this->db->bind(':id', $scheduleId);
        return $this->db->execute();
    }
}