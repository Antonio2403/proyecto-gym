
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body text-center p-5">
                        <h1 class="card-title display-4 text-primary mb-4">Bienvenido a GymPro</h1>
                        <p class="card-text lead">Tu gimnasio ideal para gestionar miembros, clases y mucho más. ¡Empieza tu transformación hoy!</p>
                        <a href="/proyecto-gym/usuario/actividades" class="btn btn-primary btn-lg mt-3">Ver Clases</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">¡Éxito!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="successMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            
            if (success) {
                document.getElementById('successMessage').textContent = decodeURIComponent(success);
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            }
        });
    </script>
