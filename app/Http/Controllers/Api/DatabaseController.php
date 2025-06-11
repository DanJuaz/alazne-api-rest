<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class DatabaseController extends BaseApiController
{
    public function checkConnection(): JsonResponse
    {
        try {
            $host = env('DB_HOST');
            $dbname = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');

            $connection = mysqli_connect($host, $username, $password, $dbname);

            if ($connection) {
                mysqli_close($connection);
                return $this->success([
                    'connection' => 'success',
                    'message' => 'Database connection is working'
                ]);
            }

            throw new \Exception('Connection failed');
        } catch (\Exception $e) {
            return $this->error([
                'connection' => 'failed',
                'message' => 'Could not connect to the database',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function availableBookings(): JsonResponse
    {
        date_default_timezone_set('Europe/Madrid');
        // Return data of available bookings

        $host = env('DB_HOST');
        $dbname = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        $resultado = [
            // Se devolveran los siguientes  5 dás laborables (L-V)
            '1' => [],
            '2' => [],
            '3' => [],
            '4' => [],
            '5' => [],

        ];

        try {
            $conn = mysqli_connect($host, $username, $password, $dbname);
            if (!$conn) throw new \Exception('Connection failed');

            foreach (['1' => 0, '2' => 1, '3' => 2, '4' => 3, '5' => 4] as $dia => $offset) {
                $fecha = date('Y-m-d', strtotime("+$offset day"));

                // Revisar si es sábado o domingo
                if (date('N', strtotime($fecha)) >= 6) {
                    $resultado[$dia]['fecha'] = $fecha;
                    continue;
                }
                
                $ocupadas = [];

                $sql = "SELECT fecha_hora_inicio FROM reservas 
                    WHERE DATE(fecha_hora_inicio) = '$fecha' 
                      AND HOUR(fecha_hora_inicio) BETWEEN 10 AND 12
                      AND status = 1";

                $query = mysqli_query($conn, $sql);
                if (!$query) throw new \Exception(mysqli_error($conn));

                while ($row = mysqli_fetch_assoc($query)) {
                    $hora = date('H:i', strtotime($row['fecha_hora_inicio']));
                    $ocupadas[] = $hora;
                }

                // Enviar la fecha
                $resultado[$dia]['fecha'] = $fecha;

                // Generar turnos de 30 min entre 10:00 y 13:00
                $inicio = strtotime("$fecha 10:00");
                $fin = strtotime("$fecha 13:00");

                for ($t = $inicio; $t < $fin; $t += 30 * 60) {
                    $hora_str = date('H:i', $t);
                    if (!in_array($hora_str, $ocupadas)) {
                        $resultado[$dia][] = ['hour' => $hora_str];
                    }
                }
            }

            mysqli_close($conn);

            return $this->success([
                'available_hours' => $resultado
            ]);
        } catch (\Exception $e) {
            return $this->error([
                'message' => 'Error al obtener las horas disponibles',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function esFechaHoraValida(string $fecha, string $hora): bool
    {
        $hoy = date('Y-m-d');
        $manana = date('Y-m-d', strtotime('+1 day'));

        $horasValidas = ['10:00', '10:30', '11:00', '11:30', '12:00', '12:30'];

        return in_array($fecha, [$hoy, $manana]) && in_array($hora, $horasValidas);
    }

    public function postBooking(Request $request): JsonResponse
    {
        $data = $request->only(['nombre', 'apellidos', 'telefono', 'email', 'fecha_hora_inicio']);

        // Validación básica
        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:200',
            'fecha_hora_inicio' => 'required|date_format:Y-m-d H:i',
        ]);

        if ($validator->fails()) {
            return $this->error([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ]);
        }

        $fechaHora = $data['fecha_hora_inicio'];
        $timestamp = strtotime($fechaHora);
        $fecha = date('Y-m-d', $timestamp);
        $hora = date('H:i', $timestamp);

        // Validar fecha (hoy o mañana) y hora válida
        if (!$this->esFechaHoraValida($fecha, $hora)) {
            return $this->error([
                'message' => 'La fecha y hora no son válidas o están fuera del horario permitido.'
            ]);
        }

        // Conexión
        $conn = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
        if (!$conn) {
            return $this->error([
                'message' => 'No se pudo conectar a la base de datos.'
            ]);
        }

        // Verificar si ya existe una reserva en ese horario
        $sqlCheck = "SELECT id FROM reservas WHERE fecha_hora_inicio = '$fechaHora' AND status = 1";
        $result = mysqli_query($conn, $sqlCheck);
        if (mysqli_num_rows($result) > 0) {
            mysqli_close($conn);
            return $this->error([
                'message' => 'Ya existe una reserva en esa hora.'
            ]);
        }

        // Insertar reserva
        $nombre = mysqli_real_escape_string($conn, $data['nombre']);
        $apellidos = mysqli_real_escape_string($conn, $data['apellidos']);
        $telefono = mysqli_real_escape_string($conn, $data['telefono']);
        $email = mysqli_real_escape_string($conn, $data['email']);

        $sqlInsert = "INSERT INTO reservas (nombre, apellidos, telefono, email, fecha_hora_inicio, estado, status)
                    VALUES ('$nombre', '$apellidos', '$telefono', '$email', '$fechaHora', 1, 1)";

        if (mysqli_query($conn, $sqlInsert)) {
            mysqli_close($conn);
            return $this->success([
                'message' => 'Reserva registrada con éxito'
            ]);
        } else {
            mysqli_close($conn);
            return $this->error([
                'message' => 'Error al guardar la reserva',
                'error' => mysqli_error($conn)
            ]);
        }

    }
}