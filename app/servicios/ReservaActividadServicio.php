<?php

declare(strict_types=1);

namespace Spartum\Servicios;

final class ReservaActividadServicio
{
    /**
     * @return array{estado: 'error'|'info'|'success', mensaje: string, ruta: string, parametros?: array<string, scalar>}
     */
    public function reservar(int $usuarioId, int $actividadId, string $fechaSesion): array
    {
        if ($usuarioId <= 0) {
            return $this->resultado('error', 'Debes iniciar sesión', '/login');
        }
        if ($actividadId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSesion)) {
            return $this->resultado('error', 'Datos de reserva no válidos', '/usuario/actividades');
        }
        if (!\ClienteSubscripcion::tieneSuscripcionActivaPorUsuarioId($usuarioId)) {
            return $this->resultado(
                'error',
                'Necesitas un plan activo y pago al día para reservar. Contrata o renueva en la sección de planes.',
                '/usuario/actividades'
            );
        }

        $cliente = \Cliente::perfilReservaPorUsuarioId($usuarioId);
        if ($cliente === null) {
            return $this->resultado('error', 'No se ha encontrado tu perfil de cliente.', '/usuario/actividades');
        }

        $clienteId = (int) $cliente['cliente_id'];
        if (!\Actividad::fechaEsSesionValida($actividadId, $fechaSesion)) {
            return $this->resultado('error', 'La fecha no corresponde a esta actividad', '/usuario/actividades');
        }

        $actividad = \Actividad::obtenerPorId($actividadId);
        if (!$actividad) {
            return $this->resultado('error', 'Actividad no encontrada', '/usuario/actividades');
        }
        if (\Actividad::sesionCanceladaPorMinimo($actividadId, $fechaSesion)) {
            return $this->resultado(
                'error',
                'Esta clase se ha cancelado porque no había al menos 5 personas apuntadas.',
                '/usuario/actividades'
            );
        }
        if (!\Actividad::sesionPermiteInscripcion($actividad, $fechaSesion)) {
            return $this->resultado(
                'error',
                'No puedes reservar sesiones pasadas ni clases que ya han empezado. Elige un horario desde hoy en adelante.',
                '/usuario/actividades'
            );
        }
        if (!\ClienteSubscripcion::puedeNuevaReservaEsaSemana($clienteId, $fechaSesion)) {
            $cupo = \ClienteSubscripcion::cupoReservasSemana($clienteId, $fechaSesion);

            return $this->resultado(
                'error',
                sprintf(
                    'Has superado el máximo de inscripciones posibles esta semana con tu plan (%d de %d reservas).',
                    $cupo['usado'],
                    $cupo['max_semana']
                ),
                '/usuario/actividades'
            );
        }
        if (\Inscripcion::yaInscritoEnSesion($clienteId, $actividadId, $fechaSesion)) {
            return $this->resultado('info', 'Ya estás inscrito en esta sesión', '/usuario/actividades');
        }

        $plazas = max(1, (int) ($actividad['plazas'] ?? 20));
        if (\Inscripcion::contarInscritosSesion($actividadId, $fechaSesion) >= $plazas) {
            return $this->resultado('error', 'Esta sesión ya está llena', '/usuario/actividades');
        }
        if (!\Inscripcion::inscribir($clienteId, $actividadId, $fechaSesion)) {
            return $this->resultado('error', 'No se pudo completar la reserva.', '/usuario/actividades');
        }

        $this->enviarConfirmacion((string) $cliente['email']);

        return [
            'estado' => 'success',
            'mensaje' => 'Reserva realizada. Aquí aparecerán los comentarios de esta sesión; podrás dejar la tuya cuando termine.',
            'ruta' => '/usuario/actividades/sesion/comentarios',
            'parametros' => [
                'actividad_id' => $actividadId,
                'fecha' => $fechaSesion,
                'orden' => 'desc',
            ],
        ];
    }

    /**
     * @return array{estado: 'error'|'success', mensaje: string, ruta: string}
     */
    public function cancelar(int $usuarioId, int $inscripcionId): array
    {
        $ruta = '/usuario/inscripciones/mis-inscripciones';
        if ($usuarioId <= 0) {
            return $this->resultado('error', 'Debes iniciar sesión', '/login');
        }
        if (!\ClienteSubscripcion::tieneSuscripcionActivaPorUsuarioId($usuarioId)) {
            return $this->resultado(
                'error',
                'Necesitas un plan activo para gestionar reservas. Contrata o renueva en Planes.',
                $ruta
            );
        }
        if ($inscripcionId <= 0 || !\Inscripcion::cancelarParaUsuario($inscripcionId, $usuarioId)) {
            return $this->resultado('error', 'No se pudo cancelar la reserva.', $ruta);
        }

        return $this->resultado('success', 'Reserva cancelada correctamente.', $ruta);
    }

    /**
     * @return array{estado: 'error'|'info'|'success', mensaje: string, ruta: string}
     */
    private function resultado(string $estado, string $mensaje, string $ruta): array
    {
        return ['estado' => $estado, 'mensaje' => $mensaje, 'ruta' => $ruta];
    }

    private function enviarConfirmacion(string $email): void
    {
        if ($email === '') {
            return;
        }

        require_once dirname(__DIR__, 2) . '/core/helpers/mail_smtp.php';
        $error = null;
        if (!\gp_mail_send(
            $email,
            'Confirmación de reserva',
            '<p>Has reservado tu plaza correctamente.</p>',
            'Has reservado tu plaza correctamente.',
            $error
        )) {
            error_log('[Spartum reserva] correo no enviado: ' . (string) $error);
        }
    }
}
