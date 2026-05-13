<?php
require_once dirname(__DIR__, 4) . '/core/helpers/horario_centro.php';
?>
<script>window.GP_HORARIO_CENTRO = <?= json_encode(gp_horario_centro_json(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
