<?php
class ErrorControlador {
    public function error404() {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>Lo sentimos, la página que buscas no existe.</p>";
    }
}

?>