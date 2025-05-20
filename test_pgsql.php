<?php
if (function_exists('pg_connect')) {
    echo "✅ PostgreSQL está habilitado.";
} else {
    echo "❌ PostgreSQL NO está habilitado.";
}
?>
